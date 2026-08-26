<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Models\Employe;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CongesController extends Controller
{
    public function index(Request $request)
    {
        $query = Conge::with('employe')->where('tenant_id', current_tenant_id());

        if ($request->filled('q')) {
            $recherche = $request->q;

            $query->whereHas('employe', function ($q) use ($recherche) {
                $q->where('nom', 'like', $recherche . '%')
                  ->orWhere('prenom', 'like', $recherche . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type_conge', $request->type);
        }

        if ($request->filled('mois')) {
            $query->whereMonth('date_debut', $request->mois);
        }

        if ($request->filled('statut')) {
            $aujourdhui = Carbon::today();

            switch ($request->statut) {
                case 'a_venir':
                    $query->whereDate('date_debut', '>', $aujourdhui);
                    break;

                case 'en_cours':
                    $query->whereDate('date_debut', '<=', $aujourdhui)
                          ->whereDate('date_fin', '>=', $aujourdhui);
                    break;

                case 'termine':
                    $query->whereDate('date_fin', '<', $aujourdhui);
                    break;
            }
        }

        $conges = $query
            ->latest('date_creation')
            ->paginate(15)
            ->withQueryString();

        $totalConges = Conge::where('tenant_id', current_tenant_id())->count();

        $aujourdhui = Carbon::today();

        $enCongeAujourdhui = Conge::where('tenant_id', current_tenant_id())
            ->whereDate('date_debut', '<=', $aujourdhui)
            ->whereDate('date_fin', '>=', $aujourdhui)
            ->distinct('employe_id')
            ->count('employe_id');

        $congesDemain = Conge::where('tenant_id', current_tenant_id())
            ->whereDate(
                'date_debut',
                $aujourdhui->copy()->addDay()
            )
            ->distinct('employe_id')
            ->count('employe_id');

        $debutSemaine = $aujourdhui->copy()->startOfWeek();
        $finSemaine = $aujourdhui->copy()->endOfWeek();

        $congesCetteSemaine = Conge::where('tenant_id', current_tenant_id())
            ->where(function ($q) use ($debutSemaine, $finSemaine) {
            $q->whereBetween('date_debut', [$debutSemaine, $finSemaine])
              ->orWhereBetween('date_fin', [$debutSemaine, $finSemaine]);
        })
            ->distinct('employe_id')
            ->count('employe_id');

        return view(
            'employes.conges.liste',
            compact(
                'conges',
                'totalConges',
                'enCongeAujourdhui',
                'congesDemain',
                'congesCetteSemaine'
            )
        );
    }

    public function create()
    {
        $employes = Employe::where('tenant_id', current_tenant_id())
            ->whereHas('contratActif')
            ->with([
                'conges',
                'contratActif',
            ])
            ->orderBy('nom')
            ->get();

        return view('employes.conges.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employe,id',
            'type_conge' => 'required|in:paye,sans_solde,maladie',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $employe = Employe::with('contratActif')
            ->findOrFail($validated['employe_id']);

        $contrat = $employe->contratActif;

        if (!$contrat) {
            return back()
                ->withInput()
                ->withErrors([
                    'employe_id' =>
                        "{$employe->nom_complet} n'a pas de contrat actif. Impossible d'enregistrer un congé.",
                ]);
        }

        $dateDebut = Carbon::parse($validated['date_debut']);
        $dateFin = Carbon::parse($validated['date_fin']);

        if ($dateDebut->lt(Carbon::parse($contrat->date_debut))) {
            return back()
                ->withInput()
                ->withErrors([
                    'date_debut' =>
                        'La date de début du congé est avant le début du contrat.',
                ]);
        }

        if ($contrat->date_fin && $dateFin->gt(Carbon::parse($contrat->date_fin))) {
            return back()
                ->withInput()
                ->withErrors([
                    'date_fin' =>
                        'La date de fin du congé dépasse la fin du contrat.',
                ]);
        }

        $congeExiste = Conge::where('employe_id', $employe->id)
            ->where(function ($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                      ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                      ->orWhere(function ($q) use ($dateDebut, $dateFin) {
                          $q->where('date_debut', '<=', $dateDebut)
                            ->where('date_fin', '>=', $dateFin);
                      });
            })
            ->exists();

        if ($congeExiste) {
            return back()
                ->withInput()
                ->withErrors([
                    'date_debut' =>
                        'Cet employé possède déjà un congé durant cette période.',
                ]);
        }

        if ($validated['type_conge'] === 'paye') {
            $joursDemandes = $dateDebut->diffInDays($dateFin) + 1;

            /*
             * Le solde appartient maintenant au contrat.
             * Le calcul est :
             * jours_conges du contrat - congés payés déjà pris
             * pendant ce contrat.
             */
            $joursUtilises = Conge::where('employe_id', $employe->id)
                ->where('type_conge', 'paye')
                ->where(function ($query) use ($contrat) {
                    /*
                     * On filtre par période plutôt que par contrat_id pour
                     * rester compatible avec d'éventuels congés créés avant
                     * ce correctif (qui pourraient ne pas avoir contrat_id
                     * renseigné). Pour les nouveaux congés, contrat_id est
                     * désormais bien enregistré (voir plus bas).
                     */
                    $query->whereDate('date_debut', '>=', $contrat->date_debut);

                    if ($contrat->date_fin) {
                        $query->whereDate('date_fin', '<=', $contrat->date_fin);
                    }
                })
                ->get()
                ->sum(function ($conge) {
                    return Carbon::parse($conge->date_debut)
                        ->diffInDays(Carbon::parse($conge->date_fin)) + 1;
                });

            $solde = max(0, (int) $contrat->nbreJourCongeAqcuise - $joursUtilises);

            if ($joursDemandes > $solde) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'date_fin' =>
                            "Solde insuffisant : {$employe->nom_complet} possède seulement {$solde} jour(s) disponible(s).",
                    ]);
            }
        }

        $validated['tenant_id'] = current_tenant_id();
        $validated['contrat_id'] = $contrat->id;

        if ($request->hasFile('justificatif')) {
            // Disque 'local' (storage/app/) : privé, non accessible directement
            // via une URL publique. Rangé par tenant puis par employé pour
            // éviter un dossier fourre-tout et faciliter le nettoyage/export.
            $validated['justificatif'] = $request
                ->file('justificatif')
                ->store(
                    "justificatifs/{$validated['tenant_id']}/{$employe->id}",
                    'local'
                );
        }

        $conge = Conge::create($validated);

        // Notifie l'utilisateur qui enregistre le congé (RH/admin).
        // NB : aujourd'hui la notification ne part que vers la personne qui
        // fait l'action (current_utilisateur_id() par défaut, voir
        // NotificationService::employeEnConge). S'il faut prévenir TOUS les
        // admins RH du tenant plutôt qu'une seule personne, il faudra
        // boucler ici sur la liste des admins et appeler la méthode pour
        // chacun d'eux.
        NotificationService::employeEnConge(
            $employe,
            $conge->date_debut->format('d/m/Y'),
            $conge->date_fin->format('d/m/Y')
        );

        return redirect()
            ->route('employes.conges.index')
            ->with('success', 'Congé ajouté avec succès.');
    }

    /**
     * Télécharge le justificatif d'un congé.
     *
     * Le fichier étant sur le disque privé 'local', il n'est pas accessible
     * via une URL directe : on vérifie que le congé appartient bien au
     * tenant courant avant de servir le fichier.
     */
    public function telechargerJustificatif(Conge $conge)
    {
        abort_unless($conge->tenant_id === current_tenant_id(), 403);

        if (!$conge->justificatif || !Storage::disk('local')->exists($conge->justificatif)) {
            abort(404, "Aucun justificatif trouvé pour ce congé.");
        }

        // On utilise response()->download() plutôt que
        // Storage::disk('local')->download() : cette dernière n'existe que
        // si le disque 'local' est explicitement défini avec le driver
        // 'local' dans config/filesystems.php. response()->download() avec
        // le chemin absolu fonctionne quelle que soit la configuration.
        return response()->download(
            Storage::disk('local')->path($conge->justificatif)
        );
    }
}

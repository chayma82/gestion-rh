<?php

namespace App\Http\Controllers;

use App\Models\AvanceSalaire;
use App\Models\Prime;
use App\Models\Salaire;
use App\Models\Employe;
use App\Models\ParametrePaie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class SalaireController extends Controller
{
    /**
     * Calcule la dernière période (Y-m) autorisée à être visible/exportée,
     * en fonction du "jour de paiement" configuré pour ce tenant.
     *
     * Règle : le mois en cours n'est débloqué que si on a atteint ou
     * dépassé son jour de paiement ; sinon seul le mois précédent (et les
     * plus anciens) sont autorisés. Centralisé ici pour que index() et
     * exporterPdf() appliquent exactement la même règle — un export ne
     * doit jamais donner accès à une période pas encore affichée dans le
     * tableau.
     */
    protected function periodeCutoff(int $tenantId): string
    {
        $parametrePaie = ParametrePaie::where('tenant_id', $tenantId)->first();
        $jourPaiement  = $parametrePaie->jour_paiement ?? 1;

        $aujourdhui = Carbon::today();

        return $aujourdhui->day >= $jourPaiement
            ? $aujourdhui->format('Y-m')
            : $aujourdhui->copy()->subMonthNoOverflow()->format('Y-m');
    }

    public function index(Request $request)
    {
        $tenantId = current_tenant_id();

        $parametrePaie = ParametrePaie::where('tenant_id', $tenantId)->first();
        $jourPaiement  = $parametrePaie->jour_paiement ?? 1;

        /*
         * Une fiche de salaire ne doit apparaître dans le tableau qu'à
         * partir du "jour de paiement" configuré du mois concerné — même
         * si elle existe déjà en base (créée en avance dès qu'une prime
         * ou une avance datée sur ce mois a été ajoutée). Sans ce filtre,
         * ajouter une prime avec une date de septembre faisait apparaître
         * tout de suite un groupe "Septembre 2026" dans le tableau, avant
         * même que le mois d'août ne soit terminé.
         *
         * Règle : la période courante (mois en cours) n'est visible que
         * si on a atteint ou dépassé le jour de paiement configuré ce
         * mois-ci. Sinon, seule les périodes antérieures sont visibles.
         * Les périodes futures (au-delà du mois en cours) restent
         * toujours masquées, quel que soit le jour du mois.
         */
        $periodeCutoff = $this->periodeCutoff($tenantId);

        $query = Salaire::with(['employe', 'contrat'])
            ->where('tenant_id', $tenantId)
            ->where('periode', '<=', $periodeCutoff);

        if ($request->filled('q')) {
            $recherche = $request->q;

            $query->whereHas('employe', function ($q) use ($recherche) {
                $q->where('nom', 'like', '%' . $recherche . '%')
                  ->orWhere('prenom', 'like', '%' . $recherche . '%');
            });
        }

        if ($request->filled('mois')) {
            $query->where('periode', 'like', '%-' . $request->mois);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        /*
         * Regroupement par mois (periode) plutôt que liste plate paginée :
         * une pagination classique (paginate(15)) coupait les mois n'importe
         * où au milieu d'une page, ce qui rendait tout regroupement visuel
         * incohérent d'une page à l'autre. On récupère donc tout ce qui
         * correspond aux filtres, trié du mois le plus récent au plus
         * ancien, puis on groupe en mémoire.
         *
         * NB : si le volume de données grossit beaucoup (des années
         * d'historique avec beaucoup d'employés), il faudra remplacer ceci
         * par une pagination au niveau des MOIS plutôt que des lignes
         * (ex: ne charger que les 12 derniers mois par défaut, avec un
         * filtre "voir plus ancien").
         */
        $salaires = $query->latest('periode')->get();

        $salairesParMois = $salaires
            ->groupBy('periode')
            ->sortKeysDesc()
            ->map(function ($salairesDuMois) {
                // Tri par nom d'employé à l'intérieur de chaque mois, pour
                // un affichage stable (latest('periode') ne trie que par
                // periode, pas par employé).
                return $salairesDuMois->sortBy(fn ($s) => $s->employe->nom ?? '');
            });

        $totalsalaire = Salaire::where('tenant_id', $tenantId)
            ->where('periode', '<=', $periodeCutoff)
            ->count();

        $masseSalariale = Salaire::query()
            ->where('tenant_id', $tenantId)
            ->where('periode', '<=', $periodeCutoff)
            ->when(
                $request->filled('mois'),
                fn ($q) => $q->where('periode', 'like', '%-' . $request->mois)
            )
            ->when(
                $request->filled('statut'),
                fn ($q) => $q->where('statut', $request->statut)
            )
            ->selectRaw(
                'COALESCE(SUM(salaire_brut + total_primes - total_avances), 0) as total'
            )
            ->value('total');

        return view(
            'employes.salaires.liste',
            compact(
                'totalsalaire',
                'salairesParMois',
                'masseSalariale',
                'parametrePaie'
            )
        );
    }

    public function create()
    {
        $employes = Employe::where('tenant_id', current_tenant_id())
            ->whereHas('contrats', function ($query) {
                $query->where('statut', 'actif');
            })
            ->with('contratActif')
            ->orderBy('nom')
            ->get();

        return view('employes.salaires.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employe,id',

            'nouveau_salaire' => 'nullable|numeric|min:0',

            'avance' => 'nullable|numeric|min:0',
            'date_avance' => 'nullable|date|required_with:avance',
            'motif_avance' => 'nullable|string|max:255',

            'montant_prime' => 'nullable|numeric|min:0',
            'date_prime' => 'nullable|date|required_with:montant_prime',
            'motif_prime' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            // Correctif : on force le tenant_id courant lors du lookup,
            // la règle de validation 'exists' ne vérifiant pas le tenant.
            // Sans ça, un employe_id d'un autre tenant pouvait être forgé
            // dans la requête.
            $employe = Employe::where('tenant_id', current_tenant_id())
                ->findOrFail($validated['employe_id']);

            $contrat = $employe->contrats()
                ->where('statut', 'actif')
                ->latest('date_debut')
                ->first();

            if (!$contrat) {
                throw ValidationException::withMessages([
                    'employe_id' =>
                        'Cet employé n\'a pas de contrat actif.',
                ]);
            }

            /*
             * Le salaire de référence est dans CONTRAT.salaire_base.
             * Le champ nouveau_salaire permet de le modifier depuis
             * l'écran salaire.
             */
            if (
                array_key_exists('nouveau_salaire', $validated) &&
                $validated['nouveau_salaire'] !== null
            ) {
                $contrat->update([
                    'salaire_base' => $validated['nouveau_salaire'],
                ]);

                // On recharge le modèle pour être sûr d'avoir la
                // nouvelle valeur en mémoire pour la suite de la
                // transaction (firstOrNew ci-dessous, etc.).
                $contrat->refresh();
            }

            $periode = now()->format('Y-m');

            /*
             * Le salaire mensuel reste dans la table salaire pour conserver
             * l'historique de paie.
             *
             * La clé logique est :
             * contrat_id + periode
             */
            $salaire = Salaire::firstOrNew([
                'contrat_id' => $contrat->id,
                'periode' => $periode,
            ]);

            if (!$salaire->exists) {
                $salaire->tenant_id = current_tenant_id();
                $salaire->employe_id = $employe->id;
                $salaire->salaire_brut = $contrat->salaire_base ?? 0;
                $salaire->total_primes = 0;
                $salaire->total_avances = 0;
                $salaire->statut = 'en_attente';
            } else {
                /*
                 * Fiche déjà existante pour ce mois : si le salaire du
                 * contrat vient d'être modifié via nouveau_salaire, on
                 * répercute la nouvelle valeur sur le mois en cours.
                 * Sinon, on garde le salaire déjà enregistré.
                 */
                if (
                    array_key_exists('nouveau_salaire', $validated) &&
                    $validated['nouveau_salaire'] !== null
                ) {
                    $salaire->salaire_brut = $contrat->salaire_base;
                }
            }

            if (!empty($validated['avance'])) {
                $nouveauTotalAvances =
                    $salaire->total_avances + $validated['avance'];

                if ($nouveauTotalAvances > $salaire->salaire_brut) {
                    throw ValidationException::withMessages([
                        'avance' =>
                            'Le total des avances ne peut pas dépasser le salaire brut.',
                    ]);
                }

                AvanceSalaire::create([
                    'tenant_id' => current_tenant_id(),
                    'employe_id' => $employe->id,
                    'contrat_id' => $contrat->id,
                    'montant' => $validated['avance'],
                    'date_avance' => $validated['date_avance'],
                    'motif' => $validated['motif_avance'] ?? null,
                ]);

                $salaire->total_avances += $validated['avance'];
            }

            if (!empty($validated['montant_prime'])) {
                Prime::create([
                    'tenant_id' => current_tenant_id(),
                    'employe_id' => $employe->id,
                    'contrat_id' => $contrat->id,
                    'montant' => $validated['montant_prime'],
                    'date_prime' => $validated['date_prime'],
                    'motif' => $validated['motif_prime'] ?? null,
                ]);

                $salaire->total_primes += $validated['montant_prime'];
            }

            $salaire->save();
        });

        return redirect()
            ->route('employes.salaires.index')
            ->with('success', 'Salaire mis à jour avec succès.');
    }

    public function payer(Salaire $salaire)
    {
        // Correctif : le route-model-binding ne filtre pas par tenant,
        // on vérifie donc explicitement l'appartenance du salaire.
        abort_unless($salaire->tenant_id === current_tenant_id(), 403);

        $salaire->update([
            'statut' => 'paye',
            'date_paiement' => now(),
        ]);

        return back()->with(
            'success',
            'Salaire marqué comme payé.'
        );
    }

    public function annulerPaiement(Salaire $salaire)
    {
        // Correctif : le route-model-binding ne filtre pas par tenant,
        // on vérifie donc explicitement l'appartenance du salaire.
        abort_unless($salaire->tenant_id === current_tenant_id(), 403);

        $salaire->update([
            'statut' => 'en_attente',
            'date_paiement' => null,
        ]);

        return back()->with(
            'success',
            'Paiement annulé, salaire repassé en attente.'
        );
    }

    public function payerTous(Request $request)
    {
        $tenantId = current_tenant_id();

        $periode = $request->input(
            'periode',
            now()->format('Y-m')
        );

        $nb = Salaire::where('tenant_id', $tenantId)
            ->where('periode', $periode)
            ->where('statut', 'en_attente')
            ->update([
                'statut' => 'paye',
                'date_paiement' => now(),
            ]);

        return back()->with(
            'success',
            "{$nb} salaire(s) marqué(s) comme payé(s) pour {$periode}."
        );
    }

    public function updateConfig(Request $request)
    {
        $request->validate([
            'jour_paiement' => 'required|integer|min:1|max:28',
        ]);

        ParametrePaie::updateOrCreate(
            ['tenant_id' => current_tenant_id()],
            ['jour_paiement' => $request->jour_paiement]
        );

        return back()->with(
            'success',
            'Jour de paiement mis à jour.'
        );
    }

    /**
     * Génère un PDF imprimable de la fiche de salaires d'une période.
     *
     * Par défaut, exporte la période actuellement débloquée (le mois
     * affiché en haut du tableau). On peut aussi demander explicitement
     * une période passée via ?periode=2026-08, mais jamais une période
     * pas encore débloquée par le jour de paiement (même règle que
     * index()) : le lien "Exporter" du tableau bascule donc tout seul
     * sur le mois suivant dès que son jour de paiement arrive, sans
     * aucun code supplémentaire à changer.
     */
    public function exporterPdf(Request $request)
    {
        $tenantId = current_tenant_id();
        $periodeCutoff = $this->periodeCutoff($tenantId);

        $periode = $request->filled('periode')
            ? $request->periode
            : $periodeCutoff;

        abort_if(
            $periode > $periodeCutoff,
            403,
            "Cette période n'est pas encore disponible."
        );

        $salaires = Salaire::with(['employe', 'contrat'])
            ->where('tenant_id', $tenantId)
            ->where('periode', $periode)
            ->get()
            ->sortBy(fn ($s) => $s->employe->nom_complet ?? $s->employe->nom ?? '')
            ->values();

        if ($salaires->isEmpty()) {
            return back()->withErrors([
                'periode' => "Aucun salaire trouvé pour la période {$periode}.",
            ]);
        }

        $masseSalariale = $salaires->sum(
            fn ($s) => (float) $s->salaire_brut + (float) $s->total_primes - (float) $s->total_avances
        );

        $nbPayes = $salaires->where('statut', 'paye')->count();

        $pdf = Pdf::loadView('employes.salaires.pdf', [
            'salaires'       => $salaires,
            'periode'        => $periode,
            'masseSalariale' => $masseSalariale,
            'nbPayes'        => $nbPayes,
            'nbTotal'        => $salaires->count(),
        ])->setPaper('a4', 'landscape');

        // stream() ouvre le PDF dans le navigateur : l'utilisateur peut
        // alors l'enregistrer OU l'imprimer directement depuis la
        // visionneuse PDF native du navigateur (icônes imprimante /
        // télécharger). Remplacer par ->download($nom) pour forcer le
        // téléchargement immédiat sans aperçu.
        return $pdf->stream("salaires-{$periode}.pdf");
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Employe;
use App\Models\Departement;
use App\Models\Poste;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ContratController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrat::with('employe')->where('tenant_id', current_tenant_id());

        if ($request->filled('q')) {
            $recherche = $request->q;

            $query->where(function ($q) use ($recherche) {
                $q->where('numcontrat', 'like', $recherche . '%')
                  ->orWhereHas('employe', function ($q) use ($recherche) {
                      $q->where('nom', 'like', $recherche . '%')
                        ->orWhere('prenom', 'like', $recherche . '%');
                  });
            });
        }

        if ($request->filled('mois')) {
            $query->whereMonth('date_debut', $request->mois);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $contrats = $query->latest()->paginate(15)->withQueryString();

        $totalcontrat = Contrat::where('tenant_id', current_tenant_id())->count();
        $totalcontratActif = Contrat::where('tenant_id', current_tenant_id())->where('statut', 'actif')->count();
        $totalcontratExpire = Contrat::where('tenant_id', current_tenant_id())->where('statut', 'expire')->count();

        return view(
            'employes.contrats.liste',
            compact(
                'contrats',
                'totalcontrat',
                'totalcontratActif',
                'totalcontratExpire'
            )
        );
    }

    public function create()
    {
        $employes = Employe::where('tenant_id', current_tenant_id())
            ->where('statutEmploye', '!=', 'archive')
            ->whereDoesntHave('contrats', function ($query) {
                $query->whereIn('statut', ['actif', 'a_venir']);
            })
            ->orderBy('nom')
            ->get();

        $departements = Departement::where('tenant_id', current_tenant_id())
            ->orderBy('libelle')
            ->get();

        $postes = Poste::where('tenant_id', current_tenant_id())
            ->orderBy('libelle')
            ->get();

        return view(
            'employes.contrats.create',
            compact('employes', 'departements', 'postes')
        );
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'employe_id' => 'required|exists:employe,id',
        'departement_id' => 'required|exists:departement,id',
        'poste_id' => 'required|exists:poste,id',

        'jours_conges' => 'required|integer|min:0',
        'type_contrat' => [
            'required',
            Rule::in([
                'CDI',
                'CDD',
                'Stage',
                'Consultant',
                'Freelance',
                'Interimaire',
            ]),
        ],

        'date_debut' => 'required|date',
        'date_fin' => 'nullable|date|after_or_equal:date_debut',
        'recruteur' => 'required|string|max:255',
        'salaire' => 'required|numeric|min:0',
    ]);

    $employeCheck = Employe::findOrFail($validated['employe_id']);

    if ($employeCheck->statutEmploye === 'archive') {
        return back()
            ->withErrors([
                'employe_id' => 'Impossible de créer un contrat pour un employé archivé.',
            ])
            ->withInput();
    }

    // Empêcher deux contrats actifs/à venir simultanément.
    $contratExiste = Contrat::where('employe_id', $employeCheck->id)
        ->whereIn('statut', ['actif', 'a_venir'])
        ->exists();

    if ($contratExiste) {
        return back()
            ->withErrors([
                'employe_id' => 'Cet employé possède déjà un contrat actif ou à venir.',
            ])
            ->withInput();
    }

    DB::transaction(function () use ($validated) {
        $employe = Employe::findOrFail($validated['employe_id']);
        $departement = Departement::findOrFail($validated['departement_id']);

        $dateDebut = Carbon::parse($validated['date_debut'])->startOfDay();

        $statutContrat = $dateDebut->isFuture()
            ? 'a_venir'
            : 'actif';

        $statutEmploye = $dateDebut->isFuture()
            ? 'attente_prise_poste'
            : 'actif';

        /*
         * Toutes les données liées à l'emploi sont maintenant dans CONTRAT.
         * Elles ne sont plus copiées dans EMPLOYE.
         *
         * 'numcontrat' est NOT NULL sans défaut en base, mais on a besoin
         * de l'id auto-incrémenté du contrat pour le construire.
         * On insère donc une valeur temporaire, puis on la remplace juste après.
         */
        $contrat = Contrat::create([
            'tenant_id' => current_tenant_id(),
            'employe_id' => $employe->id,
            'departement_id' => $departement->id,
            'poste_id' => $validated['poste_id'],
            'typeContrat' => $validated['type_contrat'],
            'date_debut' => $validated['date_debut'],
            'date_fin' => $validated['date_fin'] ?? null,
            'nbreJourCongeAqcuise' => $validated['jours_conges'],
            'salaire_base' => $validated['salaire'],
            'statut' => $statutContrat,
            'recreteur' => $validated['recruteur'],
            'numcontrat' => 'TEMP-' . uniqid(),
        ]);

        $annee = $dateDebut->year;

        $numcontrat = "CNT-{$departement->code}-{$employe->id}-{$contrat->id}-{$annee}";

        $contrat->update([
            'numcontrat' => $numcontrat,
        ]);

        $employe->update([
            'statutEmploye' => $statutEmploye,
        ]);
    });

    return redirect()
        ->route('employes.contrats.index')
        ->with('success', 'Contrat ajouté avec succès.');
}

    public function edit(Contrat $contrat)
    {
        if ($contrat->statut === 'resilie') {
            return redirect()
                ->route('employes.contrats.index')
                ->withErrors([
                    'contrat' => 'Un contrat résilié ne peut plus être modifié.',
                ]);
        }

        $contrat->load(['employe', 'departement', 'poste']);

        $departements = Departement::where('tenant_id', current_tenant_id())
            ->orderBy('libelle')
            ->get();

        $postes = Poste::where('tenant_id', current_tenant_id())
            ->orderBy('libelle')
            ->get();

        return view(
            'employes.contrats.edit',
            compact('contrat', 'departements', 'postes')
        );
    }

   public function update(Request $request, Contrat $contrat)
{
    // Un contrat résilié ne peut plus être modifié
    if (in_array($contrat->statut, ['resilie', 'expire']))
        {
        return redirect()
            ->route('employes.contrats.index')
            ->withErrors([
                'contrat' => 'Un contrat résilié ne peut plus être modifié.',
            ]);
    }

    $validated = $request->validate([
        'type_contrat' => [
            'required',
            Rule::in([
                'CDI',
                'CDD',
                'Stage',
                'Consultant',
                'Freelance',
                'Interimaire',
            ]),
        ],

        'date_fin' => [
            'nullable',
            'date',
            function ($attribute, $value, $fail) use ($contrat) {

                // Si une date de fin est renseignée
                if ($value && $contrat->date_debut) {

                    $dateFin = Carbon::parse($value);
                    $dateDebut = Carbon::parse($contrat->date_debut);

                    // Date fin doit être STRICTEMENT après date début
                    if ($dateFin->lessThanOrEqualTo($dateDebut)) {
                        $fail(
                            'La date de fin doit être strictement postérieure à la date de début du contrat (' .
                            $dateDebut->format('d/m/Y') .
                            ').'
                        );
                    }
                }
            },
        ],

        'jours_conges' => [
            'required',
            'integer',
            'min:0',
        ],

        'salaire' => [
            'required',
            'numeric',
            'min:0',
        ],

        'recruteur' => [
            'required',
            'string',
            'max:255',
        ],
    ]);

    DB::transaction(function () use ($validated, $contrat) {

        $contrat->update([
            'typeContrat'          => $validated['type_contrat'],
            'date_fin'             => $validated['date_fin'] ?? null,
            'nbreJourCongeAqcuise' => $validated['jours_conges'],
            'salaire_base'         => $validated['salaire'],
            'recreteur'            => $validated['recruteur'],
        ]);
    });

    return redirect()
        ->route('employes.contrats.index')
        ->with('success', 'Contrat mis à jour avec succès.');
}
    public function resilier(Request $request, Contrat $contrat)
    {
        if (!in_array($contrat->statut, ['actif', 'a_venir'])) {
            return back()->withErrors([
                'contrat' => 'Ce contrat ne peut pas être résilié.',
            ]);
        }

        $archiverEmploye = $request->boolean('archiver_employe');

        DB::transaction(function () use ($contrat, $archiverEmploye) {
            $contrat->update([
                'statut' => 'resilie',
                'date_fin' => Carbon::today(),
            ]);

            $employe = $contrat->employe;

            if ($employe) {
                $employe->update([
                    'statutEmploye' => $archiverEmploye
                        ? 'archive'
                        : 'fin_contrat',
                ]);
            }
        });

        return redirect()
            ->route('employes.contrats.index')
            ->with(
                'success',
                $archiverEmploye
                    ? 'Contrat résilié avec succès. L\'employé associé a été archivé.'
                    : 'Contrat résilié avec succès. L\'employé associé est maintenant en fin de contrat.'
            );
    }
}
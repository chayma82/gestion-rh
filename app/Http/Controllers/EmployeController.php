<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeController extends Controller
{
    /**
     * Liste des employés
     */
    public function index(Request $request)
    {
        

        $query = Employe::query()
            ->where('tenant_id', 1)
            ->where('statutEmploye', '!=', 'archive')
            ->with([
                'contrats' => function ($query) {
                    $query->latest('date_debut');
                },
                'contrats.departement',
                'contrats.poste',
            ]);

        /*
        |--------------------------------------------------------------------------
        | RECHERCHE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('q')) {

            $recherche = trim($request->q);

            $query->where(function ($q) use ($recherche) {

                $q->where('nom', 'like', $recherche . '%')
                    ->orWhere('prenom', 'like', $recherche . '%')
                    ->orWhere('matricule', 'like', $recherche . '%')
                    ->orWhere('cin_passeport', 'like', $recherche . '%');
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE STATUT EMPLOYÉ
        |--------------------------------------------------------------------------
        */

        if ($request->filled('statut')) {

            $query->where(
                'statutEmploye',
                $request->statut
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRE MOIS
        |--------------------------------------------------------------------------
        |
        | Le mois correspond au mois de début du contrat.
        |
        */

        if ($request->filled('mois')) {

            $query->whereHas('contrats', function ($q) use ($request) {

                $q->whereMonth(
                    'date_debut',
                    $request->mois
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        $employes = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | STATISTIQUES
        |--------------------------------------------------------------------------
        */

        // Tous les employés non archivés
        $totalemploye = Employe::where('tenant_id', 1)
            ->where('statutEmploye', '!=', 'archive')
            ->count();

        // Employés actifs
        $employesActifs = Employe::where('tenant_id', 1)
            ->where('statutEmploye', 'actif')
            ->count();

        // Employés actuellement en congé
        $employesConge = Employe::where('tenant_id', 1)
            ->where('statutEmploye', 'en_conge')
            ->count();

        return view(
            'employes.liste',
            compact(
                'employes',
                'totalemploye',
                'employesActifs',
                'employesConge'
            )
        );
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('employes.create');
    }

    /**
     * Enregistrer un employé
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'nom' => 'required|string|max:255',

            'prenom' => 'required|string|max:255',

            'sexe' => 'required|in:M,F',

            'date_naissance' => 'required|date',

            'lieu_naissance' => 'required|string|max:255',

            'nationalite' => 'required|string|max:255',

            'cin_passeport' => [
                'required',
                'string',
                'max:50',

                Rule::unique('employe')->where(
                    fn ($query) => $query->where('tenant_id', 1)
                ),
            ],

            'situation_familiale' => [
                'required',
                Rule::in([
                    'celibataire',
                    'marie',
                    'divorce',
                    'veuf'
                ]),
            ],

            'nb_enfants' => 'nullable|integer|min:0',

            'adresse' => 'required|string',

            'ville' => 'required|string|max:255',

            'code_postal' => 'required|string|max:20',

            'tel_perso' => 'required|string|max:30',

            'tel_pro' => 'nullable|string|max:30',

            'email_perso' => 'required|email|max:150',

            'email_pro' => 'nullable|email|max:150',

            'nom_contact_urgence' => 'required|string|max:150',

            'lien_parente' => 'required|string|max:50',

            'telephone_urgence' => 'required|string|max:30',

            'adresse_urgence' => 'required|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Nombre d'enfants
        |--------------------------------------------------------------------------
        */

        $validated['nb_enfants'] =
            (int) ($request->input('nb_enfants') ?? 0);

        /*
        |--------------------------------------------------------------------------
        | MATRICULE
        |--------------------------------------------------------------------------
        */

        $dernierEmploye = Employe::latest('id')->first();

        $numero = $dernierEmploye
            ? $dernierEmploye->id + 1
            : 1;

        $validated['matricule'] =
            'EMP-' . str_pad(
                $numero,
                4,
                '0',
                STR_PAD_LEFT
            );

        /*
        |--------------------------------------------------------------------------
        | TENANT / ENTREPRISE / UTILISATEUR
        |--------------------------------------------------------------------------
        */

        $validated['tenant_id'] = 1;

        $validated['entreprise_id'] = 1;

        $validated['utilisateur_creation_id'] = 1;

        /*
        |--------------------------------------------------------------------------
        | STATUT INITIAL
        |--------------------------------------------------------------------------
        |
        | Un nouvel employé n'a pas encore de contrat.
        |
        */

        $validated['statutEmploye'] = 'attente_prise_poste';

        Employe::create($validated);

        return redirect()
            ->route('employes.index')
            ->with(
                'success',
                'Employé ajouté avec succès.'
            );
    }

    /**
     * Informations employé
     */
    public function info(int $id)
    {
        $employe = Employe::with([
            'contrats' => fn ($query) =>
                $query->latest('date_debut'),

            'contrats.departement',
            'contrats.poste',
        ])->findOrFail($id);

        return view(
            'employes.info',
            compact('employe')
        );
    }

    /**
     * Modifier
     */
    public function edit($id)
    {
        $employe = Employe::findOrFail($id);

        return view(
            'employes.edit',
            compact('employe')
        );
    }

    /**
     * Mise à jour
     */
    public function update(Request $request, $id)
    {
        $employe = Employe::findOrFail($id);

        $validated = $request->validate([

            'situation_familiale' => [
                'required',
                Rule::in([
                    'celibataire',
                    'marie',
                    'divorce',
                    'veuf'
                ]),
            ],

            'nb_enfants' => 'nullable|integer|min:0',

            'adresse' => 'required|string',

            'ville' => 'required|string|max:255',

            'code_postal' => 'required|string|max:20',

            'tel_perso' => 'required|string|max:30',

            'tel_pro' => 'nullable|string|max:30',

            'email_perso' => 'required|email|max:150',

            'email_pro' => 'nullable|email|max:150',

            'nom_contact_urgence' => 'required|string|max:150',

            'lien_parente' => 'required|string|max:50',

            'telephone_urgence' => 'required|string|max:30',

            'adresse_urgence' => 'required|string',
        ]);

        /*
        |--------------------------------------------------------------------------
        | BDD : nb_enfants NOT NULL
        |--------------------------------------------------------------------------
        */

        $validated['nb_enfants'] =
            (int) ($request->input('nb_enfants') ?? 0);

        $employe->update($validated);

        return redirect()
            ->route(
                'employes.info',
                $employe->id
            )
            ->with(
                'success',
                'Employé mis à jour avec succès.'
            );
    }

    /**
     * ARCHIVER UN EMPLOYÉ
     */
    public function destroy(Employe $employe)
    {
        /*
        |--------------------------------------------------------------------------
        | Le statut est maintenant dans EMPLOYE
        |--------------------------------------------------------------------------
        */

        $employe->update([
            'statutEmploye' => 'archive',
        ]);

        /*
        |--------------------------------------------------------------------------
        | On résilie également les contrats actifs / à venir
        |--------------------------------------------------------------------------
        */

        $employe->contrats()
            ->whereIn(
                'statut',
                ['actif', 'a_venir']
            )
            ->update([
                'statut' => 'resilie',
                'date_fin' => now()->toDateString(),
            ]);

        return redirect()
            ->route('employes.index')
            ->with(
                'success',
                'Employé archivé avec succès.'
            );
    }

    /**
     * ARCHIVES
     */
    public function archives()
    {
        /*
        |--------------------------------------------------------------------------
        | Les archives sont déterminées par EMPLOYE.statutEmploye
        |--------------------------------------------------------------------------
        */

        $employes = Employe::where('tenant_id', 1)
            ->where(
                'statutEmploye',
                'archive'
            )
            ->with([
                'contrats' => function ($query) {
                    $query->latest('date_debut');
                },
                'contrats.departement',
                'contrats.poste',
            ])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view(
            'employes.archives',
            compact('employes')
        );
    }

    /**
     * Désarchiver
     */
    public function desarchiver(Employe $employe)
    {
        /*
        |--------------------------------------------------------------------------
        | On remet l'employé en attente d'un contrat
        |--------------------------------------------------------------------------
        */

        $employe->update([
            'statutEmploye' => 'attente_prise_poste',
        ]);

        return redirect()
            ->route(
                'employes.index',
                [
                    'employe_id' => $employe->id
                ]
            )
            ->with(
                'success',
                'Employé désarchivé. Créez maintenant son nouveau contrat.'
            );
    }
}

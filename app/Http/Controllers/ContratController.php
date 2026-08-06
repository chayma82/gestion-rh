<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Contrat;
use App\Models\Employe;
use App\Models\Salaire;
use App\Models\Departement;
use App\Models\Poste;

class ContratController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrat::with('employe');

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

        $contrats = $query->latest()->paginate(15)->withQueryString();

        $totalcontrat        = Contrat::count();
        $totalcontratActif   = Contrat::where('statut', 'actif')->count();
        $totalcontratExpire  = Contrat::where('statut', 'expire')->count();

        return view(
            'employes.contrats.liste',
            compact('contrats', 'totalcontrat', 'totalcontratActif', 'totalcontratExpire')
        );
    }

    public function create()
    {
        // On exclut les employés qui ont déjà un contrat actif ou à venir :
        // un employé ne peut avoir qu'un seul contrat "en cours de vie" à la fois.
        $employes = Employe::where('tenant_id', 1)
        ->where('statutEmploye', '!=', 'archive')
        ->whereDoesntHave('contrats', function ($query) {
            $query->whereIn('statut', ['actif', 'a_venir']);
        })
            ->orderBy('nom')
            ->get();

        $departements = Departement::where('tenant_id', 1)
            ->orderBy('libelle')
            ->get();

        $postes = Poste::where('tenant_id', 1)
            ->orderBy('libelle')
            ->get();

        return view('employes.contrats.create', compact('employes', 'departements', 'postes'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'employe_id'      => 'required|exists:employe,id',
            'departement_id'  => 'required|exists:departement,id',
            'poste_id'        => 'required|exists:poste,id',
            'date_embauche'   => 'required|date',
            'jours_conges'    => 'required|integer|min:0',
            'type_contrat'    => 'required|string',
            'date_debut'      => 'required|date',
            'date_fin'        => 'nullable|date|after_or_equal:date_debut',
            'recruteur'       => 'required|string|max:255',
            'salaire'         => 'required|numeric|min:0',
        ]);
        $employeCheck = Employe::findOrFail($validated['employe_id']);

    if ($employeCheck->statutEmploye === 'archive') {
        return back()
            ->withErrors(['employe_id' => 'Impossible de créer un contrat pour un employé archivé.'])
            ->withInput();
    }

        DB::transaction(function () use ($validated) {

            $employe     = Employe::findOrFail($validated['employe_id']);
            $departement = Departement::findOrFail($validated['departement_id']);
            $poste       = Poste::findOrFail($validated['poste_id']);

            $anneeEmbauche = Carbon::parse($validated['date_embauche'])->year;

            // Détermination du statut selon la date de début du contrat.
            // isFuture() compare à l'instant présent : une date_debut = aujourd'hui
            // n'est PAS future, donc le contrat démarre bien "actif" le jour même.
            $dateDebut = Carbon::parse($validated['date_debut'])->startOfDay();

            if ($dateDebut->isFuture()) {
                $statutContrat = 'a_venir';
                $statutEmploye = 'attente_prise_poste';
            } else {
                $statutContrat = 'actif';
                $statutEmploye = 'actif';
            }

            // Matricule employé (EMP-xxxx) jamais modifié ici — c'est un
            // identifiant stable, distinct du numéro de contrat.
            $employe->update([
                'departement_id'       => $departement->id,
                'poste_id'             => $poste->id,
                'date_embauche'        => $validated['date_embauche'],
                'nbreJourCongeAqcuise' => $validated['jours_conges'],
                'statutEmploye'        => $statutEmploye,
            ]);

            $contrat = Contrat::create([
                'tenant_id'   => 1,
                'employe_id'  => $validated['employe_id'],
                'typeContrat' => $validated['type_contrat'],
                'date_debut'  => $validated['date_debut'],
                'date_fin'    => $validated['date_fin'] ?? null,
                'statut'      => $statutContrat, // ← corrigé : utilise bien la variable calculée
                'recreteur'   => $validated['recruteur'],
            ]);

            // Numéro de contrat, distinct du matricule employé.
            $numcontrat = "CNT-{$departement->code}-{$employe->id}-{$contrat->id}-{$anneeEmbauche}";
            $contrat->update(['numcontrat' => $numcontrat]);

            Salaire::create([
                'tenant_id'      => 1,
                'employe_id'     => $validated['employe_id'],
                'contrat_id'     => $contrat->id,
                'periode'        => Carbon::parse($validated['date_debut'])->format('Y-m'),
                'salaire_brut'   => $validated['salaire'],
                'total_primes'   => 0,
                'total_avances'  => 0,
            ]);
        });

        return redirect()
            ->route('employes.contrats.index')
            ->with('success', 'Contrat ajouté avec succès.');
    }

    public function edit(Contrat $contrat)
    {
        // Un contrat déjà résilié appartient à l'historique : on ne le
        // modifie plus (même logique qu'un employé archivé côté Employe).
        if ($contrat->statut === 'resilie') {
            return redirect()
                ->route('employes.contrats.index')
                ->withErrors(['contrat' => 'Un contrat résilié ne peut plus être modifié.']);
        }

        $contrat->load('employe');

        return view('employes.contrats.edit', compact('contrat'));
    }

    public function update(Request $request, Contrat $contrat)
    {
        if ($contrat->statut === 'resilie') {
            return redirect()
                ->route('employes.contrats.index')
                ->withErrors(['contrat' => 'Un contrat résilié ne peut plus être modifié.']);
        }

        $validated = $request->validate([
            'type_contrat' => 'required|string',
            'date_fin'     => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($contrat) {
                    if ($value && $contrat->date_debut && Carbon::parse($value)->lt($contrat->date_debut)) {
                        $fail('La date de fin doit être postérieure ou égale à la date de début.');
                    }
                },
            ],
            'recruteur'    => 'required|string|max:255',
        ]);

        // Sécurité : employe_id, numcontrat, date_debut et statut sont
        // volontairement absents de $validated donc ne peuvent jamais être
        // modifiés ici, même si le formulaire est trafiqué côté client.
        // Ce sont des données figées à la création du contrat (au même
        // titre que nom/prenom/cin sur la fiche employé).
        $contrat->update([
            'typeContrat' => $validated['type_contrat'],
            'date_fin'    => $validated['date_fin'] ?? null,
            'recreteur'   => $validated['recruteur'],
        ]);

        return redirect()
            ->route('employes.contrats.index')
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    public function resilier(Request $request, Contrat $contrat)
    {
        // Vérifier que le contrat peut être résilié
        if (!in_array($contrat->statut, ['actif', 'a_venir'])) {

            return redirect()
                ->back()
                ->withErrors([
                    'contrat' => 'Ce contrat ne peut pas être résilié.'
                ]);
        }

        // Décidé côté écran par la 2e confirmation JS (voir liste.blade.php) :
        // l'utilisateur choisit s'il veut archiver l'employé tout de suite,
        // ou le laisser en "fin_contrat" pour l'archiver plus tard depuis
        // sa fiche. Cette information ne peut PAS être déduite automatiquement
        // (elle ne dit rien sur "démission" ou "suspension", voir la fiche
        // employé pour changer manuellement le motif si besoin).
        $archiverEmploye = $request->boolean('archiver_employe');

        DB::transaction(function () use ($contrat, $archiverEmploye) {

            // Date réelle de résiliation = aujourd'hui
            $contrat->update([
                'statut'   => 'resilie',
                'date_fin' => Carbon::today(),
            ]);

            $employe = $contrat->employe;

            if ($employe) {

                $employe->update([
                    'statutEmploye' => $archiverEmploye ? 'archive' : 'fin_contrat',
                ]);

            }

        });

        $message = $archiverEmploye
            ? 'Contrat résilié avec succès. L\'employé associé a été archivé.'
            : 'Contrat résilié avec succès. L\'employé associé est maintenant en fin de contrat.';

        return redirect()
            ->route('employes.contrats.index')
            ->with('success', $message);
    }
}

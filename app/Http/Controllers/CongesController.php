<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CongesController extends Controller
{
    public function index(Request $request)
{
    $query = Conge::with('employe');


    // Recherche
    if ($request->filled('q')) {

        $recherche = $request->q;

        $query->whereHas('employe', function ($q) use ($recherche) {

            $q->where('nom', 'like', $recherche.'%')
              ->orWhere('prenom', 'like', $recherche.'%');

        });

    }


    // Filtre type
    if ($request->filled('type')) {

        $query->where('type_conge', $request->type);

    }


    // Filtre mois
    if ($request->filled('mois')) {

        $query->whereMonth('date_debut', $request->mois);

    }



    $conges = $query
        ->latest('date_creation')
        ->paginate(15)
        ->withQueryString();



    $totalConges = Conge::count();



    /*
    |--------------------------------------------------------------------------
    | Statistiques congés
    |--------------------------------------------------------------------------
    */


    $aujourdhui = Carbon::today();


    // Employés actuellement en congé aujourd'hui
    $enCongeAujourdhui = Conge::whereDate('date_debut','<=',$aujourdhui)
        ->whereDate('date_fin','>=',$aujourdhui)
        ->distinct('employe_id')
        ->count('employe_id');



    // Employés qui commencent un congé demain
    $congesDemain = Conge::whereDate(
            'date_debut',
            $aujourdhui->copy()->addDay()
        )
        ->distinct('employe_id')
        ->count('employe_id');



    // Employés en congé cette semaine
    $debutSemaine = $aujourdhui->copy()->startOfWeek();
    $finSemaine   = $aujourdhui->copy()->endOfWeek();


    $congesCetteSemaine = Conge::where(function($q) use ($debutSemaine,$finSemaine){

            $q->whereBetween('date_debut',[
                $debutSemaine,
                $finSemaine
            ])

            ->orWhereBetween('date_fin',[
                $debutSemaine,
                $finSemaine
            ]);

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
    $employes = Employe::whereHas('contratActif')
        ->with('conges', 'contratActif')
        ->orderBy('nom')
        ->get();

    return view('employes.conges.create', compact('employes'));
}

   public function store(Request $request)
{
    $validated = $request->validate([
        'employe_id'   => 'required|exists:employe,id',
        'type_conge'   => 'required|in:paye,sans_solde,maladie',
        'date_debut'   => 'required|date',
        'date_fin'     => 'required|date|after_or_equal:date_debut',
        'motif'        => 'nullable|string',
        'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);


    $employe = Employe::with('contratActif')->findOrFail($validated['employe_id']);

    $contrat = $employe->contratActif;


    // Vérifier contrat actif
    if (!$contrat) {

        return back()
            ->withInput()
            ->withErrors([
                'employe_id' => "{$employe->nom_complet} n'a pas de contrat actif. Impossible d'enregistrer un congé.",
            ]);
    }


    $dateDebut = Carbon::parse($validated['date_debut']);
    $dateFin   = Carbon::parse($validated['date_fin']);



    // Vérification chevauchement des congés existants
    $congeExiste = Conge::where('employe_id', $validated['employe_id'])
        ->where(function($query) use ($dateDebut, $dateFin){

            $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                  ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                  ->orWhere(function($q) use ($dateDebut, $dateFin){

                      $q->where('date_debut', '<=', $dateDebut)
                        ->where('date_fin', '>=', $dateFin);

                  });

        })
        ->exists();



    if($congeExiste){

        return back()
            ->withInput()
            ->withErrors([
                'date_debut' => "Cet employé possède déjà un congé durant cette période."
            ]);

    }




    // Vérification période contrat

    if ($contrat->date_debut && $dateDebut->lt($contrat->date_debut)) {

        return back()
            ->withInput()
            ->withErrors([
                'date_debut' => "La date de début du congé est avant le début du contrat."
            ]);

    }


    if ($contrat->date_fin && $dateFin->gt($contrat->date_fin)) {

        return back()
            ->withInput()
            ->withErrors([
                'date_fin' => "La date de fin du congé dépasse la fin du contrat."
            ]);

    }



    // Vérification solde congé payé

    if ($validated['type_conge'] === 'paye') {


        $joursDemandes = $dateDebut->diffInDays($dateFin) + 1;


        if ($joursDemandes > $employe->solde_conge) {


            return back()
                ->withInput()
                ->withErrors([
                    'date_fin' =>
                    "Solde insuffisant : {$employe->nom_complet} possède seulement {$employe->solde_conge} jour(s) disponible(s)."
                ]);

        }

    }



    $validated['tenant_id'] = 1;



    if ($request->hasFile('justificatif')) {

        $validated['justificatif'] =
            $request->file('justificatif')
            ->store('justificatifs','public');

    }



    Conge::create($validated);



    return redirect()
        ->route('employes.conges.index')
        ->with('success','Congé ajouté avec succès.');
}}

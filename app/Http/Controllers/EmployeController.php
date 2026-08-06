<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use App\Models\Employe;

class EmployeController extends Controller
{
    public function index(Request $request)
    {
                $query = Employe::where(function($q){
            $q->where('statutEmploye','!=','archive')
            ->orWhereNull('statutEmploye');
        });

        // Recherche fluide
        if($request->filled('q'))
        {

            $q = $request->q;

            $query->where(function($query) use ($q){

                $query->where('nom','like',$q.'%')
                    ->orWhere('prenom','like',$q.'%')
                    ->orWhere('matricule','like',$q.'%');

            });

        }


        $employes = $query->latest()->paginate(15);


        $totalemploye = Employe::where('statutEmploye', '!=', 'archive')->count();

        $employesActifs = Employe::where(
            'statutEmploye',
            'actif'
        )->count();


        $employesConge = Employe::where(
            'statutEmploye',
            'en_conge'
        )->count();



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

public function create()
{
    return view('employes.create');
}

public function store(Request $request)
{

    $validated = $request->validate([
        'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'sexe' => 'required',
        'date_naissance' => 'required|date',
        'lieu_naissance' => 'required|string|max:255',
        'nationalite' => 'required|string|max:255',
        'cin_passeport' => ['required','string','max:8',Rule::unique('employe')->where(fn ($query) => $query->where('tenant_id', 1)), ],
        'situation_familiale' => 'string',
        'nb_enfants' => 'nullable|integer',
        'adresse' => 'required|string',
        'ville' => 'required|string|max:255',
        'code_postal' => 'required|string|max:20',
        'tel_perso' => 'required|string|max:20',
        'tel_pro' => 'nullable|string|max:20',
        'email_perso' => 'required|email',
        'email_pro' => 'nullable|email',
        'nom_contact_urgence' => 'required|string|max:255',
        'lien_parente' => 'required|string|max:255',
        'telephone_urgence' => 'required|string|max:20',
        'adresse_urgence' => 'required|string',
    ]);

    $dernierEmploye = Employe::latest('id')->first();

    if ($dernierEmploye) {
        $numero = $dernierEmploye->id + 1;
    }
    else {
        $numero = 1;
    }

    $validated['matricule'] = 'EMP-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    $validated['statutEmploye'] = 'attente_contrat';
    $validated['tenant_id'] = 1;
    $validated['entreprise_id'] = 1;
    $validated['utilisateur_creation_id'] = 1;


    Employe::create($validated);

    return redirect()->route('employes.index')->with('success', 'Employé ajouté avec succès.');
}
public function info(int $id)
{
    $employe = Employe::with('contrats')->findOrFail($id);
    return view('employes.info',compact('employe'));
}
public function edit($id)
{
    $employe = Employe::findOrFail($id);
    return view('employes.edit', compact('employe'));
}

public function update(Request $request, $id)
{
    $employe = Employe::findOrFail($id);

    $validated = $request->validate([
        // Champs modifiables uniquement
        'situation_familiale' => ['required', Rule::in(['celibataire', 'marie', 'divorce', 'veuf'])],
        'nb_enfants' => 'nullable|integer|min:0',
        'adresse' => 'required|string',
        'ville' => 'required|string|max:255',
        'code_postal' => 'required|string|max:20',
        'tel_perso' => 'required|string|max:20',
        'tel_pro' => 'nullable|string|max:20',
        'email_perso' => 'required|email',
        'email_pro' => 'nullable|email',
        'nom_contact_urgence' => 'required|string|max:255',
        'lien_parente' => 'required|string|max:255',
        'telephone_urgence' => 'required|string|max:20',
        'adresse_urgence' => 'required|string',
    ]);

    // Sécurité : nom, prenom, cin_passeport, date_naissance, sexe,
    // lieu_naissance, nationalite sont volontairement absents de
    // $validated donc ne peuvent jamais être modifiés ici, même si
    // le formulaire est trafiqué côté client.
    $employe->update($validated);

    return redirect()->route('employes.info', $employe->id)
        ->with('success', 'Employé mis à jour avec succès.');
}

public function destroy(Employe $employe)
{
    // On résilie tout contrat en cours (actif ou à venir) : un employé
    // archivé ne peut pas garder un contrat en vigueur en base.
    $employe->contrats()
        ->whereIn('statut', ['actif', 'a_venir'])
        ->update(['statut' => 'resilie']);

    $employe->update([
        'statutEmploye' => 'archive',
    ]);

    return redirect()
        ->route('employes.index')
        ->with('success', 'Employé archivé avec succès.');
}
public function archives()
{
    $employes = Employe::where('statutEmploye', 'archive')
        ->latest()
        ->paginate(15);

    return view('employes.archives', compact('employes'));
}
public function desarchiver(Employe $employe)
{
    $employe->update([
        'statutEmploye' => 'attente_contrat',
    ]);

    return redirect()
        ->route('employes.archives')
        ->with('success', 'Employé désarchivé avec succès.');
}
}

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
class SalaireController extends Controller
{

    public function index(Request $request)
    {
        //recherche
        $query = Salaire::with('employe');
         if ($request->q) {

    $query->whereHas('employe', function($q) use ($request){

        $q->where('nom','like','%'.$request->q.'%')
          ->orWhere('prenom','like','%'.$request->q.'%');

    });

}
        //filtrage par mois
        if ($request->filled('mois')) {
            $query->where('periode', 'like', '%-' . $request->mois);
        }
        //pagination
         $salaires = $query->latest('date_creation')
            ->paginate(15)
            ->withQueryString();

        $totalsalaire = Salaire::count();

        $masseSalariale = Salaire::query()
            ->when($request->filled('mois'), fn ($q) => $q->where('periode', 'like', '%-' . $request->mois))
            ->selectRaw('COALESCE(SUM(salaire_brut + total_primes - total_avances), 0) as total')
            ->value('total');

        $parametrePaie = ParametrePaie::first();

        return view('employes.salaires.liste', compact(
            'totalsalaire',
            'salaires',
            'masseSalariale',
            'parametrePaie'
        ));
    }

    public function create()
    {
        $employes = Employe::whereHas('contrats', function ($query) {
            $query->where('statut', 'actif');
        })->get();
        return view('employes.salaires.create', compact('employes'));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'employe_id'      => 'required|exists:employe,id',
        'nouveau_salaire' => 'nullable|numeric|min:0',
        'avance'          => 'nullable|numeric|min:0',
        'date_avance'     => 'nullable|date|required_with:avance',
        'motif_avance'    => 'nullable|string|max:255',

        'montant_prime'   => 'nullable|numeric|min:0',
        'date_prime'      => 'nullable|date|required_with:montant_prime',
        'motif_prime'     => 'nullable|string|max:255',
    ]);


    DB::transaction(function () use ($validated) {

        // Récupérer l'employé
        $employe = Employe::findOrFail($validated['employe_id']);


        // Récupérer le contrat actif
        $contrat = $employe->contrats()
            ->where('statut', 'actif')
            ->latest()
            ->first();


        if (!$contrat) {
            abort(422, "Cet employé n'a pas de contrat actif.");
        }


        // Période du salaire actuel
        $periode = now()->format('Y-m');


        // Récupérer ou créer le salaire du mois
        $salaire = Salaire::firstOrNew([
            'employe_id' => $employe->id,
            'contrat_id' => $contrat->id,
            'periode'    => $periode,
        ]);



        // Si le salaire n'existe pas encore
        if (!$salaire->exists) {

            $dernierSalaire = $employe->salaires()
                ->latest('periode')
                ->first();


            $salaire->tenant_id = 1;

            $salaire->salaire_brut =
                $dernierSalaire->salaire_brut ?? 0;


            $salaire->total_primes = 0;
            $salaire->statut = 'en_attente';
            $salaire->total_avances = 0;
        }



        // ==========================
        // Modification du salaire
        // ==========================

        if (!empty($validated['nouveau_salaire'])) {

            $salaire->salaire_brut =
                $validated['nouveau_salaire'];
        }



        // ==========================
        // Gestion des avances
        // ==========================

        if (!empty($validated['avance'])) {


            $nouveauTotalAvances =
                $salaire->total_avances + $validated['avance'];



            // Vérification dépassement salaire

            if ($nouveauTotalAvances > $salaire->salaire_brut) {

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'avance' =>
                    'Le total des avances ne peut pas dépasser le salaire brut.'
                ]);

            }



            // Enregistrer l'avance

            AvanceSalaire::create([

                'tenant_id'   => 1,
                'employe_id'  => $employe->id,
                'contrat_id'  => $contrat->id,

                'montant'     => $validated['avance'],

                'date_avance' =>
                    $validated['date_avance'],

                'motif' =>
                    $validated['motif_avance'] ?? null,

            ]);



            // Mise à jour du salaire

            $salaire->total_avances +=
                $validated['avance'];

        }




        // ==========================
        // Gestion des primes
        // ==========================

        if (!empty($validated['montant_prime'])) {


            Prime::create([

                'tenant_id'  => 1,

                'employe_id' =>
                    $employe->id,

                'contrat_id' =>
                    $contrat->id,


                'montant' =>
                    $validated['montant_prime'],


                'date_prime' =>
                    $validated['date_prime'],


                'motif' =>
                    $validated['motif_prime'] ?? null,

            ]);



            // Mise à jour du salaire

            $salaire->total_primes +=
                $validated['montant_prime'];

        }




        // Sauvegarder le salaire

        $salaire->save();

    });



    return redirect()
        ->route('employes.salaires.index')
        ->with('success', 'Salaire mis à jour avec succès.');
}
public function payer(Salaire $salaire)
    {
        $salaire->update([
            'statut'        => 'paye',
            'date_paiement' => now(),
        ]);

        return back()->with('success', 'Salaire marqué comme payé.');
    }
public function annulerPaiement(Salaire $salaire)
    {
        $salaire->update([
            'statut'        => 'en_attente',
            'date_paiement' => null,
        ]);

        return back()->with('success', 'Paiement annulé, salaire repassé en attente.');
    }
public function payerTous(Request $request)
    {
        $periode = $request->input('periode', now()->format('Y-m'));

        $nb = Salaire::where('periode', $periode)
            ->where('statut', 'en_attente')
            ->update([
                'statut'        => 'paye',
                'date_paiement' => now(),
            ]);

        return back()->with('success', "{$nb} salaire(s) marqué(s) comme payé(s) pour {$periode}.");
    }
  public function updateConfig(Request $request)
    {
        $request->validate([
            // 28 max pour rester valide sur tous les mois (y compris février)
            'jour_paiement' => 'required|integer|min:1|max:28',
        ]);

        $tenantId = 1;

        ParametrePaie::updateOrCreate(
            ['tenant_id' => $tenantId],
            ['jour_paiement' => $request->jour_paiement]
        );

        return back()->with('success', 'Jour de paiement mis à jour.');
    }

}

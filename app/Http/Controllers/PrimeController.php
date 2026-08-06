<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Prime;
use App\Models\Salaire;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Prime::with('employe');

       if ($request->q) {

    $query->whereHas('employe', function($q) use ($request){

        $q->where('nom','like','%'.$request->q.'%')
          ->orWhere('prenom','like','%'.$request->q.'%');

    });

}

        if ($request->filled('mois')) {
            $query->whereMonth('date_prime', $request->mois);
        }

        $primes = $query->latest('date_prime')
            ->paginate(15)
            ->withQueryString();

        $totalPrimes = (clone $query)->sum('montant');

        return view('employes.primes.liste', compact('primes', 'totalPrimes'));
    }

    public function create()
    {
        $employes = Employe::whereHas('contrats', function ($query) {
            $query->where('statut', 'actif');
        })->get();

        return view('employes.primes.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employe_id' => 'required|exists:employe,id',
            'montant'    => 'required|numeric|min:0.01',
            'date_prime' => 'required|date',
            'motif'      => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {

            $employe = Employe::findOrFail($validated['employe_id']);

            $contrat = $employe->contrats()
                ->where('statut', 'actif')
                ->latest()
                ->first();

            if (!$contrat) {
                abort(422, "Cet employé n'a pas de contrat actif.");
            }

            $periode = Carbon::parse($validated['date_prime'])->format('Y-m');

            $salaire = Salaire::firstOrNew([
                'employe_id' => $employe->id,
                'contrat_id' => $contrat->id,
                'periode'    => $periode,
            ]);

            if (!$salaire->exists) {
                $dernierSalaire = $employe->salaires()->latest('periode')->first();

                $salaire->tenant_id     = 1;
                $salaire->salaire_brut  = $dernierSalaire->salaire_brut ?? 0;
                $salaire->total_primes  = 0;
                $salaire->total_avances = 0;
                $salaire->statut        = 'en_attente';
            }

            Prime::create([
                'tenant_id'  => 1,
                'employe_id' => $employe->id,
                'contrat_id' => $contrat->id,
                'montant'    => $validated['montant'],
                'date_prime' => $validated['date_prime'],
                'motif'      => $validated['motif'] ?? null,
            ]);

            $salaire->total_primes += $validated['montant'];
            $salaire->save();
        });

        return redirect()
            ->route('employes.primes.index')
            ->with('success', 'Prime enregistrée avec succès.');
    }

    public function destroy(Prime $prime)
    {
        DB::transaction(function () use ($prime) {

            $periode = $prime->date_prime->format('Y-m');

            $salaire = Salaire::where([
                'employe_id' => $prime->employe_id,
                'contrat_id' => $prime->contrat_id,
                'periode'    => $periode,
            ])->first();

            if ($salaire) {
                $salaire->total_primes = max(0, $salaire->total_primes - $prime->montant);
                $salaire->save();
            }

            $prime->delete();
        });

        return back()->with('success', 'Prime supprimée, salaire mis à jour.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AvanceSalaire;
use App\Models\Employe;
use App\Models\Salaire;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AvanceController extends Controller
{
    public function index(Request $request)
    {
        $query = AvanceSalaire::with('employe')->where('tenant_id', current_tenant_id());

        if ($request->q) {

    $query->whereHas('employe', function($q) use ($request){

        $q->where('nom','like','%'.$request->q.'%')
          ->orWhere('prenom','like','%'.$request->q.'%');

    });

}

        if ($request->filled('mois')) {
            $query->whereMonth('date_avance', $request->mois);
        }

        $avances = $query->latest('date_avance')
            ->paginate(15)
            ->withQueryString();

        $totalAvances = (clone $query)->sum('montant');

        return view('employes.avances.liste', compact('avances', 'totalAvances'));
    }

    public function create()
    {
        // Correctif : filtrage par tenant_id ajouté, sinon tous les
        // employés actifs de tous les tenants remontaient dans le
        // formulaire d'ajout d'avance.
        $employes = Employe::where('tenant_id', current_tenant_id())
            ->whereHas('contrats', function ($query) {
                $query->where('statut', 'actif');
            })
            ->orderBy('nom')
            ->get();

        return view('employes.avances.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employe_id'  => 'required|exists:employe,id',
            'montant'     => 'required|numeric|min:0.01',
            'date_avance' => 'required|date',
            'motif'       => 'nullable|string|max:255',
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
                ->latest()
                ->first();

            if (!$contrat) {
                abort(422, "Cet employé n'a pas de contrat actif.");
            }

            $periode = Carbon::parse($validated['date_avance'])->format('Y-m');

            // On récupère (ou prépare) la fiche de salaire du mois concerné
            $salaire = Salaire::firstOrNew([
                'employe_id' => $employe->id,
                'contrat_id' => $contrat->id,
                'periode'    => $periode,
            ]);

            if (!$salaire->exists) {
                $dernierSalaire = $employe->salaires()->latest('periode')->first();

                $salaire->tenant_id     = current_tenant_id();
                $salaire->salaire_brut  = $dernierSalaire->salaire_brut ?? 0;
                $salaire->total_primes  = 0;
                $salaire->total_avances = 0;
                $salaire->statut        = 'en_attente';
            }

            $nouveauTotalAvances = $salaire->total_avances + $validated['montant'];

            if ($nouveauTotalAvances > $salaire->salaire_brut) {
                throw ValidationException::withMessages([
                    'montant' => 'Le total des avances ne peut pas dépasser le salaire brut de ce mois.',
                ]);
            }

            AvanceSalaire::create([
                'tenant_id'   => current_tenant_id(),
                'employe_id'  => $employe->id,
                'contrat_id'  => $contrat->id,
                'montant'     => $validated['montant'],
                'date_avance' => $validated['date_avance'],
                'motif'       => $validated['motif'] ?? null,
            ]);

            $salaire->total_avances = $nouveauTotalAvances;
            $salaire->save();
        });

        return redirect()
            ->route('employes.avances.index')
            ->with('success', 'Avance enregistrée avec succès.');
    }

    public function destroy(AvanceSalaire $avance)
    {
        // Correctif : vérifier que l'avance appartient bien au tenant
        // courant avant toute suppression (protection route-model-binding).
        abort_unless($avance->tenant_id === current_tenant_id(), 403);

        DB::transaction(function () use ($avance) {

            $periode = $avance->date_avance->format('Y-m');

            $salaire = Salaire::where([
                'employe_id' => $avance->employe_id,
                'contrat_id' => $avance->contrat_id,
                'periode'    => $periode,
            ])->first();

            if ($salaire) {
                $salaire->total_avances = max(0, $salaire->total_avances - $avance->montant);
                $salaire->save();
            }

            $avance->delete();
        });

        return back()->with('success', 'Avance supprimée, salaire mis à jour.');
    }
}
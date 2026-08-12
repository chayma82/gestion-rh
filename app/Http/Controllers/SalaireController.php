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

class SalaireController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = 1;

        $query = Salaire::with(['employe', 'contrat'])
            ->where('tenant_id', $tenantId);

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

        $salaires = $query
            ->latest('date_creation')
            ->paginate(15)
            ->withQueryString();

        $totalsalaire = Salaire::where('tenant_id', $tenantId)->count();

        $masseSalariale = Salaire::query()
            ->where('tenant_id', $tenantId)
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

        $parametrePaie = ParametrePaie::where('tenant_id', $tenantId)->first();

        return view(
            'employes.salaires.liste',
            compact(
                'totalsalaire',
                'salaires',
                'masseSalariale',
                'parametrePaie'
            )
        );
    }

    public function create()
    {
        $employes = Employe::where('tenant_id', 1)
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
            $employe = Employe::findOrFail($validated['employe_id']);

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
                $salaire->tenant_id = 1;
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
                    'tenant_id' => 1,
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
                    'tenant_id' => 1,
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
        $tenantId = 1;

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
            ['tenant_id' => 1],
            ['jour_paiement' => $request->jour_paiement]
        );

        return back()->with(
            'success',
            'Jour de paiement mis à jour.'
        );
    }
}

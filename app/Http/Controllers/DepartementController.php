<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartementController extends Controller
{
    public function index(Request $request)
    {
        // Filtre explicite en plus du TenantScope automatique (défense en
        // profondeur) : ne montre jamais que les départements du tenant
        // actuellement connecté.
        $query = Departement::withCount(['postes', 'contrats'])
            ->where('tenant_id', current_tenant_id());

        if ($request->filled('q')) {
            $recherche = $request->q;

            $query->where(function ($q) use ($recherche) {
                $q->where('libelle', 'like', '%' . $recherche . '%')
                  ->orWhere('code', 'like', '%' . $recherche . '%');
            });
        }

        $departements = $query->orderBy('libelle')->paginate(15)->withQueryString();

        return view('departements.liste', compact('departements'));
    }

    public function create()
    {
        return view('departements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique('departement')->where(fn ($q) => $q->where('tenant_id', current_tenant_id())),
            ],
            'libelle' => 'required|string|max:150',
        ]);

        // tenant_id est de toute façon réattribué automatiquement par le
        // trait BelongsToTenant, mais on le laisse explicite par clarté.
        $validated['tenant_id'] = current_tenant_id();

        Departement::create($validated);

        return redirect()
            ->route('departements.index')
            ->with('success', 'Département créé avec succès.');
    }

    public function edit(Departement $departement)
    {
        abort_unless($departement->tenant_id === current_tenant_id(), 403);

        return view('departements.edit', compact('departement'));
    }

    public function update(Request $request, Departement $departement)
    {
        abort_unless($departement->tenant_id === current_tenant_id(), 403);

        $validated = $request->validate([
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique('departement')
                    ->where(fn ($q) => $q->where('tenant_id', current_tenant_id()))
                    ->ignore($departement->id),
            ],
            'libelle' => 'required|string|max:150',
        ]);

        $departement->update($validated);

        return redirect()
            ->route('departements.index')
            ->with('success', 'Département mis à jour avec succès.');
    }

    public function destroy(Departement $departement)
    {
        abort_unless($departement->tenant_id === current_tenant_id(), 403);

        if ($departement->postes()->exists() || $departement->contrats()->exists()) {
            return back()->withErrors([
                'departement' => "Impossible de supprimer ce département : des postes ou des contrats y sont encore rattachés.",
            ]);
        }

        $departement->delete();

        return redirect()
            ->route('departements.index')
            ->with('success', 'Département supprimé avec succès.');
    }
}

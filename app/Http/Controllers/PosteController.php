<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\Poste;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PosteController extends Controller
{
    public function index(Request $request)
    {
        // Filtre explicite en plus du TenantScope automatique (défense en
        // profondeur) : ne montre jamais que les postes du tenant
        // actuellement connecté.
        $query = Poste::with('departement')
            ->withCount('contrats')
            ->where('tenant_id', current_tenant_id());

        if ($request->filled('q')) {
            $recherche = $request->q;

            $query->where(function ($q) use ($recherche) {
                $q->where('libelle', 'like', '%' . $recherche . '%')
                  ->orWhere('code', 'like', '%' . $recherche . '%');
            });
        }

        if ($request->filled('departement_id')) {
            $query->where('departement_id', $request->departement_id);
        }

        $postes = $query->orderBy('libelle')->paginate(15)->withQueryString();

        $departements = Departement::where('tenant_id', current_tenant_id())
            ->orderBy('libelle')
            ->get();

        return view('postes.liste', compact('postes', 'departements'));
    }

    public function create()
    {
        $departements = Departement::where('tenant_id', current_tenant_id())
            ->orderBy('libelle')
            ->get();

        return view('postes.create', compact('departements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'departement_id' => [
                'required',
                Rule::exists('departement', 'id')->where(fn ($q) => $q->where('tenant_id', current_tenant_id())),
            ],
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique('poste')->where(fn ($q) => $q->where('tenant_id', current_tenant_id())),
            ],
            'libelle' => 'required|string|max:150',
        ]);

        $validated['tenant_id'] = current_tenant_id();

        Poste::create($validated);

        return redirect()
            ->route('postes.index')
            ->with('success', 'Poste créé avec succès.');
    }

    public function edit(Poste $poste)
    {
        abort_unless($poste->tenant_id === current_tenant_id(), 403);

        $departements = Departement::where('tenant_id', current_tenant_id())
            ->orderBy('libelle')
            ->get();

        return view('postes.edit', compact('poste', 'departements'));
    }

    public function update(Request $request, Poste $poste)
    {
        abort_unless($poste->tenant_id === current_tenant_id(), 403);

        $validated = $request->validate([
            'departement_id' => [
                'required',
                Rule::exists('departement', 'id')->where(fn ($q) => $q->where('tenant_id', current_tenant_id())),
            ],
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique('poste')
                    ->where(fn ($q) => $q->where('tenant_id', current_tenant_id()))
                    ->ignore($poste->id),
            ],
            'libelle' => 'required|string|max:150',
        ]);

        $poste->update($validated);

        return redirect()
            ->route('postes.index')
            ->with('success', 'Poste mis à jour avec succès.');
    }

    public function destroy(Poste $poste)
    {
        abort_unless($poste->tenant_id === current_tenant_id(), 403);

        if ($poste->contrats()->exists()) {
            return back()->withErrors([
                'poste' => "Impossible de supprimer ce poste : des contrats y sont encore rattachés.",
            ]);
        }

        $poste->delete();

        return redirect()
            ->route('postes.index')
            ->with('success', 'Poste supprimé avec succès.');
    }
}

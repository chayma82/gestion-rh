<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::where('tenant_id', current_tenant_id())
            ->withCount('utilisateurs')
            ->orderBy('nom')
            ->get();

        return view('roles.liste', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => [
                'required', 'string', 'max:50',
                Rule::unique('role')->where(fn ($q) => $q->where('tenant_id', current_tenant_id())),
            ],
            'acces_admin' => ['sometimes', 'boolean'],
            'acces_facturation' => ['sometimes', 'boolean'],
            'acces_rh' => ['sometimes', 'boolean'],
        ]);

        $validated['tenant_id'] = current_tenant_id();
        $validated['acces_admin'] = $request->boolean('acces_admin');
        $validated['acces_facturation'] = $request->boolean('acces_facturation');
        $validated['acces_rh'] = $request->boolean('acces_rh');

        Role::create($validated);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    public function edit(int $id)
    {
        $role = Role::where('tenant_id', current_tenant_id())->findOrFail($id);

        if (strtolower($role->nom) === 'admin') {
            return redirect()
                ->route('roles.index')
                ->withErrors(['role' => "Le rôle Admin est protégé et ne peut pas être modifié."]);
        }

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, int $id)
    {
        $role = Role::where('tenant_id', current_tenant_id())->findOrFail($id);

        if (strtolower($role->nom) === 'admin') {
            return back()->withErrors([
                'role' => "Le rôle Admin est protégé et ne peut pas être modifié.",
            ]);
        }

        $validated = $request->validate([
            'nom' => [
                'required', 'string', 'max:50',
                Rule::unique('role')
                    ->where(fn ($q) => $q->where('tenant_id', current_tenant_id()))
                    ->ignore($role->id),
            ],
            'acces_admin' => ['sometimes', 'boolean'],
            'acces_facturation' => ['sometimes', 'boolean'],
            'acces_rh' => ['sometimes', 'boolean'],
        ]);

        $validated['acces_admin'] = $request->boolean('acces_admin');
        $validated['acces_facturation'] = $request->boolean('acces_facturation');
        $validated['acces_rh'] = $request->boolean('acces_rh');

        $role->update($validated);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(int $id)
    {
        $role = Role::where('tenant_id', current_tenant_id())->findOrFail($id);

        if (strtolower($role->nom) === 'admin') {
            return back()->withErrors([
                'role' => "Le rôle Admin est protégé et ne peut pas être supprimé.",
            ]);
        }

        if ($role->utilisateurs()->exists()) {
            return back()->withErrors([
                'role' => "Impossible de supprimer ce rôle : des utilisateurs y sont encore rattachés.",
            ]);
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }
}

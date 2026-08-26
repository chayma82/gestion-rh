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
        ]);

        $validated['tenant_id'] = current_tenant_id();

        Role::create($validated);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rôle créé avec succès.');
    }

    public function edit(int $id)
    {
        $role = Role::where('tenant_id', current_tenant_id())->findOrFail($id);

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, int $id)
    {
        $role = Role::where('tenant_id', current_tenant_id())->findOrFail($id);

        $validated = $request->validate([
            'nom' => [
                'required', 'string', 'max:50',
                Rule::unique('role')
                    ->where(fn ($q) => $q->where('tenant_id', current_tenant_id()))
                    ->ignore($role->id),
            ],
        ]);

        $role->update($validated);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function destroy(int $id)
    {
        $role = Role::where('tenant_id', current_tenant_id())->findOrFail($id);

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
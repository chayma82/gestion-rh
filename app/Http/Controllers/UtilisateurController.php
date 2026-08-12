<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Utilisateur;
use App\Models\Role;

class UtilisateurController extends Controller
{
    // TODO: remplacer ce 1 en dur par le tenant de l'utilisateur connecté
    // une fois l'authentification en place (ex: auth()->user()->tenant_id)
    private const TENANT_ID = 1;

    public function index(Request $request)
    {
        $query = Utilisateur::with('role')->where('tenant_id', self::TENANT_ID);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nom', 'like', "%{$q}%")
                    ->orWhere('prenom', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $utilisateurs = $query->orderBy('nom')->paginate(10)->withQueryString();
        $roles = Role::where('tenant_id', self::TENANT_ID)->orderBy('nom')->get();

        return view('utilisateurs.liste', compact('utilisateurs', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('tenant_id', self::TENANT_ID)->orderBy('nom')->get();

        return view('utilisateurs.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('utilisateur')->where(fn ($q) => $q->where('tenant_id', self::TENANT_ID)),
            ],
            'telephone' => 'nullable|string|max:30',
            'role_id' => [
                'required',
                Rule::exists('role', 'id')->where(fn ($q) => $q->where('tenant_id', self::TENANT_ID)),
            ],
            'motdepasse' => 'required|string|min:8|confirmed',
            'actif' => 'nullable|boolean',
        ]);

        Utilisateur::create([
            'tenant_id' => self::TENANT_ID,
            'entreprise_id' => 1,
            'role_id' => $validated['role_id'],
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'motdepasse' => $validated['motdepasse'], // hashé automatiquement par le modèle
            'actif' => $request->boolean('actif', true),
        ]);

        return redirect()
            ->route('utilisateur.index')
            ->with('success', 'Utilisateur ajouté avec succès.');
    }

    public function edit(int $id)
    {
        $utilisateur = Utilisateur::where('tenant_id', self::TENANT_ID)->findOrFail($id);
        $roles = Role::where('tenant_id', self::TENANT_ID)->orderBy('nom')->get();

        return view('utilisateurs.edit', compact('utilisateur', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $utilisateur = Utilisateur::where('tenant_id', self::TENANT_ID)->findOrFail($id);

        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('utilisateur')
                    ->where(fn ($q) => $q->where('tenant_id', self::TENANT_ID))
                    ->ignore($utilisateur->id),
            ],
            'telephone' => 'nullable|string|max:30',
            'role_id' => [
                'required',
                Rule::exists('role', 'id')->where(fn ($q) => $q->where('tenant_id', self::TENANT_ID)),
            ],
            // mot de passe optionnel à la modification : on ne le change que s'il est renseigné
            'motdepasse' => 'nullable|string|min:8|confirmed',
            'actif' => 'nullable|boolean',
        ]);

        $utilisateur->nom = $validated['nom'];
        $utilisateur->prenom = $validated['prenom'];
        $utilisateur->email = $validated['email'];
        $utilisateur->telephone = $validated['telephone'] ?? null;
        $utilisateur->role_id = $validated['role_id'];
        $utilisateur->actif = $request->boolean('actif', true);

        if (!empty($validated['motdepasse'])) {
            $utilisateur->motdepasse = $validated['motdepasse']; // hashé automatiquement par le modèle
        }

        $utilisateur->save();

        return redirect()
            ->route('utilisateur.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    // Active / désactive l'accès au site sans supprimer le compte (préserve l'historique)
    public function toggleActif(int $id)
    {
        $utilisateur = Utilisateur::where('tenant_id', self::TENANT_ID)->findOrFail($id);
        $utilisateur->update(['actif' => !$utilisateur->actif]);

        return back()->with('success', $utilisateur->actif
            ? 'Accès réactivé pour ' . $utilisateur->nom_complet
            : 'Accès désactivé pour ' . $utilisateur->nom_complet);
    }

    public function destroy(int $id)
    {
        $utilisateur = Utilisateur::where('tenant_id', self::TENANT_ID)->findOrFail($id);
        $utilisateur->delete();

        return redirect()
            ->route('utilisateur.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}

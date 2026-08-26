<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Log;
use App\Models\Utilisateur;
use App\Models\Role;

/**
 * NOTE : l'envoi du mail de confirmation d'activation (CompteEntrepriseActiveMail)
 * n'est PAS géré ici. Il est déclenché automatiquement par UtilisateurObserver
 * dès qu'un Utilisateur passe de actif=false à actif=true via Eloquent
 * (voir app/Observers/UtilisateurObserver.php).
 */
class UtilisateurController extends Controller
{
    public function index(Request $request)
    {
        $query = Utilisateur::with('role')->where('tenant_id', current_tenant_id());

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
        $roles = Role::where('tenant_id', current_tenant_id())->orderBy('nom')->get();

        return view('utilisateurs.liste', compact('utilisateurs', 'roles'));
    }

    public function create()
    {
        $roles = Role::where('tenant_id', current_tenant_id())->orderBy('nom')->get();

        return view('utilisateurs.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('utilisateur')->where(fn ($q) => $q->where('tenant_id', current_tenant_id())),
            ],
            'telephone' => 'nullable|string|max:30',
            'role_id' => [
                'required',
                Rule::exists('role', 'id')->where(fn ($q) => $q->where('tenant_id', current_tenant_id())),
            ],
            'motdepasse' => 'required|string|min:8|confirmed',
            'actif' => 'nullable|boolean',
        ]);

        $acteur = current_utilisateur();

        $utilisateur = Utilisateur::create([
            'tenant_id' => current_tenant_id(),
            'entreprise_id' => current_entreprise_id(),
            'role_id' => $validated['role_id'],
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'motdepasse' => $validated['motdepasse'], // hashé automatiquement par le modèle
            'actif' => $request->boolean('actif', true),
        ]);

        Log::enregistrer(
            tenantId: current_tenant_id(),
            utilisateurId: $acteur?->id,
            recordId: $utilisateur->id,
            nomTable: 'utilisateur',
            description: "Création de l'utilisateur {$utilisateur->email} par {$acteur?->email}",
        );

        return redirect()
            ->route('utilisateur.index')
            ->with('success', 'Utilisateur ajouté avec succès.');
    }

    public function edit(int $id)
    {
        $utilisateur = Utilisateur::where('tenant_id', current_tenant_id())->findOrFail($id);
        $roles = Role::where('tenant_id', current_tenant_id())->orderBy('nom')->get();

        return view('utilisateurs.edit', compact('utilisateur', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        $utilisateur = Utilisateur::where('tenant_id', current_tenant_id())->findOrFail($id);
        $acteur = current_utilisateur();

        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('utilisateur')
                    ->where(fn ($q) => $q->where('tenant_id', current_tenant_id()))
                    ->ignore($utilisateur->id),
            ],
            'telephone' => 'nullable|string|max:30',
            'role_id' => [
                'required',
                Rule::exists('role', 'id')->where(fn ($q) => $q->where('tenant_id', current_tenant_id())),
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

        $motDePasseChange = !empty($validated['motdepasse']);

        if ($motDePasseChange) {
            $utilisateur->motdepasse = $validated['motdepasse']; // hashé automatiquement par le modèle
        }

        $utilisateur->save(); // déclenche UtilisateurObserver::updating() si actif passe à true

        $description = "Modification de l'utilisateur {$utilisateur->email} par {$acteur?->email}";
        if ($motDePasseChange) {
            $description .= " (mot de passe changé)";
        }

        Log::enregistrer(
            tenantId: current_tenant_id(),
            utilisateurId: $acteur?->id,
            recordId: $utilisateur->id,
            nomTable: 'utilisateur',
            description: $description,
        );

        return redirect()
            ->route('utilisateur.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    // Active / désactive l'accès au site sans supprimer le compte (préserve l'historique)
    public function toggleActif(int $id)
    {
        $utilisateur = Utilisateur::where('tenant_id', current_tenant_id())->findOrFail($id);
        $acteur = current_utilisateur();

        $utilisateur->update(['actif' => !$utilisateur->actif]); // déclenche UtilisateurObserver::updating() si actif passe à true

        Log::enregistrer(
            tenantId: current_tenant_id(),
            utilisateurId: $acteur?->id,
            recordId: $utilisateur->id,
            nomTable: 'utilisateur',
            description: $utilisateur->actif
                ? "Accès réactivé pour {$utilisateur->email} par {$acteur?->email}"
                : "Accès désactivé pour {$utilisateur->email} par {$acteur?->email}",
        );

        return back()->with('success', $utilisateur->actif
            ? 'Accès réactivé pour ' . $utilisateur->nom_complet
            : 'Accès désactivé pour ' . $utilisateur->nom_complet);
    }

    public function destroy(int $id)
    {
        $utilisateur = Utilisateur::where('tenant_id', current_tenant_id())->findOrFail($id);
        $acteur = current_utilisateur();

        // On journalise AVANT la suppression : record_id fait référence à un
        // utilisateur qui n'existera plus juste après.
        Log::enregistrer(
            tenantId: current_tenant_id(),
            utilisateurId: $acteur?->id,
            recordId: $utilisateur->id,
            nomTable: 'utilisateur',
            description: "Suppression de l'utilisateur {$utilisateur->email} par {$acteur?->email}",
        );

        $utilisateur->delete();

        return redirect()
            ->route('utilisateur.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}

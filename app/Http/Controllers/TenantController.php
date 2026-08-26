<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Tenant;
use App\Models\TenantCategorie;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

/**
 * Gestion des tenants (entreprises inscrites sur la plateforme).
 *
 * Contrairement aux autres contrôleurs, celui-ci n'est PAS scopé par
 * current_tenant_id() : il est réservé à un super-admin qui doit voir
 * tous les tenants, notamment ceux en attente de validation après
 * inscription (cf. AuthController::store, qui crée l'admin d'un nouveau
 * tenant avec actif = false).
 *
 * NOTE : l'envoi du mail de confirmation d'activation (CompteEntrepriseActiveMail)
 * n'est PAS géré ici. Il est déclenché automatiquement par UtilisateurObserver
 * dès qu'un Utilisateur passe de actif=false à actif=true via Eloquent
 * (voir app/Observers/UtilisateurObserver.php).
 *
 * Protégez ces routes avec un middleware dédié, ex :
 *   Route::middleware(['auth.utilisateur', 'super.admin'])->group(...)
 * ou vérifiez is_super_admin() en tête de chaque méthode comme ci-dessous.
 */
class TenantController extends Controller
{
    private function autoriserSuperAdmin(): void
    {
        abort_unless(is_super_admin(), 403, "Accès réservé à l'administration de la plateforme.");
    }

    public function index(Request $request)
    {
        $this->autoriserSuperAdmin();

        $query = Tenant::with('tenantCategorie')
            ->withCount('utilisateurs');

        if ($request->filled('q')) {
            $query->where('nom', 'like', '%' . $request->q . '%');
        }

        // "En attente" = le tenant n'a encore aucun utilisateur actif
        // (cf. AuthController::store qui crée l'admin avec actif = false).
        if ($request->get('statut') === 'en_attente') {
            $query->whereDoesntHave('utilisateurs', fn ($q) => $q->where('actif', true));
        }

        $tenants = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $categories = TenantCategorie::orderBy('nom')->get();

        return view('tenants.liste', compact('tenants', 'categories'));
    }

    public function show(Tenant $tenant)
    {
        $this->autoriserSuperAdmin();

        $tenant->load(['tenantCategorie', 'utilisateurs.role', 'roles']);

        return view('tenants.show', compact('tenant'));
    }

    /**
     * Valide l'inscription d'un tenant : active son (ses) compte(s)
     * administrateur en attente.
     *
     * IMPORTANT : on charge les modèles puis on appelle ->update() sur
     * CHAQUE instance (plutôt qu'un update() de masse sur le Builder),
     * car seul un update() sur une instance Eloquent déclenche les
     * événements de modèle (updating/updated) et donc l'UtilisateurObserver
     * qui envoie le mail de confirmation.
     */
    public function valider(Tenant $tenant)
    {
        $this->autoriserSuperAdmin();

        $superAdmin = current_utilisateur();

        $utilisateursEnAttente = Utilisateur::where('tenant_id', $tenant->id)
            ->where('actif', false)
            ->get();

        foreach ($utilisateursEnAttente as $utilisateur) {
            $utilisateur->update(['actif' => true]); // déclenche UtilisateurObserver::updating()
        }

        $nb = $utilisateursEnAttente->count();

        Log::enregistrer(
            tenantId: $tenant->id,
            utilisateurId: $superAdmin?->id,
            recordId: $tenant->id,
            nomTable: 'tenant',
            description: "Tenant « {$tenant->nom} » validé : {$nb} compte(s) activé(s)",
        );

        return back()->with(
            'success',
            $nb > 0
                ? "Tenant validé : {$nb} compte(s) activé(s)."
                : "Ce tenant n'avait aucun compte en attente de validation."
        );
    }

    /**
     * Suspend un tenant entier (désactive tous ses comptes utilisateurs).
     * Ne supprime rien : préserve l'historique, comme le reste de l'appli.
     */
    public function suspendre(Tenant $tenant)
    {
        $this->autoriserSuperAdmin();

        $superAdmin = current_utilisateur();

        $nb = Utilisateur::where('tenant_id', $tenant->id)->update(['actif' => false]);

        Log::enregistrer(
            tenantId: $tenant->id,
            utilisateurId: $superAdmin?->id,
            recordId: $tenant->id,
            nomTable: 'tenant',
            description: "Tenant « {$tenant->nom} » suspendu : {$nb} compte(s) désactivé(s)",
        );

        return back()->with('success', "Tenant suspendu : {$nb} compte(s) désactivé(s).");
    }

    public function changerCategorie(Request $request, Tenant $tenant)
    {
        $this->autoriserSuperAdmin();

        $superAdmin = current_utilisateur();

        $validated = $request->validate([
            'tenant_categorie_id' => 'required|exists:tenant_categorie,id',
        ]);

        $ancienneCategorie = $tenant->tenantCategorie?->nom ?? 'aucune';

        $tenant->update($validated);

        $nouvelleCategorie = TenantCategorie::find($validated['tenant_categorie_id'])?->nom ?? '—';

        Log::enregistrer(
            tenantId: $tenant->id,
            utilisateurId: $superAdmin?->id,
            recordId: $tenant->id,
            nomTable: 'tenant',
            description: "Catégorie du tenant « {$tenant->nom} » changée : {$ancienneCategorie} → {$nouvelleCategorie}",
        );

        return back()->with('success', 'Catégorie du tenant mise à jour.');
    }
}

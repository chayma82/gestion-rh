<?php

use App\Models\Utilisateur;

/*
|--------------------------------------------------------------------------
| Helpers multi-tenant
|--------------------------------------------------------------------------
|
| Remplace tous les "1" en dur utilisés comme tenant_id / entreprise_id /
| utilisateur_id dans les contrôleurs par la vraie valeur de l'utilisateur
| connecté (basée sur la session posée par AuthController::login()).
|
| Si current_utilisateur() existe déjà ailleurs dans le projet (c'est le
| cas d'après AuthController / AuthenticateUtilisateur), NE PAS dupliquer
| cette fonction : gardez uniquement current_tenant_id(), current_entreprise_id()
| et current_utilisateur_id() ci-dessous, et supprimez le bloc du dessus.
|
*/

if (!function_exists('current_utilisateur')) {
    /**
     * Utilisateur actuellement connecté, résolu depuis la session.
     * Mis en cache pour la durée de la requête (évite une requête SQL
     * à chaque appel).
     */
    function current_utilisateur(): ?Utilisateur
    {
        static $cache = null;
        static $resolved = false;

        if ($resolved) {
            return $cache;
        }

        $id = session('utilisateur_id');
        $cache = $id ? Utilisateur::find($id) : null;
        $resolved = true;

        return $cache;
    }
}

if (!function_exists('current_tenant_id')) {
    /**
     * tenant_id de l'utilisateur connecté.
     * Coupe la requête avec un 401 si appelée hors d'une route protégée
     * par le middleware AuthenticateUtilisateur (garde-fou de sécurité :
     * mieux vaut planter que de renvoyer les données d'un autre tenant).
     */
    function current_tenant_id(): int
    {
        $utilisateur = current_utilisateur();

        abort_if(!$utilisateur, 401, 'Utilisateur non authentifié.');

        return $utilisateur->tenant_id;
    }
}

if (!function_exists('current_entreprise_id')) {
    function current_entreprise_id(): int
    {
        $utilisateur = current_utilisateur();

        abort_if(!$utilisateur, 401, 'Utilisateur non authentifié.');

        return $utilisateur->entreprise_id;
    }
}

if (!function_exists('current_utilisateur_id')) {
    function current_utilisateur_id(): ?int
    {
        return current_utilisateur()?->id;
    }
}

if (!function_exists('is_super_admin')) {
    /**
     * Un "super admin" est un utilisateur dont le rôle s'appelle "SuperAdmin".
     * Utilisé pour la gestion des tenants (validation des inscriptions),
     * qui doit rester en dehors du périmètre d'un tenant classique.
     * Adaptez cette fonction si vous préférez un mécanisme différent
     * (ex : colonne dédiée utilisateur.est_super_admin).
     */
    function is_super_admin(): bool
    {
        $utilisateur = current_utilisateur();

        return $utilisateur && $utilisateur->role?->nom === 'SuperAdmin';
    }
}
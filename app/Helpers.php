<?php

use App\Models\Utilisateur;

if (!function_exists('current_utilisateur')) {
    /**
     * Retourne l'utilisateur actuellement connecté (ou null si personne n'est connecté).
     * Mis en cache pour la durée de la requête pour éviter des requêtes SQL répétées.
     */
    function current_utilisateur(): ?Utilisateur
    {
        static $utilisateur = null;
        static $resolved = false;

        if ($resolved) {
            return $utilisateur;
        }

        $resolved = true;
        $id = session('utilisateur_id');

        if (!$id) {
            return null;
        }

        $utilisateur = Utilisateur::with(['role', 'tenant.tenantCategorie'])->find($id);

        return $utilisateur;
    }
}

if (!function_exists('current_tenant_id')) {
    /**
     * Retourne le tenant_id de l'utilisateur connecté.
     * Valeur de secours à 1 tant que toutes les pages ne sont pas protégées par le login
     * (permet une migration progressive sans tout casser d'un coup).
     */
    function current_tenant_id(): int
    {
        return current_utilisateur()?->tenant_id ?? (int) session('tenant_id', 1);
    }
}
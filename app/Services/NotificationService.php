<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Utilisateur;
use App\Models\Contrat;
use App\Models\Employe;
use Carbon\Carbon;

class NotificationService
{
    // ------------------------------------------------------------------
    // Création
    // ------------------------------------------------------------------

    /**
     * Création "contextuelle" : à utiliser UNIQUEMENT dans un contexte web,
     * où un utilisateur est réellement connecté (current_utilisateur_id() /
     * current_tenant_id() disponibles).
     *
     * Ne jamais appeler ceci depuis une commande planifiée : sans session,
     * current_utilisateur_id() planterait. Utiliser creerPourUtilisateur()
     * dans ce cas.
     *
     * @param string $type Doit être l'une des valeurs de l'enum de la
     *                      colonne `type` : 'facture', 'employe', 'contrat'.
     */
    public static function creer(
        string $type,
        string $titre,
        string $message,
        ?int $referenceId = null
    ): Notification {
        return self::creerPourUtilisateur(
            current_utilisateur_id(),
            $type,
            $titre,
            $message,
            $referenceId,
            current_tenant_id()
        );
    }

    /**
     * Création "explicite" : LA méthode à utiliser depuis les commandes
     * planifiées, les jobs en queue, ou dès qu'on connaît précisément le
     * destinataire (pas de session disponible).
     *
     * $tenantId est optionnel : si omis, il est déduit du tenant_id de
     * l'utilisateur destinataire.
     */
    public static function creerPourUtilisateur(
        int $utilisateurId,
        string $type,
        string $titre,
        string $message,
        ?int $referenceId = null,
        ?int $tenantId = null
    ): Notification {
        return Notification::create([
            'tenant_id'      => $tenantId ?? Utilisateur::whereKey($utilisateurId)->value('tenant_id'),
            'utilisateur_id' => $utilisateurId,
            'type'           => $type,
            'titre'          => $titre,
            'message'        => $message,
            'lue'            => false,
            'date_reception' => now(),
            'reference_id'   => $referenceId,
        ]);
    }

    // ------------------------------------------------------------------
    // Contrats
    // ------------------------------------------------------------------

    /**
     * À appeler depuis une commande planifiée (ex: notifications:verifier-contrats)
     * pour chaque destinataire concerné.
     */
    public static function contratBientotExpire(Contrat $contrat, int $joursRestants, int $utilisateurId): Notification
    {
        $dateFinFormatee = $contrat->date_fin
            ? Carbon::parse($contrat->date_fin)->format('Y-m-d')
            : 'date non définie';

        // $joursRestants = 0 correspond au jour même de l'expiration
        // (le contrat est encore "actif" ce jour-là, il ne bascule en
        // "expire" que le lendemain — voir ExpirerContrats).
        $titre   = $joursRestants === 0 ? 'Contrat expirant aujourd\'hui' : 'Contrat arrivant à expiration';
        $message = $joursRestants === 0
            ? "Le contrat {$contrat->numcontrat} de {$contrat->employe->nom_complet} expire aujourd'hui ({$dateFinFormatee})."
            : "Le contrat {$contrat->numcontrat} de {$contrat->employe->nom_complet} expire dans {$joursRestants} jours (le {$dateFinFormatee}).";

        return self::creerPourUtilisateur($utilisateurId, 'contrat', $titre, $message, $contrat->id, $contrat->tenant_id);
    }

    /**
     * À appeler quand un contrat vient de passer au statut "expire"
     * (ex: dans contrats:expirer / contrats:actualiser / employes:synchroniser-statuts).
     */
    public static function contratExpire(Contrat $contrat, int $utilisateurId): Notification
    {
        $dateFinFormatee = $contrat->date_fin
            ? Carbon::parse($contrat->date_fin)->format('Y-m-d')
            : 'date non définie';

        return self::creerPourUtilisateur(
            $utilisateurId,
            'contrat',
            'Contrat expiré',
            "Le contrat {$contrat->numcontrat} de {$contrat->employe->nom_complet} a expiré le {$dateFinFormatee}.",
            $contrat->id,
            $contrat->tenant_id
        );
    }

    // ------------------------------------------------------------------
    // Factures
    // ------------------------------------------------------------------

    /**
     * À appeler pour prévenir qu'une facture approche de son échéance.
     */
    public static function factureBientotEcheance($facture, int $joursRestants, int $utilisateurId): Notification
    {
        return self::creerPourUtilisateur(
            $utilisateurId,
            'facture',
            'Échéance de facture proche',
            "La facture #{$facture->numFacture} arrive à échéance dans {$joursRestants} jour(s).",
            $facture->id,
            $facture->tenant_id
        );
    }

    /**
     * Générique — conservée pour compatibilité, mais pour factures:maj-statuts
     * on utilise désormais les deux méthodes spécifiques ci-dessous, qui
     * portent une action claire selon le sens du flux (achat vs vente).
     */
    public static function factureEnRetard($facture, int $utilisateurId): Notification
    {
        return self::creerPourUtilisateur(
            $utilisateurId,
            'facture',
            'Facture en retard',
            "La facture #{$facture->numFacture} est désormais en retard de paiement.",
            $facture->id,
            $facture->tenant_id
        );
    }

    /**
     * Facture de VENTE en retard : c'est le client qui nous doit de l'argent,
     * l'action attendue est de le relancer.
     */
    public static function factureVenteARelancer($facture, int $utilisateurId): Notification
    {
        return self::creerPourUtilisateur(
            $utilisateurId,
            'facture',
            'Facture client à relancer',
            "La facture #{$facture->numFacture} est en retard de paiement : pensez à contacter le client.",
            $facture->id,
            $facture->tenant_id
        );
    }

    /**
     * Facture d'ACHAT en retard : c'est nous qui devons de l'argent au
     * fournisseur, l'action attendue est de la payer.
     */
    public static function factureAchatAPayer($facture, int $utilisateurId): Notification
    {
        return self::creerPourUtilisateur(
            $utilisateurId,
            'facture',
            'Facture fournisseur à payer',
            "La facture #{$facture->numFacture} est en retard : à régler auprès du fournisseur.",
            $facture->id,
            $facture->tenant_id
        );
    }

    /**
     * À appeler avant le jour de paiement des salaires configuré
     * (ParametrePaie::jour_paiement), typiquement à J-7, J-2 et J.
     * $joursRestants = 0 pour "c'est aujourd'hui".
     */
    public static function salairePaiementProche(int $tenantId, string $periode, int $joursRestants, int $utilisateurId): Notification
    {
        $titre   = $joursRestants === 0 ? 'Paiement des salaires aujourd\'hui' : 'Paiement des salaires à venir';
        $message = $joursRestants === 0
            ? "C'est aujourd'hui le jour de paiement des salaires pour la période {$periode}."
            : "Le paiement des salaires pour la période {$periode} est prévu dans {$joursRestants} jour(s).";

        return self::creerPourUtilisateur($utilisateurId, 'facture', $titre, $message, null, $tenantId);
    }

    /**
     * Conservée pour SalaireController::payer()/payerTous() : notifie qu'un
     * salaire vient d'être payé. $utilisateurId reste optionnel car cette
     * méthode est appelée depuis un contexte web (utilisateur connecté).
     */
    public static function paiementEffectue($salaire, ?int $utilisateurId = null): Notification
    {
        $titre   = 'Notification de paiement';
        $message = "Le salaire de {$salaire->employe->nom_complet} pour la période {$salaire->periode} a été payé.";

        return $utilisateurId
            ? self::creerPourUtilisateur($utilisateurId, 'facture', $titre, $message, $salaire->id, $salaire->tenant_id)
            : self::creer('facture', $titre, $message, $salaire->id);
    }

    // ------------------------------------------------------------------
    // Employés
    // ------------------------------------------------------------------

    /**
     * À appeler depuis EmployeController::store() juste après la création
     * d'un employé.
     */
    public static function nouvelEmploye(Employe $employe, int $utilisateurId): Notification
    {
        return self::creerPourUtilisateur(
            $utilisateurId,
            'employe',
            'Nouvel employé',
            "{$employe->nom_complet} vient d'être ajouté(e) à l'effectif.",
            $employe->id,
            $employe->tenant_id
        );
    }

    /**
     * À appeler depuis CongeController::store() (ou l'équivalent) juste
     * après la création d'un congé.
     */
    public static function employeDebutConge(Employe $employe, string $dateDebut, string $dateFin, int $utilisateurId): Notification
    {
        return self::creerPourUtilisateur(
            $utilisateurId,
            'employe',
            'Employé en congé',
            "{$employe->nom_complet} est en congé du {$dateDebut} au {$dateFin}.",
            $employe->id,
            $employe->tenant_id
        );
    }

    /**
     * À appeler quand un congé se termine et que l'employé redevient actif
     * (ex: dans employes:synchroniser-statuts, étape "retour de congé").
     */
    public static function employeRetourConge(Employe $employe, int $utilisateurId): Notification
    {
        return self::creerPourUtilisateur(
            $utilisateurId,
            'employe',
            'Retour de congé',
            "{$employe->nom_complet} est de retour de congé.",
            $employe->id,
            $employe->tenant_id
        );
    }

    // ------------------------------------------------------------------
    // Lecture — utilisées par le topbar (NotificationsComposer) et le
    // dashboard, pour garantir qu'ils affichent exactement la même chose.
    // ------------------------------------------------------------------

    /**
     * Dernières notifications d'un utilisateur (limité, pour l'affichage).
     */
    public static function recentes(int $utilisateurId, int $limite = 5)
    {
        return Notification::where('utilisateur_id', $utilisateurId)
            ->latest('date_reception')
            ->take($limite)
            ->get();
    }

    /**
     * Nombre total de notifications non lues d'un utilisateur.
     *
     * Volontairement séparé de recentes() : recentes() est limité, donc
     * compter les non-lues dedans ne reflèterait pas le vrai total si
     * l'utilisateur a plus de $limite notifications non lues.
     */
    public static function compterNonLues(int $utilisateurId): int
    {
        return Notification::where('utilisateur_id', $utilisateurId)
            ->where('lue', false)
            ->count();
    }
}

<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Contrat;
use App\Models\Employe;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Création générique. tenant_id et utilisateur_id sont toujours fixés
     * à 1 (mono-tenant / mono-utilisateur pour l'instant), comme partout
     * ailleurs dans le projet (EmployeController, ContratController...).
     */
    public static function creer(
        string $type,
        string $titre,
        string $message
    ): Notification {

        return Notification::create([
            'tenant_id'      => 1,
            'utilisateur_id' => 1,
            'type'           => $type,
            'titre'          => $titre,
            'message'        => $message,
            'lue'            => false,
            'date_reception' => now(),
        ]);
    }

    /**
     * À appeler depuis CongeController::store() (ou l'équivalent) juste
     * après la création d'un congé.
     */
    public static function employeEnConge(Employe $employe, string $dateDebut, string $dateFin): Notification
    {
        return self::creer(
            'alerte_rh',
            'Employé en congé',
            "{$employe->nom_complet} est en congé du {$dateDebut} au {$dateFin}."
        );
    }

    /**
     * Appelée automatiquement par la commande planifiée
     * notifications:verifier-contrats (voir plus bas).
     */
    public static function contratExpireBientot(Contrat $contrat, int $joursRestants): Notification
    {
        $dateFin = $contrat->date_fin ? Carbon::parse($contrat->date_fin) : null;
        $dateFinFormatee = $dateFin ? $dateFin->format('Y-m-d') : 'date non définie';

        return self::creer(
            'contrat',
            'Contrat arrivant à expiration',
            "Le contrat {$contrat->numcontrat} de {$contrat->employe->nom_complet} expire dans {$joursRestants} jours (le {$dateFinFormatee})."
        );
    }

    /**
     * À appeler depuis FactureController::store() juste après la création
     * d'une facture. Adapte "$facture->numero" au vrai nom de colonne chez
     * toi si besoin.
     */
    public static function nouvelleFacture($facture): Notification
    {
        return self::creer(
            'facture',
            'Nouvelle facture disponible',
            "La facture #{$facture->numero} est disponible."
        );
    }

    /**
     * À appeler partout où un document est mis à disposition (upload de
     * fiche de paie, contrat signé, etc.).
     */
    public static function nouveauDocument(string $nomDocument): Notification
    {
        return self::creer(
            'document',
            'Nouveau document disponible',
            "Le document \"{$nomDocument}\" est désormais disponible."
        );
    }

    /**
     * À appeler depuis SalaireController::payer() / payerTous() juste
     * après le paiement effectif.
     */
    public static function paiementEffectue($salaire): Notification
    {
        return self::creer(
            'facture',
            'Notification de paiement',
            "Le salaire de {$salaire->employe->nom_complet} pour la période {$salaire->periode} a été payé."
        );
    }

    /**
     * Pour toute alerte administrative libre (maintenance, changement de
     * politique interne, rappel...). À appeler manuellement où besoin.
     */
    public static function alerteAdministrative(string $titre, string $message): Notification
    {
        return self::creer('administrative', $titre, $message);
    }
}

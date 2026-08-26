<?php

namespace App\View\Composers;

use App\Services\NotificationService;
use Illuminate\View\View;

class NotificationsComposer
{
    /**
     * Alimente le topbar avec les 5 dernières notifications de
     * l'utilisateur connecté — exactement la même source de données
     * (NotificationService::recentes) que celle utilisée par
     * DashboardController, pour garantir un affichage identique partout.
     */
    public function compose(View $view): void
    {
        $utilisateurId = current_utilisateur_id();

        $notifications = $utilisateurId
            ? NotificationService::recentes($utilisateurId, 5)
            : collect();

        // Compteur réel des non-lues, indépendant de la limite de 5 utilisée
        // par "recentes" — c'est lui qui pilote l'affichage du badge rouge
        // dans le topbar (voir topbar.blade.php).
        $notificationsNonLues = $utilisateurId
            ? NotificationService::compterNonLues($utilisateurId)
            : 0;

        $view->with('notifications', $notifications);
        $view->with('notificationsNonLues', $notificationsNonLues);
    }
}

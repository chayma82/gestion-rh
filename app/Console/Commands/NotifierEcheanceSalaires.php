<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\ParametrePaie;
use App\Models\Utilisateur;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifierEcheanceSalaires extends Command
{
    protected $signature = 'salaires:notifier-echeance';

    protected $description = "Notifie les utilisateurs avant le jour de paiement des salaires configuré (J-7, J-2, jour J).";

    public function handle(): int
    {
        $aujourdhui = Carbon::today();
        $periode    = $aujourdhui->format('Y-m');

        foreach (ParametrePaie::all() as $parametrePaie) {

            $jourPaiement = $parametrePaie->jour_paiement ?? 3;

            // Date de paiement du mois courant (bornée à la fin du mois si
            // jour_paiement dépasse le nombre de jours du mois, ex: 31 en
            // février).
            $datePaiement = $aujourdhui->copy()->day(min($jourPaiement, $aujourdhui->daysInMonth));

            // Si la date de paiement de ce mois est déjà passée, rien à
            // notifier pour ce tenant aujourd'hui (le prochain jalon sera
            // celui du mois suivant, recalculé automatiquement le mois
            // prochain par cette même commande).
            if ($datePaiement->isPast() && !$datePaiement->isToday()) {
                continue;
            }

            $joursRestants = $aujourdhui->diffInDays($datePaiement);

            if (!in_array($joursRestants, [7, 2, 0], true)) {
                continue;
            }

            // Anti-doublon : si la commande tourne plusieurs fois le même
            // jour, on ne renotifie pas. On se base sur le titre (fixe) plutôt
            // que sur le message (qui varie selon joursRestants) pour rester
            // simple et fiable.
            $dejaNotifie = Notification::where('tenant_id', $parametrePaie->tenant_id)
                ->where('type', 'facture')
                ->where('titre', 'like', 'Paiement des salaires%')
                ->whereDate('date_reception', $aujourdhui)
                ->exists();

            if ($dejaNotifie) {
                continue;
            }

            // Seuls les utilisateurs ayant acces_facturation (ou
            // acces_admin) sont notifiés du paiement des salaires.
            $destinataires = Utilisateur::destinatairesNotification($parametrePaie->tenant_id, 'facture');

            foreach ($destinataires as $utilisateurId) {
                NotificationService::salairePaiementProche($parametrePaie->tenant_id, $periode, $joursRestants, $utilisateurId);
            }

            $libelle = $joursRestants === 0 ? "aujourd'hui" : "J-{$joursRestants}";
            $this->info("Tenant #{$parametrePaie->tenant_id} : notification paiement salaires ({$libelle}) envoyée à {$destinataires->count()} destinataire(s).");
        }

        return self::SUCCESS;
    }
}

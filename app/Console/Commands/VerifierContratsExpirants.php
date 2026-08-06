<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrat;
use App\Models\Notification;
use App\Services\NotificationService;
use Carbon\Carbon;

class VerifierContratsExpirants extends Command
{
    protected $signature = 'notifications:verifier-contrats';

    protected $description = "Notifie les contrats actifs ou à venir qui expirent dans 30 ou 60 jours";

    public function handle()
    {
        foreach ([30, 60] as $jours) {

            $dateCible = Carbon::today()->addDays($jours);

            $contrats = Contrat::with('employe')
                ->whereIn('statut', ['actif', 'a_venir'])
                ->whereNotNull('date_fin')
                ->whereDate('date_fin', $dateCible)
                ->get();

            foreach ($contrats as $contrat) {

                // Anti-doublon : si la commande tourne plusieurs fois le même
                // jour (ou est relancée manuellement), on ne renotifie pas.
                // Pas de colonne dédiée dans la table notification, donc on
                // se base sur le contenu du message + la date de réception.
                $dejaNotifie = Notification::where('type', 'contrat')
                    ->where('message', 'like', "%{$contrat->numcontrat}%{$jours} jours%")
                    ->whereDate('date_reception', Carbon::today())
                    ->exists();

                if (!$dejaNotifie) {
                    NotificationService::contratExpireBientot($contrat, $jours);
                    $this->info("Notification créée : contrat {$contrat->numcontrat} ({$jours} jours).");
                }

            }

        }

        $this->info('Vérification des contrats expirants terminée.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrat;
use App\Models\Notification;
use App\Models\Utilisateur;
use App\Services\NotificationService;
use Carbon\Carbon;

class VerifierContratsExpirants extends Command
{
    protected $signature = 'notifications:verifier-contrats';

    protected $description = "Notifie les contrats actifs ou à venir qui expirent dans 30 ou 60 jours";

    public function handle()
    {
        // 60 et 30 jours avant l'échéance, puis le jour même (0 = aujourd'hui).
        foreach ([60, 30, 0] as $jours) {

            $dateCible = Carbon::today()->addDays($jours);

            $contrats = Contrat::with('employe')
                ->whereIn('statut', ['actif', 'a_venir'])
                ->whereNotNull('date_fin')
                ->whereDate('date_fin', $dateCible)
                ->get();

            foreach ($contrats as $contrat) {

                // Anti-doublon : si la commande tourne plusieurs fois le même
                // jour (ou est relancée manuellement), on ne renotifie pas.
                // On se base sur reference_id (= contrat->id) + la date du
                // jour : comme un contrat donné n'a qu'une seule date_fin,
                // il ne peut correspondre qu'à UN SEUL des trois paliers
                // (60/30/0) à un jour donné, donc pas besoin de distinguer
                // le palier dans la vérification.
                //
                // IMPORTANT : ce contrôle doit se faire AVANT la boucle sur
                // les destinataires, pas dedans — sinon dès qu'un premier
                // destinataire est notifié, les suivants seraient injustement
                // sautés au prochain passage de la commande le même jour.
                $dejaNotifie = Notification::where('type', 'contrat')
                    ->where('reference_id', $contrat->id)
                    ->whereDate('date_reception', Carbon::today())
                    ->exists();

                if ($dejaNotifie) {
                    continue;
                }

                // Seuls les utilisateurs ayant acces_rh (ou acces_admin)
                // sont notifiés d'un contrat qui expire bientôt.
                $destinataires = Utilisateur::destinatairesNotification($contrat->tenant_id, 'contrat');

                foreach ($destinataires as $utilisateurId) {
                    NotificationService::contratBientotExpire($contrat, $jours, $utilisateurId);
                }

                $libelle = $jours === 0 ? "aujourd'hui" : "{$jours} jours";
                $this->info("Notification créée : contrat {$contrat->numcontrat} ({$libelle}) pour {$destinataires->count()} destinataire(s).");
            }

        }

        $this->info('Vérification des contrats expirants terminée.');

        return self::SUCCESS;
    }
}

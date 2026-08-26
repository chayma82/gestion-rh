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
                // On se base maintenant sur reference_id (= contrat->id),
                // plus fiable qu'un "like" sur le message.
                //
                // IMPORTANT : ce contrôle doit se faire AVANT la boucle sur
                // les destinataires, pas dedans — sinon dès qu'un premier
                // destinataire est notifié, les suivants seraient injustement
                // sautés au prochain passage de la commande le même jour.
                $dejaNotifie = Notification::where('type', 'contrat')
                    ->where('reference_id', $contrat->id)
                    ->where('message', 'like', "%{$jours} jours%")
                    ->whereDate('date_reception', Carbon::today())
                    ->exists();

                if ($dejaNotifie) {
                    continue;
                }

                // TODO : filtrer sur les utilisateurs RH/admin du tenant si
                // votre modèle Utilisateur expose un rôle/scope pour ça.
                // En l'état, on notifie tous les utilisateurs du tenant du
                // contrat (comportement precedent : un seul destinataire non
                // défini, ce qui plantait faute de paramètre).
                $destinataires = Utilisateur::where('tenant_id', $contrat->tenant_id)->pluck('id');

                foreach ($destinataires as $utilisateurId) {
                    NotificationService::contratBientotExpire($contrat, $jours, $utilisateurId);
                }

                $this->info("Notification créée : contrat {$contrat->numcontrat} ({$jours} jours) pour {$destinataires->count()} destinataire(s).");
            }

        }

        $this->info('Vérification des contrats expirants terminée.');

        return self::SUCCESS;
    }
}

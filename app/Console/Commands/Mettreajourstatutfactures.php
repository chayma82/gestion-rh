<?php

namespace App\Console\Commands;

use App\Models\FactureAchat;
use App\Models\FactureVente;
use App\Models\Utilisateur;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MettreAJourStatutFactures extends Command
{
    /**
     * Nom et signature de la commande.
     */
    protected $signature = 'factures:maj-statuts';

    /**
     * Description affichée dans "php artisan list".
     */
    protected $description = 'Passe automatiquement en "en_retard" les factures (achats et ventes) dont l\'échéance est dépassée';

    public function handle()
    {
        $aujourdhui = Carbon::today();

        // ---- Factures d'achat ----
        $facturesAchat = FactureAchat::whereNotIn('statut', ['payee', 'archive', 'en_retard'])
            ->whereNotNull('date_echeance')
            ->where('date_echeance', '<', $aujourdhui)
            ->get();

        foreach ($facturesAchat as $facture) {
            $facture->update(['statut' => 'en_retard']);
            $this->notifierRetard($facture, 'achat');
        }

        // ---- Factures de vente ----
        $facturesVente = FactureVente::whereNotIn('statut', ['payee', 'archive', 'en_retard'])
            ->whereNotNull('date_echeance')
            ->where('date_echeance', '<', $aujourdhui)
            ->get();

        foreach ($facturesVente as $facture) {
            $facture->update(['statut' => 'en_retard']);
            $this->notifierRetard($facture, 'vente');
        }

        $nombreAchat = $facturesAchat->count();
        $nombreVente = $facturesVente->count();

        $this->info($nombreAchat . ' facture(s) d\'achat passée(s) en retard.');
        $this->info($nombreVente . ' facture(s) de vente passée(s) en retard.');

        Log::info('[factures:maj-statuts] ' . $nombreAchat . ' achat(s) + ' . $nombreVente . ' vente(s) mise(s) à jour le ' . now());

        return self::SUCCESS;
    }

    /**
     * Notifie les utilisateurs du tenant qu'une facture vient de passer en
     * retard — avec une action différente selon le sens du flux :
     *   - vente : c'est le client qui nous doit de l'argent -> le relancer
     *   - achat : c'est nous qui devons de l'argent au fournisseur -> payer
     *
     * Pas d'anti-doublon nécessaire : une fois "en_retard", la facture sort
     * des requêtes ci-dessus et n'est plus jamais re-traitée.
     *
     * TODO : filtrer sur les utilisateurs RH/compta/admin du tenant si votre
     * modèle Utilisateur expose un rôle/scope pour ça.
     */
    protected function notifierRetard($facture, string $type): void
    {
        if (!$facture->tenant_id) {
            return;
        }

        $destinataires = Utilisateur::where('tenant_id', $facture->tenant_id)->pluck('id');

        foreach ($destinataires as $utilisateurId) {
            if ($type === 'vente') {
                NotificationService::factureVenteARelancer($facture, $utilisateurId);
            } else {
                NotificationService::factureAchatAPayer($facture, $utilisateurId);
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\FactureAchat;
use App\Models\FactureVente;
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
        }

        // ---- Factures de vente ----
        $facturesVente = FactureVente::whereNotIn('statut', ['payee', 'archive', 'en_retard'])
            ->whereNotNull('date_echeance')
            ->where('date_echeance', '<', $aujourdhui)
            ->get();

        foreach ($facturesVente as $facture) {
            $facture->update(['statut' => 'en_retard']);
        }

        $nombreAchat = $facturesAchat->count();
        $nombreVente = $facturesVente->count();

        $this->info($nombreAchat . ' facture(s) d\'achat passée(s) en retard.');
        $this->info($nombreVente . ' facture(s) de vente passée(s) en retard.');

        Log::info('[factures:maj-statuts] ' . $nombreAchat . ' achat(s) + ' . $nombreVente . ' vente(s) mise(s) à jour le ' . now());

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\FactureAchat;
use App\Models\FactureVente;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ReconcilierStatutsFactures extends Command
{
    /**
     * Nom et signature de la commande.
     * --dry-run : affiche les changements sans les appliquer.
     */
    protected $signature = 'factures:reconcilier-statuts {--dry-run : Afficher les changements sans les appliquer}';

    /**
     * Description affichée dans "php artisan list".
     */
    protected $description = "Recalcule le statut de chaque facture (achats et ventes) à partir de l'échéance réelle, pour corriger les statuts faussés par une saisie manuelle incorrecte (ancien bug du select 'En retard').";

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $aujourdhui = Carbon::today();
        $corrections = 0;

        foreach (['achat' => FactureAchat::class, 'vente' => FactureVente::class] as $type => $modele) {

            // On ne touche jamais aux factures payées ou archivées :
            // ce sont des statuts finaux décidés par une action explicite de l'utilisateur.
            $factures = $modele::whereNotIn('statut', ['payee', 'archive'])->get();

            foreach ($factures as $facture) {

                $bonStatut = ($facture->date_echeance && $facture->date_echeance->lt($aujourdhui))
                    ? 'en_retard'
                    : 'en_attente';

                if ($facture->statut !== $bonStatut) {
                    $this->line(sprintf(
                        "Facture %s #%d (%s) : statut '%s' -> '%s'",
                        $type,
                        $facture->id,
                        $facture->numFacture,
                        $facture->statut,
                        $bonStatut
                    ));

                    $corrections++;

                    if (!$dryRun) {
                        $facture->update(['statut' => $bonStatut]);
                    }
                }
            }
        }

        $this->info($dryRun
            ? "{$corrections} correction(s) détectée(s) (mode simulation, rien n'a été modifié)."
            : "{$corrections} correction(s) appliquée(s).");

        return self::SUCCESS;
    }
}

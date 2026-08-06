<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReconcilierStatuts extends Command
{
    protected $signature = 'contrats:reconcilier-statuts {--dry-run : Afficher les changements sans les appliquer}';

    protected $description = "Recalcule le statut de chaque contrat (et de l'employé associé) à partir des dates réelles, pour corriger les données faussées par un ancien bug.";

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $aujourdhui = now()->toDateString();

        $contrats = Contrat::with('employe')->get();
        $corrections = 0;

        foreach ($contrats as $contrat) {

            // On ne touche pas aux contrats déjà résiliés manuellement
            if ($contrat->statut === 'resilie') {
                continue;
            }

            if ($contrat->date_debut->toDateString() > $aujourdhui) {
                $bonStatutContrat = 'a_venir';
                $bonStatutEmploye = 'attente_prise_poste';
            } elseif ($contrat->date_fin && $contrat->date_fin->toDateString() < $aujourdhui) {
                $bonStatutContrat = 'expire';
                $bonStatutEmploye = 'fin_contrat';
            } else {
                $bonStatutContrat = 'actif';
                $bonStatutEmploye = 'actif';
            }

            $employe = $contrat->employe;

            $statutContratAChanger = $contrat->statut !== $bonStatutContrat;

            // On ne corrige le statut de l'employé que s'il est actuellement
            // dans un état "piloté par le contrat" (on ne veut pas écraser
            // en_conge / suspendu / demissionnaire mis à jour manuellement).
            $etatsPilotesParContrat = ['attente_contrat', 'attente_prise_poste', 'actif', 'fin_contrat'];
            $statutEmployeAChanger = $employe
                && in_array($employe->statutEmploye, $etatsPilotesParContrat)
                && $employe->statutEmploye !== $bonStatutEmploye;

            if ($statutContratAChanger || $statutEmployeAChanger) {

                $this->line(sprintf(
                    "Contrat #%d (%s) : statut '%s' -> '%s'%s",
                    $contrat->id,
                    $contrat->numcontrat,
                    $contrat->statut,
                    $bonStatutContrat,
                    $statutEmployeAChanger
                        ? " | Employé #{$employe->id} : statutEmploye '{$employe->statutEmploye}' -> '{$bonStatutEmploye}'"
                        : ''
                ));

                $corrections++;

                if (!$dryRun) {
                    DB::transaction(function () use ($contrat, $bonStatutContrat, $employe, $bonStatutEmploye, $statutEmployeAChanger) {
                        $contrat->update(['statut' => $bonStatutContrat]);

                        if ($statutEmployeAChanger) {
                            $employe->update(['statutEmploye' => $bonStatutEmploye]);
                        }
                    });
                }
            }
        }

        $this->info($dryRun
            ? "{$corrections} correction(s) détectée(s) (mode simulation, rien n'a été modifié)."
            : "{$corrections} correction(s) appliquée(s).");

        return self::SUCCESS;
    }
}

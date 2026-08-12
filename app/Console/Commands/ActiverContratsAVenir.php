<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ActiverContratsAVenir extends Command
{
    /**
     * Nom et signature de la commande.
     */
    protected $signature = 'contrats:actualiser
        {--dry-run : Simule l\'exécution sans écrire en base}';

    /**
     * Description affichée dans `php artisan list`.
     */
    protected $description = "Active les contrats 'a_venir' dont la date de début est atteinte, "
        . "et expire les contrats 'actif' dont la date de fin est dépassée. "
        . "Met à jour le statutEmploye en conséquence.";

    public function handle(): int
    {
        $today   = Carbon::today();
        $dryRun  = (bool) $this->option('dry-run');

        $activated = 0;
        $expired   = 0;

        DB::beginTransaction();

        try {
            /*
            |----------------------------------------------------------------
            | 1. Activer les contrats "a_venir" arrivés à échéance
            |----------------------------------------------------------------
            */
            $contratsAActiver = Contrat::where('statut', 'a_venir')
                ->whereDate('date_debut', '<=', $today)
                ->with('employe')
                ->get();

            foreach ($contratsAActiver as $contrat) {
                $this->line("Activation contrat #{$contrat->id} ({$contrat->numcontrat}) - employé #{$contrat->employe_id}");

                if (!$dryRun) {
                    $contrat->statut = 'actif';
                    $contrat->save();

                    if ($contrat->employe) {
                        $contrat->employe->statutEmploye = 'actif';
                        $contrat->employe->save();
                    }
                }

                $activated++;
            }

            /*
            |----------------------------------------------------------------
            | 2. Expirer les contrats "actif" dont la date de fin est passée
            |----------------------------------------------------------------
            */
            $contratsAExpirer = Contrat::where('statut', 'actif')
                ->whereNotNull('date_fin')
                ->whereDate('date_fin', '<', $today)
                ->with('employe')
                ->get();

            foreach ($contratsAExpirer as $contrat) {
                $this->line("Expiration contrat #{$contrat->id} ({$contrat->numcontrat}) - employé #{$contrat->employe_id}");

                if (!$dryRun) {
                    $contrat->statut = 'expire';
                    $contrat->save();

                    if ($contrat->employe) {
                        // On ne force le statut employé que s'il n'a pas
                        // un autre contrat actif ou à venir en parallèle.
                        $autreContratActif = Contrat::where('employe_id', $contrat->employe_id)
                            ->whereIn('statut', ['actif', 'a_venir'])
                            ->where('id', '!=', $contrat->id)
                            ->exists();

                        if (!$autreContratActif) {
                            $contrat->employe->statutEmploye = 'fin_contrat';
                            $contrat->employe->save();
                        }
                    }
                }

                $expired++;
            }

            if ($dryRun) {
                DB::rollBack();
                $this->warn("Mode simulation (--dry-run) : aucune modification enregistrée.");
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur dans ActiverContratsAVenir: ' . $e->getMessage());
            $this->error('Une erreur est survenue : ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("Terminé : {$activated} contrat(s) activé(s), {$expired} contrat(s) expiré(s).");

        return self::SUCCESS;
    }
}

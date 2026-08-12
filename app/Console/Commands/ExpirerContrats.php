<?php

namespace App\Console\Commands;

use App\Models\Contrat;
use Illuminate\Console\Command;

class ExpirerContrats extends Command
{
    protected $signature = 'contrats:expirer';
    protected $description = 'Passe en "expire" les contrats dont la date de fin est dépassée, et met à jour le statut des employés concernés.';

    public function handle()
    {
        $contrats = Contrat::whereIn('statut', ['actif', 'a_venir'])
            ->whereNotNull('date_fin')
            ->whereDate('date_fin', '<', now())
            ->get();

        foreach ($contrats as $contrat) {
            $contrat->update(['statut' => 'expire']);

            $employe = $contrat->employe;

            if ($employe && $employe->statutEmploye !== 'archive') {
                $employe->update(['statutEmploye' => 'fin_contrat']);
            }

            $this->line("Contrat #{$contrat->numcontrat} → expire ; employé #{$employe?->matricule} → fin_contrat");
        }

        $this->info("{$contrats->count()} contrat(s) expiré(s) traité(s).");
    }
}

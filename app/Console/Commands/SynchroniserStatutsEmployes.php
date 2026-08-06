<?php
// app/Console/Commands/SynchroniserStatutsEmployes.php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SynchroniserStatutsEmployes extends Command
{
    protected $signature = 'employes:synchroniser-statuts';

    protected $description = "Met à jour le statut des contrats (a_venir -> actif -> expire) et le statut des employés en conséquence.";

    public function handle()
    {
        $aujourdhui = Carbon::today();

        // 1. Contrats "a_venir" dont la date de début est arrivée -> "actif"
        Contrat::where('statut', 'a_venir')
            ->whereDate('date_debut', '<=', $aujourdhui)
            ->get()
            ->each(function (Contrat $contrat) {

                $contrat->update(['statut' => 'actif']);

                $employe = $contrat->employe;

                if ($employe && $employe->statutEmploye === 'attente_prise_poste') {
                    $employe->update(['statutEmploye' => 'actif']);
                }
            });

        // 2. Contrats "actif" dont la date de fin est dépassée -> "expire"
        Contrat::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->whereDate('date_fin', '<', $aujourdhui)
            ->get()
            ->each(function (Contrat $contrat) {

                $contrat->update(['statut' => 'expire']);

                $employe = $contrat->employe;

                if ($employe && !in_array($employe->statutEmploye, ['archive', 'demissionnaire'])) {
                    $employe->update(['statutEmploye' => 'fin_contrat']);
                }
            });

        // 3. Congés en cours aujourd'hui -> employé "en_conge"
        Employe::whereHas('conges', function ($q) use ($aujourdhui) {
                $q->whereDate('date_debut', '<=', $aujourdhui)
                  ->whereDate('date_fin', '>=', $aujourdhui);
            })
            ->where('statutEmploye', 'actif')
            ->get()
            ->each(function (Employe $employe) {
                $employe->update(['statutEmploye' => 'en_conge']);
            });

        // 4. Congés terminés -> retour à "actif" si le contrat l'est toujours
        Employe::where('statutEmploye', 'en_conge')
            ->whereDoesntHave('conges', function ($q) use ($aujourdhui) {
                $q->whereDate('date_debut', '<=', $aujourdhui)
                  ->whereDate('date_fin', '>=', $aujourdhui);
            })
            ->get()
            ->each(function (Employe $employe) {
                $employe->update([
                    'statutEmploye' => $employe->contratActif ? 'actif' : 'fin_contrat',
                ]);
            });

        $this->info('Statuts des contrats et employés synchronisés.');
    }
}

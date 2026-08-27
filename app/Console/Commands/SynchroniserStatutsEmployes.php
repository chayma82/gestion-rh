<?php
// app/Console/Commands/SynchroniserStatutsEmployes.php

namespace App\Console\Commands;

use App\Models\Contrat;
use App\Models\Employe;
use App\Models\Utilisateur;
use App\Services\NotificationService;
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
            ->with(['conges' => function ($q) use ($aujourdhui) {
                $q->whereDate('date_debut', '<=', $aujourdhui)
                  ->whereDate('date_fin', '>=', $aujourdhui);
            }])
            ->where('statutEmploye', 'actif')
            ->get()
            ->each(function (Employe $employe) {

                $employe->update(['statutEmploye' => 'en_conge']);

                // Pas d'anti-doublon nécessaire : une fois passé à "en_conge",
                // l'employé sort de cette requête (elle filtre sur
                // statutEmploye = 'actif') et n'est plus re-traité tant qu'il
                // n'est pas revenu actif entre-temps.
                // TODO : filtrer sur les utilisateurs RH/admin du tenant si
                // votre modèle Utilisateur expose un rôle/scope pour ça.
                $conge = $employe->conges->first();

                if ($conge) {
                    $destinataires = Utilisateur::where('tenant_id', $employe->tenant_id)->pluck('id');

                    foreach ($destinataires as $utilisateurId) {
                        NotificationService::employeDebutConge(
                            $employe,
                            Carbon::parse($conge->date_debut)->format('Y-m-d'),
                            Carbon::parse($conge->date_fin)->format('Y-m-d'),
                            $utilisateurId
                        );
                    }
                }
            });

        // 4. Congés terminés -> retour à "actif" si le contrat l'est toujours
        //    On notifie le retour de congé UNIQUEMENT quand l'employé
        //    redevient effectivement "actif" (pas s'il tombe en fin_contrat,
        //    ce qui ne serait pas vraiment un "retour").
        Employe::where('statutEmploye', 'en_conge')
            ->whereDoesntHave('conges', function ($q) use ($aujourdhui) {
                $q->whereDate('date_debut', '<=', $aujourdhui)
                  ->whereDate('date_fin', '>=', $aujourdhui);
            })
            ->get()
            ->each(function (Employe $employe) {

                $nouveauStatut = $employe->contratActif ? 'actif' : 'fin_contrat';

                $employe->update(['statutEmploye' => $nouveauStatut]);

                if ($nouveauStatut === 'actif') {
                    // Pas d'anti-doublon nécessaire : une fois sorti de
                    // "en_conge", l'employé ne sera plus re-traité par cette
                    // requête tant qu'il ne repart pas en congé.
                    // TODO : filtrer sur les utilisateurs RH/admin du tenant
                    // si votre modèle Utilisateur expose un rôle/scope pour ça.
                    $destinataires = Utilisateur::where('tenant_id', $employe->tenant_id)->pluck('id');

                    foreach ($destinataires as $utilisateurId) {
                        NotificationService::employeRetourConge($employe, $utilisateurId);
                    }
                }
            });

        $this->info('Statuts des contrats et employés synchronisés.');
    }
}

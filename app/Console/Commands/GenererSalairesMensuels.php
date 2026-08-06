<?php

namespace App\Console\Commands;

use App\Models\Employe;
use App\Models\ParametrePaie;
use App\Models\Salaire;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenererSalairesMensuels extends Command
{
    /**
     * Nom et signature de la commande.
     * --force permet de générer même si aujourd'hui n'est pas le jour de paie
     * configuré (utile pour tester manuellement).
     */
    protected $signature = 'salaires:generer-mensuel {--force : Générer même si ce n\'est pas le jour de paie configuré}';

    /**
     * Description affichée dans "php artisan list".
     */
    protected $description = "Génère automatiquement la fiche de salaire du mois pour chaque employé sous contrat actif, le jour de paie configuré.";

    public function handle(): int
    {
        $tenantId = 1;

        $parametrePaie = ParametrePaie::where('tenant_id', $tenantId)->first();
        $jourPaiement  = $parametrePaie->jour_paiement ?? 3;

        $aujourdhui = Carbon::today();

        if (!$this->option('force') && $aujourdhui->day !== $jourPaiement) {
            $this->info("Aujourd'hui ({$aujourdhui->day}) n'est pas le jour de paie configuré ({$jourPaiement}). Rien à faire.");
            return self::SUCCESS;
        }

        $periode = $aujourdhui->format('Y-m');
        $nombreCrees = 0;

        // Employés ayant un contrat actif, avec ce contrat préchargé
        $employes = Employe::whereHas('contrats', fn ($q) => $q->where('statut', 'actif'))
            ->with(['contrats' => fn ($q) => $q->where('statut', 'actif')->latest('date_debut')])
            ->get();

        foreach ($employes as $employe) {

            $contrat = $employe->contrats->first();

            if (!$contrat) {
                continue;
            }

            // On ne recrée jamais une fiche déjà existante pour ce mois
            $existeDeja = Salaire::where('employe_id', $employe->id)
                ->where('contrat_id', $contrat->id)
                ->where('periode', $periode)
                ->exists();

            if ($existeDeja) {
                continue;
            }

            // Salaire de base repris du dernier bulletin connu (sinon 0)
            $dernierSalaire = $employe->salaires()->latest('periode')->first();

            Salaire::create([
                'tenant_id'     => $tenantId,
                'employe_id'    => $employe->id,
                'contrat_id'    => $contrat->id,
                'periode'       => $periode,
                'salaire_brut'  => $dernierSalaire->salaire_brut ?? 0,
                'total_primes'  => 0,
                'total_avances' => 0,
                'statut'        => 'en_attente',
            ]);

            $nombreCrees++;
        }

        $this->info("{$nombreCrees} fiche(s) de salaire générée(s) pour la période {$periode}.");

        return self::SUCCESS;
    }
}

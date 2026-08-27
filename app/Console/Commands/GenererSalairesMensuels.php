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
    protected $description = "Génère automatiquement la fiche de salaire du mois pour chaque employé sous contrat actif, le jour de paie configuré, avec calcul au prorata si le contrat ne couvre pas le mois entier.";

    public function handle(): int
    {
        $aujourdhui = Carbon::today();
        $periode    = $aujourdhui->format('Y-m');
        $totalCrees = 0;

        // BUG CORRIGÉ : le tenant_id était codé en dur à 1, donc cette
        // commande ne générait jamais rien pour les autres tenants. On
        // boucle maintenant sur tous les tenants qui ont au moins un
        // employé, chacun avec son propre jour de paiement configuré.
        $tenantIds = Employe::query()->distinct()->pluck('tenant_id');

        foreach ($tenantIds as $tenantId) {

            $parametrePaie = ParametrePaie::where('tenant_id', $tenantId)->first();
            $jourPaiement  = $parametrePaie->jour_paiement ?? 3;

            if (!$this->option('force') && $aujourdhui->day !== $jourPaiement) {
                continue;
            }

            $totalCrees += $this->genererPourTenant($tenantId, $periode, $aujourdhui);
        }

        $this->info("{$totalCrees} fiche(s) de salaire générée(s) au total pour la période {$periode}.");

        return self::SUCCESS;
    }

    protected function genererPourTenant(int $tenantId, string $periode, Carbon $aujourdhui): int
    {
        $nombreCrees = 0;

        $debutMois      = $aujourdhui->copy()->startOfMonth();
        $finMois        = $aujourdhui->copy()->endOfMonth();
        $joursDansMois  = $debutMois->daysInMonth;

        // Employés de ce tenant ayant un contrat actif, avec ce contrat préchargé
        $employes = Employe::where('tenant_id', $tenantId)
            ->whereHas('contrats', fn ($q) => $q->where('statut', 'actif'))
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

            /*
             * Calcul au prorata si le contrat ne couvre pas le mois entier
             * (embauche en cours de mois, ou fin de contrat en cours de
             * mois) :
             *
             *   salaire_brut = (salaire_base / nb_jours_du_mois) * jours_travaillés
             *
             * Sinon (contrat qui couvre tout le mois), on prend simplement
             * le salaire_base du contrat.
             */
            $debutPeriode = Carbon::parse($contrat->date_debut)->max($debutMois);
            $finPeriode   = $contrat->date_fin
                ? Carbon::parse($contrat->date_fin)->min($finMois)
                : $finMois;

            $joursTravailles = $debutPeriode->lte($finPeriode)
                ? $debutPeriode->diffInDays($finPeriode) + 1
                : 0;

            $salaireBase = (float) ($contrat->salaire_base ?? 0);

            $salaireBrut = ($joursTravailles >= $joursDansMois)
                ? $salaireBase
                : round(($salaireBase / $joursDansMois) * $joursTravailles, 2);

            Salaire::create([
                'tenant_id'     => $tenantId,
                'employe_id'    => $employe->id,
                'contrat_id'    => $contrat->id,
                'periode'       => $periode,
                'salaire_brut'  => $salaireBrut,
                'total_primes'  => 0,
                'total_avances' => 0,
                'statut'        => 'en_attente',
            ]);

            $prorata = $joursTravailles < $joursDansMois
                ? " (prorata {$joursTravailles}/{$joursDansMois} jours)"
                : '';

            $this->line("Salaire créé : {$employe->nom_complet} — {$salaireBrut} DT{$prorata}");

            $nombreCrees++;
        }

        if ($nombreCrees > 0) {
            $this->info("Tenant #{$tenantId} : {$nombreCrees} fiche(s) générée(s).");
        }

        return $nombreCrees;
    }
}
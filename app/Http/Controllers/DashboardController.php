<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Contrat;
use App\Models\Conge;
use App\Models\Salaire;
use App\Models\Prime;
use App\Models\AvanceSalaire;
use App\Models\FactureAchat;
use App\Models\FactureVente;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $entrepriseId = 1;
        $aujourdhui = Carbon::today();
        $periodeCourante = $aujourdhui->format('Y-m');
        $moisCourant = $aujourdhui->month;
        $anneeCourante = $aujourdhui->year;

        // ------------------------------------------------------------
        // Cartes statistiques RH
        // ------------------------------------------------------------

        // Total employés (on exclut les archivés, comme partout ailleurs
        // dans l'application - cf. EmployeController@index)
        $totalEmployes = Employe::where('statutEmploye', '!=', 'archive')
            ->orWhereNull('statutEmploye')
            ->count();

        // Contrats actifs
        $contratsActifs = Contrat::where('statut', 'actif')->count();

        // Employés actuellement en congé aujourd'hui (même logique que
        // CongesController::index).
        $congesAujourdhui = Conge::whereDate('date_debut', '<=', $aujourdhui)
            ->whereDate('date_fin', '>=', $aujourdhui)
            ->distinct('employe_id')
            ->count('employe_id');

        // Masse salariale du mois en cours (salaire_brut + primes - avances)
        $masseSalarialeMois = Salaire::where('periode', $periodeCourante)
            ->selectRaw('COALESCE(SUM(salaire_brut + total_primes - total_avances), 0) as total')
            ->value('total');

        // ------------------------------------------------------------
        // Cartes statistiques FACTURES (achats & ventes)
        // ------------------------------------------------------------

        $baseAchats = FactureAchat::actives()->where('entreprise_id', $entrepriseId);
        $baseVentes = FactureVente::actives()->where('entreprise_id', $entrepriseId);

        // Chiffre d'affaires du mois (ventes émises ce mois-ci)
        $caVentesMois = (clone $baseVentes)
            ->whereMonth('dateEmissionFacture', $moisCourant)
            ->whereYear('dateEmissionFacture', $anneeCourante)
            ->sum('montant_ttc');

        // Achats du mois (factures fournisseurs émises ce mois-ci)
        $achatsMois = (clone $baseAchats)
            ->whereMonth('dateEmissionFacture', $moisCourant)
            ->whereYear('dateEmissionFacture', $anneeCourante)
            ->sum('montant_ttc');

        // Montant à recevoir des clients (factures ventes non payées)
        $montantARecevoir = (clone $baseVentes)
            ->where('statut', '!=', 'payee')
            ->sum('montant_restant');

        // Montant à payer aux fournisseurs (factures achats non payées)
        // Pas de colonne montant_restant sur la table facture_achat,
        // on calcule donc montant_ttc - montant_paye directement en SQL.
        $montantAPayer = (clone $baseAchats)
            ->where('statut', '!=', 'payee')
            ->selectRaw('COALESCE(SUM(montant_ttc - montant_paye), 0) as total')
            ->value('total');

        // Nombre de factures en retard, tous types confondus
        $achatsEnRetard = (clone $baseAchats)->where('statut', 'en_retard')->count();
        $ventesEnRetard = (clone $baseVentes)->where('statut', 'en_retard')->count();
        $facturesEnRetardTotal = $achatsEnRetard + $ventesEnRetard;

        // ------------------------------------------------------------
        // Prochaines échéances (achats + ventes, non payées, triées par
        // date d'échéance la plus proche)
        // ------------------------------------------------------------
        $prochainesAchats = (clone $baseAchats)
            ->with('fournisseur')
            ->where('statut', '!=', 'payee')
            ->whereNotNull('date_echeance')
            ->orderBy('date_echeance')
            ->take(5)
            ->get()
            ->map(function ($f) {
                return [
                    'type'     => 'achat',
                    'numero'   => $f->numFacture,
                    'tiers'    => $f->fournisseur->nom ?? '—',
                    'montant'  => $f->montant_ttc,
                    'echeance' => $f->date_echeance,
                    'statut'   => $f->statut,
                    'lien'     => route('factures.achats.info', $f->id),
                ];
            });

        $prochainesVentes = (clone $baseVentes)
            ->where('statut', '!=', 'payee')
            ->whereNotNull('date_echeance')
            ->orderBy('date_echeance')
            ->take(5)
            ->get()
            ->map(function ($f) {
                return [
                    'type'     => 'vente',
                    'numero'   => $f->numFacture,
                    'tiers'    => $f->nom_client,
                    'montant'  => $f->montant_ttc,
                    'echeance' => $f->date_echeance,
                    'statut'   => $f->statut,
                    'lien'     => route('factures.ventes.info', $f->id),
                ];
            });

        $prochainesEcheances = $prochainesAchats
            ->concat($prochainesVentes)
            ->sortBy('echeance')
            ->take(5)
            ->values();

        // ------------------------------------------------------------
        // Croissance employés par mois (10 derniers mois)
        // ------------------------------------------------------------
        $croissance = [];

        for ($i = 9; $i >= 0; $i--) {

            $date = Carbon::now()->subMonths($i);

            $croissance[] = [
                'label' => $date->translatedFormat('M'),
                'valeur' => Employe::whereYear('date_creation', $date->year)
                    ->whereMonth('date_creation', $date->month)
                    ->count(),
            ];
        }

        // ------------------------------------------------------------
        // Notifications dynamiques (calculées sur les vraies données,
        // et plus figées en dur)
        // ------------------------------------------------------------
        $notifications = [];

        // Nouveaux employés ce mois
        $nouveauxEmployes = Employe::whereMonth('date_creation', now()->month)
            ->whereYear('date_creation', now()->year)
            ->count();

        if ($nouveauxEmployes > 0) {
            $notifications[] = [
                'icon'   => 'fa-user-plus',
                'color'  => 'bg-orange-50 text-[#E2721B]',
                'titre'  => 'Nouveaux employés',
                'texte'  => "{$nouveauxEmployes} employé(s) ajouté(s) ce mois",
                'temps'  => 'Ce mois',
            ];
        }

        // Contrats actifs
        $notifications[] = [
            'icon'   => 'fa-file-contract',
            'color'  => 'bg-blue-50 text-blue-600',
            'titre'  => 'Contrats actifs',
            'texte'  => "{$contratsActifs} contrat(s) actuellement actif(s)",
            'temps'  => "Aujourd'hui",
        ];

        // Contrats arrivant à échéance dans les 30 prochains jours
        $contratsAExpirer = Contrat::where('statut', 'actif')
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [$aujourdhui, $aujourdhui->copy()->addDays(30)])
            ->count();

        if ($contratsAExpirer > 0) {
            $notifications[] = [
                'icon'   => 'fa-triangle-exclamation',
                'color'  => 'bg-red-50 text-red-500',
                'titre'  => 'Contrats bientôt expirés',
                'texte'  => "{$contratsAExpirer} contrat(s) arrivent à échéance sous 30 jours",
                'temps'  => 'À surveiller',
            ];
        }

        // Congés qui démarrent demain
        $congesDemain = Conge::whereDate('date_debut', $aujourdhui->copy()->addDay())
            ->distinct('employe_id')
            ->count('employe_id');

        if ($congesDemain > 0) {
            $notifications[] = [
                'icon'   => 'fa-calendar-day',
                'color'  => 'bg-green-50 text-green-600',
                'titre'  => 'Congés à venir',
                'texte'  => "{$congesDemain} employé(s) commencent un congé demain",
                'temps'  => 'Demain',
            ];
        }

        // Salaires du mois en attente de paiement
        $salairesEnAttente = Salaire::where('periode', $periodeCourante)
            ->where('statut', 'en_attente')
            ->count();

        if ($salairesEnAttente > 0) {
            $notifications[] = [
                'icon'   => 'fa-sack-dollar',
                'color'  => 'bg-orange-50 text-[#E2721B]',
                'titre'  => 'Paiements en attente',
                'texte'  => "{$salairesEnAttente} salaire(s) en attente de paiement ce mois",
                'temps'  => 'Ce mois',
            ];
        }

        // Factures en retard (achats + ventes)
        if ($facturesEnRetardTotal > 0) {
            $detail = [];
            if ($achatsEnRetard > 0) {
                $detail[] = "{$achatsEnRetard} achat(s)";
            }
            if ($ventesEnRetard > 0) {
                $detail[] = "{$ventesEnRetard} vente(s)";
            }

            $notifications[] = [
                'icon'   => 'fa-file-invoice-dollar',
                'color'  => 'bg-red-50 text-red-500',
                'titre'  => 'Factures en retard',
                'texte'  => "{$facturesEnRetardTotal} facture(s) en retard (" . implode(' + ', $detail) . ')',
                'temps'  => 'À régler',
            ];
        }

        return view(
            'dashboard.Dashboard',
            compact(
                'totalEmployes',
                'contratsActifs',
                'congesAujourdhui',
                'masseSalarialeMois',
                'croissance',
                'notifications',
                'caVentesMois',
                'achatsMois',
                'montantARecevoir',
                'montantAPayer',
                'facturesEnRetardTotal',
                'prochainesEcheances'
            )
        );
    }
}

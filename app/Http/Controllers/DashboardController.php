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
use App\Services\NotificationService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $entrepriseId = current_entreprise_id();
        $tenantId = current_tenant_id();
        $aujourdhui = Carbon::today();
        $periodeCourante = $aujourdhui->format('Y-m');
        $moisCourant = $aujourdhui->month;
        $anneeCourante = $aujourdhui->year;

        // ------------------------------------------------------------
        // Cartes statistiques RH
        // ------------------------------------------------------------

        $totalEmployes = Employe::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('statutEmploye', '!=', 'archive')
                  ->orWhereNull('statutEmploye');
            })
            ->count();

        $contratsActifs = Contrat::where('tenant_id', $tenantId)
            ->where('statut', 'actif')
            ->count();

        $congesAujourdhui = Conge::where('tenant_id', $tenantId)
            ->whereDate('date_debut', '<=', $aujourdhui)
            ->whereDate('date_fin', '>=', $aujourdhui)
            ->distinct('employe_id')
            ->count('employe_id');

        $masseSalarialeMois = Salaire::where('tenant_id', $tenantId)
            ->where('periode', $periodeCourante)
            ->selectRaw('COALESCE(SUM(salaire_brut + total_primes - total_avances), 0) as total')
            ->value('total');

        // ------------------------------------------------------------
        // Cartes statistiques FACTURES (achats & ventes)
        // ------------------------------------------------------------

        $baseAchats = FactureAchat::actives()->where('entreprise_id', $entrepriseId);
        $baseVentes = FactureVente::actives()->where('entreprise_id', $entrepriseId);

        $caVentesMois = (clone $baseVentes)
            ->whereMonth('dateEmissionFacture', $moisCourant)
            ->whereYear('dateEmissionFacture', $anneeCourante)
            ->sum('montant_ttc');

        $achatsMois = (clone $baseAchats)
            ->whereMonth('dateEmissionFacture', $moisCourant)
            ->whereYear('dateEmissionFacture', $anneeCourante)
            ->sum('montant_ttc');

        $montantARecevoir = (clone $baseVentes)
            ->where('statut', '!=', 'payee')
            ->sum('montant_restant');

        $montantAPayer = (clone $baseAchats)
            ->where('statut', '!=', 'payee')
            ->selectRaw('COALESCE(SUM(montant_ttc - montant_paye), 0) as total')
            ->value('total');

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
                'valeur' => Employe::where('tenant_id', $tenantId)
                    ->whereYear('date_creation', $date->year)
                    ->whereMonth('date_creation', $date->month)
                    ->count(),
            ];
        }

        // ------------------------------------------------------------
        // Notifications : on utilise désormais les VRAIES notifications
        // persistées (table notification), les mêmes que celles affichées
        // dans le topbar (via NotificationsComposer). Fini le tableau
        // recalculé à la main ici, qui pouvait diverger de ce qu'affichait
        // le topbar.
        // ------------------------------------------------------------
        $utilisateurId = current_utilisateur_id();

        $notifications = $utilisateurId
            ? NotificationService::recentes($utilisateurId, 5)
            : collect();

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

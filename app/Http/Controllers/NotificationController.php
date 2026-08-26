<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NotificationController extends Controller
{
    /**
     * Page complète de l'historique des notifications.
     */
    public function index()
    {
        $utilisateurId = current_utilisateur_id();

        $notifications = Notification::where('utilisateur_id', $utilisateurId)
            ->latest('date_reception')
            ->paginate(20);

        return view('notifications.liste', compact('notifications'));
    }

    /**
     * Endpoint JSON léger : nombre de non-lues + dernières notifications.
     * Permet au topbar de se rafraîchir en AJAX (polling) sans recharger
     * toute la page, en complément du NotificationsComposer qui les injecte
     * déjà au premier chargement de chaque vue.
     */
    public function nonLues()
    {
        $utilisateurId = current_utilisateur_id();

        return response()->json([
            'nonLues'       => NotificationService::compterNonLues($utilisateurId),
            'notifications' => NotificationService::recentes($utilisateurId),
        ]);
    }

    /**
     * Sécurité : on ne peut marquer comme lue qu'une notification qui nous
     * appartient. Sans ce contrôle, n'importe quel utilisateur connecté
     * pouvait marquer comme lue la notification de n'importe qui d'autre
     * simplement en devinant/changeant l'id dans l'URL.
     */
    public function marquerLue(Request $request, Notification $notification)
    {
        if ($notification->utilisateur_id !== current_utilisateur_id()) {
            abort(403);
        }

        $notification->marquerCommeLue();

        // Le panneau du topbar appelle cette route en AJAX (fetch) pour
        // marquer une notification comme lue sans recharger la page.
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'nonLues' => NotificationService::compterNonLues($notification->utilisateur_id),
            ]);
        }

        return back();
    }

    public function marquerToutesLues(Request $request)
    {
        $utilisateurId = current_utilisateur_id();

        Notification::where('utilisateur_id', $utilisateurId)
            ->where('lue', false)
            ->update([
                'lue'          => true,
                'date_lecture' => now(),
            ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'nonLues' => 0]);
        }

        return back();
    }

    /**
     * Marque la notification comme lue PUIS redirige vers la ressource liée
     * (contrat, facture, employé...) grâce à type + reference_id, au lieu
     * de laisser l'utilisateur sur la liste après un clic.
     *
     * NB : les noms de routes ci-dessous ('employes.contrats.show', etc.)
     * sont à adapter à vos routes réelles — je n'ai pas votre fichier
     * routes/web.php sous les yeux. Route::has() évite un crash si l'une
     * d'elles n'existe pas encore : on retombe simplement sur la liste.
     */
    public function ouvrir(Notification $notification)
    {
        if ($notification->utilisateur_id !== current_utilisateur_id()) {
            abort(403);
        }

        if (!$notification->lue) {
            $notification->marquerCommeLue();
        }

        $route = match ($notification->type) {
            'contrat' => $notification->reference_id ? ['employes.contrats.show', $notification->reference_id] : null,
            'employe' => $notification->reference_id ? ['employes.show', $notification->reference_id] : null,
            'facture' => $notification->reference_id ? ['factures.ventes.show', $notification->reference_id] : null,
            default   => null,
        };

        if ($route && Route::has($route[0])) {
            return redirect()->route(...$route);
        }

        return redirect()->route('notifications.index');
    }
}

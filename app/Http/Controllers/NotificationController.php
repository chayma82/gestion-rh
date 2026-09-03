<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Utilisateur;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class NotificationController extends Controller
{
    /**
     * Types de notifications ('facture', 'employe', 'contrat') autorisés
     * pour l'utilisateur connecté, en fonction des accès de son rôle.
     *
     * NB : ceci filtre uniquement ce que voit ce contrôleur. Les méthodes
     * NotificationService::recentes() et ::compterNonLues() (utilisées par
     * nonLues() ci-dessous, pour le polling du topbar) doivent appliquer le
     * même filtre côté service, sinon le badge de notifications non lues
     * restera incorrect pour un RH par exemple.
     */
    private function typesAutorises(): array
    {
        $role = Utilisateur::find(current_utilisateur_id())?->role;

        if (!$role) {
            return [];
        }

        if ($role->acces_admin) {
            return ['facture', 'employe', 'contrat'];
        }

        $types = [];

        if ($role->acces_facturation) {
            $types[] = 'facture';
        }

        if ($role->acces_rh) {
            $types[] = 'employe';
            $types[] = 'contrat';
        }

        return $types;
    }

    /**
     * Page complète de l'historique des notifications.
     */
    public function index()
    {
        $utilisateurId = current_utilisateur_id();

        $notifications = Notification::where('utilisateur_id', $utilisateurId)
            ->whereIn('type', $this->typesAutorises())
            ->latest('date_reception')
            ->paginate(20);

        return view('notifications.liste', compact('notifications'));
    }

    /**
     * Renvoie les notifications non lues de l'utilisateur connecté, en JSON.
     * Utilisée par le polling JS du topbar (toutes les 15s) pour détecter
     * les nouvelles notifications créées en arrière-plan (commandes
     * planifiées) sans attendre un rechargement de page.
     *
     * NB : icon/couleur/diff sont pré-calculés ici car ce sont des
     * accesseurs Eloquent (getIconAttribute, getCouleurAttribute) qui ne
     * sont PAS inclus automatiquement dans la sérialisation JSON — il
     * faudrait les ajouter à $appends sur le modèle Notification pour que
     * $notification->toJson() les inclue tout seul.
     *
     * TODO : NotificationService::recentes() et ::compterNonLues() ne
     * filtrent pas encore par type autorisé. Le filter() ci-dessous corrige
     * la liste affichée, mais pas le compteur 'nonLues' qui peut donc
     * inclure des notifications que l'utilisateur ne devrait pas voir.
     */
    public function nonLues()
    {
        $utilisateurId = current_utilisateur_id();
        $typesAutorises = $this->typesAutorises();

        $notifications = NotificationService::recentes($utilisateurId, 5, $typesAutorises)
            ->map(function ($n) {
                return [
                    'id'      => $n->id,
                    'titre'   => $n->titre,
                    'message' => $n->message,
                    'lue'     => $n->lue,
                    'icon'    => $n->icon,
                    'couleur' => $n->couleur,
                    'diff'    => $n->date_reception?->diffForHumans(),
                ];
            });

        return response()->json([
            'nonLues'       => NotificationService::compterNonLues($utilisateurId, $typesAutorises),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Sécurité : on ne peut marquer comme lue qu'une notification qui nous
     * appartient ET dont le type est autorisé pour notre rôle. Sans ce
     * contrôle, n'importe quel utilisateur connecté pouvait marquer comme
     * lue la notification de n'importe qui d'autre simplement en
     * devinant/changeant l'id dans l'URL.
     */
    public function marquerLue(Request $request, Notification $notification)
    {
        if (
            $notification->utilisateur_id !== current_utilisateur_id()
            || !in_array($notification->type, $this->typesAutorises(), true)
        ) {
            abort(403);
        }

        $notification->marquerCommeLue();

        // Le panneau du topbar appelle cette route en AJAX (fetch) pour
        // marquer une notification comme lue sans recharger la page.
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'nonLues' => NotificationService::compterNonLues(
                    $notification->utilisateur_id,
                    $this->typesAutorises()
                ),
            ]);
        }

        return back();
    }

    public function marquerToutesLues(Request $request)
    {
        $utilisateurId = current_utilisateur_id();

        Notification::where('utilisateur_id', $utilisateurId)
            ->whereIn('type', $this->typesAutorises())
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
        if (
            $notification->utilisateur_id !== current_utilisateur_id()
            || !in_array($notification->type, $this->typesAutorises(), true)
        ) {
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

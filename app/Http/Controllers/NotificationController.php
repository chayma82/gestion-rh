<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $utilisateurId = 1;

        $notifications = Notification::where('utilisateur_id', $utilisateurId)
            ->latest('date_reception')
            ->paginate(20);

        return view('notifications.liste', compact('notifications'));
    }

    public function marquerLue(Notification $notification)
    {
        $notification->marquerCommeLue();

        return back();
    }

    public function marquerToutesLues()
    {
        $utilisateurId =  1;

        Notification::where('utilisateur_id', $utilisateurId)
            ->where('lue', false)
            ->update([
                'lue'          => true,
                'date_lecture' => now(),
            ]);

        return back();
    }
}

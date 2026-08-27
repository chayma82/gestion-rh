<?php

namespace App\Providers;

use App\Models\Utilisateur;
use App\Observers\UtilisateurObserver;
use App\View\Composers\NotificationsComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        
        // Envoie automatiquement le mail de confirmation dès qu'un
        // Utilisateur passe de actif=false à actif=true via Eloquent.
        Utilisateur::observe(UtilisateurObserver::class);

        // Alimente le topbar (notifications + badge non-lues) sur TOUTES
        // les pages qui l'incluent, pas seulement le Dashboard.
        //
        // 'layouts.topbar' correspond au chemin du fichier
        // resources/views/layouts/topbar.blade.php. Si votre topbar est
        // rangé ailleurs (ex: partials.topbar), adaptez le nom ci-dessous —
        // c'est le nom de vue tel qu'on l'utiliserait dans @include(...),
        // PAS le chemin de fichier avec des slashs.
        View::composer('partials.topbar', NotificationsComposer::class);
    }
}

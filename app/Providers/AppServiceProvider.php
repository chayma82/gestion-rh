<?php

namespace App\Providers;

use App\Models\Utilisateur;
use App\Observers\UtilisateurObserver;
use Illuminate\Support\ServiceProvider;

/**
 * Si votre AppServiceProvider.php existant contient déjà d'autres
 * bindings dans register() ou boot(), NE REMPLACEZ PAS le fichier :
 * ajoutez seulement les 2 imports (use ...) en haut, et la ligne
 * Utilisateur::observe(...) à l'intérieur de la méthode boot() existante.
 */
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
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateUtilisateur
{
    public function handle(Request $request, Closure $next): Response
    {
        $utilisateur = current_utilisateur();

        if (!$utilisateur) {
            return redirect()->route('login');
        }

        if (!$utilisateur->actif) {
            session()->forget(['utilisateur_id', 'tenant_id']);
            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été désactivé. Contactez votre administrateur.',
            ]);
        }

        // Accessible dans tous les templates via $utilisateurConnecte
        view()->share('utilisateurConnecte', $utilisateur);

        return $next($request);
    }
}

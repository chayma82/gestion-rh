<?php

namespace App\Observers;

use App\Mail\CompteEntrepriseActiveMail;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Mail;

/**
 * Observe le modèle Utilisateur : dès qu'un compte passe de actif=false
 * à actif=true (par n'importe quel moyen passant par Eloquent : contrôleur,
 * Tinker, commande Artisan, seeder...), envoie automatiquement le mail
 * de confirmation d'activation.
 *
 * Placez ce fichier dans : app/Observers/UtilisateurObserver.php
 */
class UtilisateurObserver
{
    /**
     * Déclenché juste avant la sauvegarde d'une modification (UPDATE).
     * On compare l'ancienne valeur ($utilisateur->getOriginal()) et la
     * nouvelle, pour ne réagir qu'à une vraie transition false -> true.
     */
    public function updating(Utilisateur $utilisateur): void
    {
        $etaitActifAvant = (bool) $utilisateur->getOriginal('actif');
        $estActifMaintenant = (bool) $utilisateur->actif;

        if (!$etaitActifAvant && $estActifMaintenant) {
            Mail::to($utilisateur->email)->send(new CompteEntrepriseActiveMail($utilisateur));
        }
    }
}

<?php

namespace App\Mail;

use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email envoyé à l'administrateur d'une entreprise lorsque son compte
 * est validé/activé par le super-admin (cf. TenantController::valider).
 *
 * Placez ce fichier dans : app/Mail/CompteEntrepriseActiveMail.php
 *
 * ShouldQueue : l'envoi se fait en arrière-plan si une queue est
 * configurée (recommandé). Sinon, retirez "implements ShouldQueue"
 * pour un envoi synchrone immédiat.
 */
class CompteEntrepriseActiveMail extends Mailable 
{
    use Queueable, SerializesModels;

    public function __construct(public Utilisateur $utilisateur)
    {
    }

    public function build()
    {
        return $this
            ->subject('Votre compte La Luna HRMS est activé')
            ->view('emails.compte_active')
            ->with([
                'utilisateur' => $this->utilisateur,
                'loginUrl'    => route('auth.authi'),
            ]);
    }
}

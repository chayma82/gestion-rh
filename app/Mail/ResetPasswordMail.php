<?php

namespace App\Mail;

use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email contenant le lien de réinitialisation de mot de passe.
 *
 * Placez ce fichier dans : app/Mail/ResetPasswordMail.php
 */
class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $resetUrl;

    public function __construct(public Utilisateur $utilisateur, string $token)
    {
        $this->resetUrl = route('password.reset', ['token' => $token])
            . '?email=' . urlencode($utilisateur->email);
    }

    public function build()
    {
        return $this
            ->subject('Réinitialisation de votre mot de passe - La Luna HRMS')
            ->view('emails.reinitialiser_mot_de_passe')
            ->with([
                'utilisateur' => $this->utilisateur,
                'resetUrl'    => $this->resetUrl,
            ]);
    }
}

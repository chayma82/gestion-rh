<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email de confirmation envoyé à l'administrateur qui vient de soumettre
 * une demande de création de compte entreprise (cf. AuthController::store).
 *
 * Reprend le même message que la page auth.successajout affichée juste
 * après la soumission du formulaire, pour que la personne ait une trace
 * écrite dans sa boîte mail en plus de la page web.
 *
 * Placez ce fichier dans : app/Mail/DemandeEntrepriseRecueMail.php
 */
class DemandeEntrepriseRecueMail extends Mailable 
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public Utilisateur $utilisateur
    ) {
    }

    public function build()
    {
        return $this
            ->subject('Votre demande de compte entreprise a bien été reçue')
            ->view('emails.demande_confirmation')
            ->with([
                'tenant'      => $this->tenant,
                'utilisateur' => $this->utilisateur,
                'loginUrl'    => route('auth.authi'),
            ]);
    }
}

<?php

namespace App\Mail;

use App\Models\Entreprise;
use App\Models\Tenant;
use App\Models\Utilisateur;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email envoyé à l'équipe (vous) lorsqu'une nouvelle entreprise
 * soumet une demande de création de compte (cf. AuthController::store).
 *
 * Contient toutes les infos de l'entreprise ET de l'administrateur
 * pour permettre une revue rapide sans avoir à ouvrir le back-office.
 *
 * Placez ce fichier dans : app/Mail/NouvelleDemandeEntrepriseMail.php
 */
class NouvelleDemandeEntrepriseMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public Entreprise $entreprise,
        public Utilisateur $utilisateur
    ) {
    }

    public function build()
    {
        return $this
            ->subject('Nouvelle demande de compte entreprise : ' . $this->tenant->nom)
            ->view('emails.nouvelle_demande')
            ->with([
                'tenant'      => $this->tenant,
                'entreprise'  => $this->entreprise,
                'utilisateur' => $this->utilisateur,
            ]);
    }
}

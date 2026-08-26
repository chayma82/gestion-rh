<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Log;
use App\Models\PasswordResetUtilisateur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Gère le flux "mot de passe oublié" pour le modèle Utilisateur (auth
 * maison de l'app, pas le système Auth standard de Laravel).
 *
 * Flux :
 *  1. GET  /mot-de-passe-oublie          -> formulaire de saisie de l'email
 *  2. POST /mot-de-passe-oublie          -> génère un token, l'enregistre (hashé), envoie le mail
 *  3. GET  /reinitialiser-mot-de-passe/{token}?email=... -> formulaire nouveau mot de passe
 *  4. POST /reinitialiser-mot-de-passe   -> vérifie le token, met à jour motdepasse
 *
 * Placez ce fichier dans : app/Http/Controllers/ForgotPasswordController.php
 */
class ForgotPasswordController extends Controller
{
    /** Durée de validité du lien, en minutes */
    private const EXPIRATION_MINUTES = 60;

    public function show()
    {
        return view('auth.mot_de_passe_oublie');
    }

    public function envoyer(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();

        // Message volontairement identique qu'un compte existe ou non,
        // pour ne pas permettre de deviner quels emails sont enregistrés.
        $messageGenerique = "Si un compte existe avec cet email, un lien de réinitialisation vient de vous être envoyé.";

        if (!$utilisateur) {
            return back()->with('status', $messageGenerique);
        }

        // On supprime l'éventuel ancien token en attente pour cet email
        PasswordResetUtilisateur::where('email', $utilisateur->email)->delete();

        $token = Str::random(64);

        PasswordResetUtilisateur::create([
            'email'      => $utilisateur->email,
            'token'      => Hash::make($token), // on ne stocke jamais le token en clair
            'created_at' => now(),
        ]);

        Mail::to($utilisateur->email)->send(new ResetPasswordMail($utilisateur, $token));

        Log::enregistrer(
            tenantId: $utilisateur->tenant_id,
            utilisateurId: $utilisateur->id,
            recordId: $utilisateur->id,
            nomTable: 'utilisateur',
            description: "Demande de réinitialisation de mot de passe pour {$utilisateur->email}",
        );

        return back()->with('status', $messageGenerique);
    }

    public function formulaireReinitialisation(string $token, Request $request)
    {
        return view('auth.reinitialiser_mot_de_passe', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reinitialiser(Request $request)
    {
        $validated = $request->validate([
            'token'    => 'required|string',
            'email'    => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = PasswordResetUtilisateur::where('email', $validated['email'])->first();

        if (!$record || !Hash::check($validated['token'], $record->token)) {
            return back()
                ->withErrors(['email' => "Ce lien de réinitialisation est invalide ou a déjà été utilisé."])
                ->onlyInput('email');
        }

        if ($record->created_at->addMinutes(self::EXPIRATION_MINUTES)->isPast()) {
            $record->delete();

            return back()
                ->withErrors(['email' => "Ce lien de réinitialisation a expiré. Veuillez refaire une demande."])
                ->onlyInput('email');
        }

        $utilisateur = Utilisateur::where('email', $validated['email'])->first();

        if (!$utilisateur) {
            return back()->withErrors(['email' => "Aucun compte trouvé pour cet email."]);
        }

        $utilisateur->update(['motdepasse' => $validated['password']]); // hashé automatiquement par le modèle

        // Le lien est à usage unique : on le supprime après utilisation
        $record->delete();

        Log::enregistrer(
            tenantId: $utilisateur->tenant_id,
            utilisateurId: $utilisateur->id,
            recordId: $utilisateur->id,
            nomTable: 'utilisateur',
            description: "Mot de passe réinitialisé avec succès pour {$utilisateur->email}",
        );

        return redirect()
            ->route('auth.authi')
            ->with('status', "Votre mot de passe a été réinitialisé avec succès. Vous pouvez vous connecter.");
    }
}

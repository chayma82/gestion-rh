<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NouvelleDemandeEntrepriseMail;
use App\Mail\DemandeEntrepriseRecueMail;
use App\Models\Tenant;
use App\Models\TenantCategorie;
use App\Models\Entreprise;
use App\Models\Log;
use App\Models\Role;
use App\Models\Utilisateur;

class AuthController extends Controller
{
    public function authi()
    {
        if (current_utilisateur()) {
            // Le super admin n'a pas de dashboard RH : on l'envoie directement
            // vers la gestion des tenants, qui est son seul espace de travail.
            return is_super_admin()
                ? redirect()->route('tenants.index')
                : redirect()->route('Dashboard.index');
        }

        return view('auth.Auth');
    }

    public function create()
    {
        return view('auth.ajoutentreprise');
    }

    // Traitement du formulaire : crée le tenant + l'entreprise + l'admin, EN ATTENTE de validation
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Entreprise
            'nom_entreprise'       => 'required|string|max:150',
            'num_fiscal'           => 'required|string|max:50|unique:entreprise,num_fiscal',
            'type_entreprise'      => 'required|in:rh,autre',
            'secteur_activite'     => 'required|string|max:100',
            'email_entreprise'     => 'required|email|max:150|unique:entreprise,email',
            'telephone_entreprise' => 'nullable|string|max:30',
            'adresse'              => 'required|string|max:255',
            'ville'                => 'required|string|max:100',
            'code_postal'          => 'required|string|max:20',

            // Administrateur
            'nom'                  => 'required|string|max:100',
            'prenom'               => 'required|string|max:100',
            'email_admin'          => 'required|email|max:150|unique:utilisateur,email',
            'telephone_admin'      => 'nullable|string|max:30',
            'motdepasse'           => 'required|string|min:8|confirmed',
        ]);

        // La transaction retourne les 3 objets créés (tenant, entreprise, utilisateur admin)
        // pour pouvoir ensuite les transmettre tels quels au mail de notification et au log.
        [$tenant, $entreprise, $utilisateur] = DB::transaction(function () use ($validated) {

            $categorieBasique = TenantCategorie::where('nom', 'Basique')->first();

            // Le tenant porte automatiquement le même nom que l'entreprise
            $tenant = Tenant::create([
                'nom' => $validated['nom_entreprise'],
                'tenant_categorie_id' => $categorieBasique?->id,
            ]);

            $entreprise = Entreprise::create([
                'tenant_id'        => $tenant->id,
                'nom'              => $validated['nom_entreprise'],
                'email'            => $validated['email_entreprise'],
                'type_entreprise'  => $validated['type_entreprise'],
                'secteur_activite' => $validated['secteur_activite'],
                'adresse'          => $validated['adresse'],
                'ville'            => $validated['ville'],
                'code_postal'      => $validated['code_postal'],
                'num_fiscal'       => $validated['num_fiscal'],
                'telephone'        => $validated['telephone_entreprise'] ?? null,
            ]);

            // Le rôle Admin créé avec l'entreprise a automatiquement accès
            // aux 3 modules : Admin, Facturation et RH.
            $roleAdmin = Role::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'nom' => 'Admin',
                ],
                [
                    'acces_admin' => true,
                    'acces_facturation' => true,
                    'acces_rh' => true,
                ]
            );

            $utilisateur = Utilisateur::create([
                'tenant_id'     => $tenant->id,
                'entreprise_id' => $entreprise->id,
                'role_id'       => $roleAdmin->id,
                'nom'           => $validated['nom'],
                'prenom'        => $validated['prenom'],
                'email'         => $validated['email_admin'],
                'telephone'     => $validated['telephone_admin'] ?? null,
                'motdepasse'    => $validated['motdepasse'], // hashé automatiquement par le modèle
                'actif'         => false, // en attente de validation manuelle par votre équipe
            ]);

            return [$tenant, $entreprise, $utilisateur];
        });

        // Journalise la demande de création (utilisateur_id = null : personne n'est encore
        // authentifiée, c'est l'admin lui-même qui vient de s'auto-inscrire).
        Log::enregistrer(
            tenantId: $tenant->id,
            utilisateurId: null,
            recordId: $tenant->id,
            nomTable: 'tenant',
            description: "Nouvelle demande d'inscription : entreprise « {$entreprise->nom} », admin {$utilisateur->email}",
        );

        // Notifie l'équipe (vous) qu'une nouvelle entreprise attend validation,
        // avec toutes les infos de l'entreprise + de l'admin demandé.
        $tenant->load('tenantCategorie');

        Mail::to(config('mail.admin_notification_address', config('mail.from.address')))
            ->send(new NouvelleDemandeEntrepriseMail($tenant, $entreprise, $utilisateur));

        // Confirme aussi à l'admin lui-même que sa demande a bien été reçue,
        // avec le même message que la page auth.successajout.
        Mail::to($utilisateur->email)
            ->send(new DemandeEntrepriseRecueMail($tenant, $utilisateur));

        return redirect()
            ->route('auth.success', ['entreprise' => $entreprise->id])
            ->with('nomEntreprise', $tenant->nom)
            ->with('entreprise', $entreprise);
    }

    public function success(Request $request)
    {
        // On recharge l'entreprise depuis son ID passé dans l'URL : ça survit
        // à un rafraîchissement de page, contrairement aux données flashées
        // en session qui ne durent qu'une seule requête.
        $entreprise = Entreprise::find($request->query('entreprise'))
            ?? session('entreprise');

        return view('auth.successajout', [
            'nomEntreprise' => $entreprise->nom ?? session('nomEntreprise'),
            'entreprise'    => $entreprise,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $utilisateur = Utilisateur::where('email', $credentials['email'])->first();

        if (!$utilisateur || !Hash::check($credentials['password'], $utilisateur->motdepasse)) {
            // Tentative échouée : on journalise quand même si l'email correspond
            // à un compte existant (utile pour détecter des tentatives d'intrusion).
            if ($utilisateur) {
                Log::enregistrer(
                    tenantId: $utilisateur->tenant_id,
                    utilisateurId: $utilisateur->id,
                    recordId: $utilisateur->id,
                    nomTable: 'utilisateur',
                    description: "Tentative de connexion échouée (mot de passe incorrect) pour {$utilisateur->email}",
                );
            }

            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        if (!$utilisateur->actif) {
            Log::enregistrer(
                tenantId: $utilisateur->tenant_id,
                utilisateurId: $utilisateur->id,
                recordId: $utilisateur->id,
                nomTable: 'utilisateur',
                description: "Tentative de connexion refusée : compte inactif ({$utilisateur->email})",
            );

            return back()
                ->withErrors(['email' => "Ce compte est en attente de validation ou a été désactivé."])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        session([
            'utilisateur_id' => $utilisateur->id,
            'tenant_id'      => $utilisateur->tenant_id,
        ]);

        Log::enregistrer(
            tenantId: $utilisateur->tenant_id,
            utilisateurId: $utilisateur->id,
            recordId: $utilisateur->id,
            nomTable: 'utilisateur',
            description: "Connexion réussie de {$utilisateur->email}",
        );

        // Le super admin n'a pas de dashboard RH : on l'envoie directement
        // vers la gestion des tenants, quoi qu'il ait tenté de visiter avant
        // d'être redirigé vers le login (pas de redirect()->intended() pour lui).
        if (is_super_admin()) {
            return redirect()->route('tenants.index');
        }

        return redirect()->intended(route('Dashboard.index'));
    }

    public function logout(Request $request)
    {
        $utilisateur = current_utilisateur();

        if ($utilisateur) {
            Log::enregistrer(
                tenantId: $utilisateur->tenant_id,
                utilisateurId: $utilisateur->id,
                recordId: $utilisateur->id,
                nomTable: 'utilisateur',
                description: "Déconnexion de {$utilisateur->email}",
            );
        }

        $request->session()->forget(['utilisateur_id', 'tenant_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.authi');
    }
}

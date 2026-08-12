<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Models\TenantCategorie;
use App\Models\Role;
use App\Models\Utilisateur;

class AuthController extends Controller
{
    // Formulaire de connexion
    public function authi()
    {
        // Déjà connecté ? Inutile de repasser par le login.
        if (current_utilisateur()) {
            return redirect()->route('Dashboard.index');
        }

        return view('auth.Auth');
    }

    // Formulaire de demande de création de compte entreprise
    public function create()
    {
        return view('auth.ajoutentreprise');
    }

    // Traitement du formulaire ci-dessus : crée le tenant + l'admin, EN ATTENTE de validation
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_entreprise' => 'required|string|max:100',
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'email'          => 'required|email|max:150|unique:utilisateur,email',
            'telephone'      => 'nullable|string|max:30',
            'motdepasse'     => 'required|string|min:8|confirmed',
        ]);

        $tenant = DB::transaction(function () use ($validated) {

            // Catégorie par défaut à l'inscription : Basique.
            // Une équipe interne pourra la faire évoluer manuellement après validation.
            $categorieBasique = TenantCategorie::where('nom', 'Basique')->first();

            $tenant = Tenant::create([
                'nom' => $validated['nom_entreprise'],
                'tenant_categorie_id' => $categorieBasique?->id,
            ]);

            // Rôle Admin par défaut pour ce nouveau tenant
            $roleAdmin = Role::firstOrCreate([
                'tenant_id' => $tenant->id,
                'nom' => 'Admin',
            ]);

            Utilisateur::create([
                'tenant_id' => $tenant->id,
                'entreprise_id' => 1,
                'role_id' => $roleAdmin->id,
                'nom' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'] ?? null,
                'motdepasse' => $validated['motdepasse'], // hashé automatiquement par le modèle
                'actif' => false, // en attente de validation manuelle par votre équipe
            ]);

            return $tenant;
        });

        return redirect()
            ->route('auth.success')
            ->with('nomEntreprise', $tenant->nom);
    }

    // Page de confirmation après la demande
    public function success()
    {
        return view('auth.successajout', [
            'nomEntreprise' => session('nomEntreprise'),
        ]);
    }

    // Connexion réelle (avant : redirigeait vers le dashboard sans rien vérifier)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $utilisateur = Utilisateur::where('email', $credentials['email'])->first();

        if (!$utilisateur || !Hash::check($credentials['password'], $utilisateur->motdepasse)) {
            return back()
                ->withErrors(['email' => 'Identifiants incorrects.'])
                ->onlyInput('email');
        }

        if (!$utilisateur->actif) {
            return back()
                ->withErrors(['email' => "Ce compte est en attente de validation ou a été désactivé."])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        session([
            'utilisateur_id' => $utilisateur->id,
            'tenant_id'      => $utilisateur->tenant_id,
        ]);

        return redirect()->intended(route('Dashboard.index'));
    }

    // Déconnexion réelle (avant : ne vidait pas la session, donc l'utilisateur restait connecté)
    public function logout(Request $request)
    {
        $request->session()->forget(['utilisateur_id', 'tenant_id']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.authi');
    }
}
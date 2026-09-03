<div class="flex flex-col h-full bg-white">

    <!-- Logo -->
    <div class="flex  h-[72px] items-center gap-3 px-6 py-6 border-b border-gray-200">

        <div class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-[#E2721B] text-white shrink-0">
            <i class="fa-solid fa-building text-sm"></i>
        </div>

        <div class="leading-tight overflow-hidden">
            @php
                $utilisateur = current_utilisateur();
            @endphp

            <div class="leading-tight overflow-hidden">
                <h2 class="text-base font-bold text-gray-900 truncate">
                    {{ $utilisateur?->entreprise?->nom ?? 'Portail RH' }}
                </h2>

                <p class="text-xs text-gray-400 truncate">
                    {{ $utilisateur?->role?->nom ?? 'Utilisateur' }}
                </p>
            </div>
        </div>

    </div>

    @php
        // Droits d'accès du rôle de l'utilisateur connecté : ils déterminent
        // les sections du menu visibles. Un utilisateur sans rôle assigné
        // ne voit aucune section restreinte (accès par défaut refusé).
        $accesAdmin = (bool) $utilisateur?->role?->acces_admin;
        $accesFacturation = (bool) $utilisateur?->role?->acces_facturation;
        $accesRh = (bool) $utilisateur?->role?->acces_rh;
    @endphp

    <!-- Menu -->
    <ul class="mt-4 flex-1 space-y-1 px-2 overflow-y-auto">

        <li>
            <a href="{{ route('Dashboard.index') }}"
                @class([
                    'flex items-center gap-3 px-4 py-3 rounded-lg cursor-pointer transition',
                    'bg-orange-50 text-[#E2721B]' => request()->routeIs('Dashboard.index'),
                    'text-gray-600 hover:bg-orange-50 hover:text-[#E2721B]' => !request()->routeIs('Dashboard.index'),
                ])>
                <i class="fa-solid fa-table-columns w-4 text-center"></i>
                <span class="text-sm font-medium">Tableau de bord</span>
            </a>
        </li>

    @php
        // Couvre tous les noms de routes liés aux employés, y compris
        // le préfixe singulier "employe." utilisé par certains contrôleurs
        // (employe.conge.index, employe.avance.index, employe.prime.index).
        $employesActif = request()->routeIs('employes.*') || request()->routeIs('employe.*');

        $listeEmployesActif = request()->routeIs(
            'employes.index', 'employes.create', 'employes.store',
            'employes.edit', 'employes.update', 'employes.info',
            'employes.destroy', 'employes.archives', 'employes.desarchiver'
        );

        $contratsActif = request()->routeIs('employes.contrats.*');

        $congesActif = request()->routeIs('employes.conges.*') || request()->routeIs('employe.conge.index');

        $salairesGroupeActif = request()->routeIs('employes.salaires.*')
            || request()->routeIs('employes.avances.*')
            || request()->routeIs('employes.primes.*')
            || request()->routeIs('employe.avance.index')
            || request()->routeIs('employe.prime.index');

        $tableauSalairesActif = request()->routeIs('employes.salaires.*');
        $avancesActif = request()->routeIs('employes.avances.*') || request()->routeIs('employe.avance.index');
        $primesActif = request()->routeIs('employes.primes.*') || request()->routeIs('employe.prime.index');
    @endphp

    @if($accesRh)
    <li>

        <div @class([
                'w-full flex items-center justify-between px-4 py-3 rounded-lg',
                'bg-orange-50 text-[#E2721B]' => $employesActif,
                'text-gray-600' => !$employesActif,
            ])>

            <div class="flex items-center gap-3">
                <i class="fa-solid fa-users"></i>
                <span class="text-sm font-medium">Employés</span>
            </div>

            <i class="fa-solid fa-chevron-down text-xs"></i>

        </div>

        {{-- Sous-menu toujours visible, plus de toggle --}}
        <ul class="ml-10 mt-2 space-y-2">

            <li>
                <a href="{{ route('employes.index') }}"
                    @class([
                        'block text-sm',
                        'text-[#E2721B] font-semibold' => $listeEmployesActif,
                        'text-gray-600 hover:text-[#E2721B]' => !$listeEmployesActif,
                    ])>
                    Liste des employés
                </a>
            </li>

            <li>
                <a href="{{ route('employes.contrats.index') }}"
                    @class([
                        'block text-sm',
                        'text-[#E2721B] font-semibold' => $contratsActif,
                        'text-gray-600 hover:text-[#E2721B]' => !$contratsActif,
                    ])>
                    Contrats
                </a>
            </li>

            <li>
                <a href="{{ route('employes.conges.index') }}"
                    @class([
                        'block text-sm',
                        'text-[#E2721B] font-semibold' => $congesActif,
                        'text-gray-600 hover:text-[#E2721B]' => !$congesActif,
                    ])>
                    Congés
                </a>
            </li>

            <li>

                <div @class([
                        'w-full flex items-center justify-between py-2',
                        'text-[#E2721B]' => $salairesGroupeActif,
                        'text-gray-600' => !$salairesGroupeActif,
                    ])>

                    <span class="text-sm font-medium">Salaires</span>

                    <i class="fa-solid fa-chevron-down text-xs"></i>

                </div>

                {{-- Sous-sous-menu toujours visible, plus de toggle --}}
                <ul class="ml-5 mt-2 space-y-2">

                    <li>
                        <a href="{{ route('employes.salaires.index') }}"
                            @class([
                                'block text-sm',
                                'text-[#E2721B] font-semibold' => $tableauSalairesActif,
                                'text-gray-600 hover:text-[#E2721B]' => !$tableauSalairesActif,
                            ])>
                            Tableau de salaires
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('employes.avances.index') }}"
                            @class([
                                'block text-sm',
                                'text-[#E2721B] font-semibold' => $avancesActif,
                                'text-gray-600 hover:text-[#E2721B]' => !$avancesActif,
                            ])>
                            Avances
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('employes.primes.index') }}"
                            @class([
                                'block text-sm',
                                'text-[#E2721B] font-semibold' => $primesActif,
                                'text-gray-600 hover:text-[#E2721B]' => !$primesActif,
                            ])>
                            Primes
                        </a>
                    </li>

                </ul>

            </li>

        </ul>

    </li>
    @endif

    @php
    // ===== VENTES =====
    $ventesActif =
        request()->routeIs('clients.*') ||
        request()->routeIs('factures.ventes.*');

    $clientsActif = request()->routeIs('clients.*');

    $facturesVentesActif = request()->routeIs('factures.ventes.*');

    // ===== ACHATS =====
    $achatsActif =
        request()->routeIs('fournisseurs.*') ||
        request()->routeIs('factures.achats.*');

    $fournisseursActif = request()->routeIs('fournisseurs.*');

    $facturesAchatsActif = request()->routeIs('factures.achats.*');

    // ===== PARAMÈTRES (Utilisateurs & Rôles) =====
    // Les routes réelles sont 'utilisateur.index' / 'utilisateurs.*' et 'roles.*'
    // (pas de préfixe "parametres."), on aligne donc les conditions ci-dessous.
    $utilisateursActif = request()->routeIs('utilisateur.index') || request()->routeIs('utilisateurs.*');

    $rolesActif = request()->routeIs('roles.*');

    $departementsActif = request()->routeIs('departements.*');

    $postesActif = request()->routeIs('postes.*');

    $parametresActif = $utilisateursActif || $rolesActif || $departementsActif || $postesActif;
    @endphp

    <!-- ================= VENTES ================= -->

@if($accesFacturation)
<li>

    <div @class([
        'w-full flex items-center justify-between px-4 py-3 rounded-lg',
        'bg-orange-50 text-[#E2721B]' => $ventesActif,
        'text-gray-600' => !$ventesActif,
    ])>

        <div class="flex items-center gap-3">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="text-sm font-medium">Ventes</span>
        </div>

        <i class="fa-solid fa-chevron-down text-xs"></i>

    </div>

    <ul class="ml-10 mt-2 space-y-2">

        <!-- Clients -->
        <li>
            <a href="{{ route('clients.index') }}"
                @class([
                    'block text-sm',
                    'text-[#E2721B] font-semibold' => $clientsActif,
                    'text-gray-600 hover:text-[#E2721B]' => !$clientsActif,
                ])>
                Clients
            </a>
        </li>

        <!-- Factures ventes -->
        <li>
            <a href="{{ route('factures.ventes.index') }}"
                @class([
                    'block text-sm',
                    'text-[#E2721B] font-semibold' => $facturesVentesActif,
                    'text-gray-600 hover:text-[#E2721B]' => !$facturesVentesActif,
                ])>
                Factures clients
            </a>
        </li>

    </ul>

</li>
@endif


<!-- ================= ACHATS ================= -->

@if($accesFacturation)
<li>

    <div @class([
        'w-full flex items-center justify-between px-4 py-3 rounded-lg',
        'bg-orange-50 text-[#E2721B]' => $achatsActif,
        'text-gray-600' => !$achatsActif,
    ])>

        <div class="flex items-center gap-3">
            <i class="fa-solid fa-truck"></i>
            <span class="text-sm font-medium">Achats</span>
        </div>

        <i class="fa-solid fa-chevron-down text-xs"></i>

    </div>

    <ul class="ml-10 mt-2 space-y-2">

        <!-- Fournisseurs -->
        <li>
            <a href="{{ route('fournisseurs.index') }}"
                @class([
                    'block text-sm',
                    'text-[#E2721B] font-semibold' => $fournisseursActif,
                    'text-gray-600 hover:text-[#E2721B]' => !$fournisseursActif,
                ])>
                Fournisseurs
            </a>
        </li>

        <!-- Factures achats -->
        <li>
            <a href="{{ route('factures.achats.index') }}"
                @class([
                    'block text-sm',
                    'text-[#E2721B] font-semibold' => $facturesAchatsActif,
                    'text-gray-600 hover:text-[#E2721B]' => !$facturesAchatsActif,
                ])>
                Factures fournisseurs
            </a>
        </li>

    </ul>

</li>
@endif

<!-- ================= PARAMÈTRES ================= -->

@if($accesAdmin)
<li>

    <div @class([
        'w-full flex items-center justify-between px-4 py-3 rounded-lg',
        'bg-orange-50 text-[#E2721B]' => $parametresActif,
        'text-gray-600' => !$parametresActif,
    ])>

        <div class="flex items-center gap-3">
            <i class="fa-solid fa-gear"></i>
            <span class="text-sm font-medium">Paramètres</span>
        </div>

        <i class="fa-solid fa-chevron-down text-xs"></i>

    </div>

    <ul class="ml-10 mt-2 space-y-2">

        <!-- Utilisateurs -->
        <li>
            <a href="{{ route('utilisateur.index') }}"
                @class([
                    'block text-sm',
                    'text-[#E2721B] font-semibold' => $utilisateursActif,
                    'text-gray-600 hover:text-[#E2721B]' => !$utilisateursActif,
                ])>
                Utilisateurs
            </a>
        </li>

        <!-- Rôles -->
        <li>
            <a href="{{ route('roles.index') }}"
                @class([
                    'block text-sm',
                    'text-[#E2721B] font-semibold' => $rolesActif,
                    'text-gray-600 hover:text-[#E2721B]' => !$rolesActif,
                ])>
                Rôles
            </a>
        </li>

        <!-- Départements -->
        <li>
            <a href="{{ route('departements.index') }}"
                @class([
                    'block text-sm',
                    'text-[#E2721B] font-semibold' => $departementsActif,
                    'text-gray-600 hover:text-[#E2721B]' => !$departementsActif,
                ])>
                Départements
            </a>
        </li>

        <!-- Postes -->
        <li>
            <a href="{{ route('postes.index') }}"
                @class([
                    'block text-sm',
                    'text-[#E2721B] font-semibold' => $postesActif,
                    'text-gray-600 hover:text-[#E2721B]' => !$postesActif,
                ])>
                Postes
            </a>
        </li>

    </ul>

</li>
@endif
    </ul>
    <!-- Déconnexion -->
    <div class="px-2 pb-4 border-t border-gray-100 pt-3">

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-lg cursor-pointer text-red-600 font-medium hover:bg-red-50 transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                <span class="text-sm">Déconnexion</span>
            </button>
        </form>

    </div>

</div>

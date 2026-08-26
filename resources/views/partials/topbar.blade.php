@php
    $utilisateur = current_utilisateur();
@endphp
<div class="flex items-center h-[72px] bg-white/80 backdrop-blur-sm border-b border-gray-100 px-6 gap-4 sticky top-0 z-30">

    <!-- Bouton Sidebar -->
    <button
        id="toggleSidebar"
        class="w-10 h-10 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#E2721B] transition shrink-0">
        <i class="fa-solid fa-bars text-base"></i>
    </button>

    @php
        // Titre de page calculé depuis la route courante
        $pageTitle = match (true) {
            request()->routeIs('Dashboard.index')          => 'Tableau de bord',

            // Employés
            request()->routeIs('employes.contrats.*')      => 'Contrats',
            request()->routeIs('employes.conges.*'),
            request()->routeIs('employe.conge.index')      => 'Congés',
            request()->routeIs('employes.salaires.*')      => 'Salaires',
            request()->routeIs('employes.avances.*'),
            request()->routeIs('employe.avance.index')     => 'Avances',
            request()->routeIs('employes.primes.*'),
            request()->routeIs('employe.prime.index')      => 'Primes',
            request()->routeIs('employes.archives')        => 'Employés archivés',
            request()->routeIs('employes.*'),
            request()->routeIs('employe.*')                 => 'Employés',

            // Ventes
            request()->routeIs('clients.*')         => 'Clients',
            request()->routeIs('ventes.clients.*')         => 'Clients',
            request()->routeIs('factures.ventes.*')          => 'Factures clients',

            // Achats
            request()->routeIs('fournisseurs.*')    => 'Fournisseurs',
            request()->routeIs('achats.fournisseurs.*')    => 'Fournisseurs',
            request()->routeIs('factures.achats.*')        => 'Factures fournisseurs',

            // Paramètres (Utilisateurs & Rôles)
            request()->routeIs('utilisateur.*')            => 'Utilisateurs',
            request()->routeIs('utilisateurs.*')            => 'Utilisateurs',
            request()->routeIs('roles.*')                    => 'Rôles',
            request()->routeIs('departements.*')             => 'Départements',
            request()->routeIs('postes.*')                   => 'Postes',

            default                                        => 'Portail RH',
        };
    @endphp

    <!-- Titre de la page -->
    <div class="flex items-center gap-2 min-w-0">
        <span class="text-sm text-gray-400 hidden sm:inline">
            {{ $utilisateur?->entreprise?->nom ?? '_' }}
        </span>
        <i class="fa-solid fa-chevron-right text-gray-300 text-xs hidden sm:inline"></i>
        <h1 class="text-base font-semibold text-gray-800 truncate">{{ $pageTitle }}</h1>
    </div>

    <div class="flex-1"></div>

    <!-- Notifications -->
    <div class="relative">

        @php
            // Injectées par App\View\Composers\NotificationsComposer.
            // Fallback défensif si jamais le composer n'est pas branché sur
            // cette vue (évite un plantage, affiche juste "aucune notification").
            $notifications = $notifications ?? collect();
            $notificationsNonLues = $notificationsNonLues ?? 0;
        @endphp

        <button
            id="notifBtn"
            class="relative w-10 h-10 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#E2721B] transition">
            <i class="fa-regular fa-bell text-base"></i>
            <span
                id="notifBadge"
                class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-[#E2721B] ring-2 ring-white {{ $notificationsNonLues > 0 ? '' : 'hidden' }}">
            </span>
        </button>

        <!-- Panneau déroulant -->
        <div
            id="notifPanel"
            class="absolute right-0 top-full mt-3 w-80 max-h-[28rem] overflow-y-auto bg-white rounded-2xl border border-gray-100 shadow-xl origin-top opacity-0 scale-95 -translate-y-2 pointer-events-none transition-all duration-200 z-50 p-5">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">
                    Notifications
                </h3>
                @if($notificationsNonLues > 0)
                    <button
                        id="notifMarquerToutesLues"
                        type="button"
                        class="text-xs text-[#E2721B] hover:underline font-medium">
                        Tout marquer comme lu
                    </button>
                @endif
            </div>

            <div id="notifListe" class="divide-y divide-gray-100">

                @forelse($notifications as $n)
                    <button
                        type="button"
                        data-id="{{ $n->id }}"
                        data-lue="{{ $n->lue ? '1' : '0' }}"
                        class="notif-item w-full text-left flex items-start gap-3 py-3.5 {{ $loop->first ? 'pt-0' : '' }} {{ !$n->lue ? 'bg-orange-50/40' : '' }} hover:bg-gray-50 transition rounded-lg px-1 -mx-1">

                        <div class="w-8 h-8 rounded-full {{ $n->couleur }} flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $n->icon }} text-xs"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                {{ $n->titre }}
                                @if(!$n->lue)
                                    <span class="notif-dot w-1.5 h-1.5 rounded-full bg-[#E2721B] shrink-0"></span>
                                @endif
                            </p>
                            <p class="text-sm text-gray-500 leading-snug">{{ $n->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $n->date_reception?->diffForHumans() }}</p>
                        </div>

                    </button>
                @empty
                    <p id="notifVide" class="text-sm text-gray-400 text-center py-6">
                        Aucune notification pour le moment.
                    </p>
                @endforelse

            </div>

            <div class="pt-3 mt-1 text-center">
                <a href="{{ route('notifications.index') }}" class="text-xs text-[#E2721B] hover:underline font-medium">
                    Voir toutes les notifications
                </a>
            </div>

        </div>

    </div>

    <!-- Séparateur -->
    <div class="w-px h-8 bg-gray-200"></div>

    <!-- Utilisateur -->
    <div class="flex items-center gap-3 pr-1 cursor-pointer group">

        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 text-[#9A2A00] flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-sm">
            {{ strtoupper(substr($utilisateur?->prenom ?? '', 0, 1) . substr($utilisateur?->nom ?? '', 0, 1)) }}
        </div>

        <div class="leading-tight hidden sm:block">
            <p class="text-sm font-semibold text-gray-800">
                {{ $utilisateur?->nom }} {{ $utilisateur?->prenom }}
            </p>

            <p class="text-xs text-gray-400">
                {{ $utilisateur?->role?->nom ?? 'Utilisateur' }}
            </p>
        </div>

        <i class="fa-solid fa-chevron-down text-gray-400 text-xs hidden sm:block group-hover:text-gray-600 transition"></i>

    </div>

</div>

<script>

const notifBtn = document.getElementById('notifBtn');
const notifPanel = document.getElementById('notifPanel');

function openNotifPanel() {
    notifPanel.classList.remove('opacity-0', 'scale-95', '-translate-y-2', 'pointer-events-none');
    notifPanel.classList.add('opacity-100', 'scale-100', 'translate-y-0', 'pointer-events-auto');
}

function closeNotifPanel() {
    notifPanel.classList.add('opacity-0', 'scale-95', '-translate-y-2', 'pointer-events-none');
    notifPanel.classList.remove('opacity-100', 'scale-100', 'translate-y-0', 'pointer-events-auto');
}

notifBtn.addEventListener('click', (e) => {
    e.stopPropagation();

    const isOpen = notifPanel.classList.contains('opacity-100');

    if (isOpen) {
        closeNotifPanel();
    } else {
        openNotifPanel();
    }
});

document.addEventListener('click', (e) => {
    if (!notifPanel.contains(e.target) && !notifBtn.contains(e.target)) {
        closeNotifPanel();
    }
});

// ------------------------------------------------------------------
// Notifications : marquer comme lue (au clic) / tout marquer comme lu.
// Nécessite une balise <meta name="csrf-token" content="{{ csrf_token() }}">
// dans le <head> du layout (standard Laravel).
// ------------------------------------------------------------------
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const notifBadge = document.getElementById('notifBadge');
const notifMarquerToutesLuesBtn = document.getElementById('notifMarquerToutesLues');

function majBadge(nonLues) {
    if (nonLues > 0) {
        notifBadge.classList.remove('hidden');
    } else {
        notifBadge.classList.add('hidden');
    }
}

document.querySelectorAll('.notif-item').forEach((item) => {
    item.addEventListener('click', () => {
        const id = item.dataset.id;
        const dejaLue = item.dataset.lue === '1';

        // Marque l'élément comme lu visuellement tout de suite (pas
        // besoin d'attendre la réponse serveur pour une UI réactive).
        item.classList.remove('bg-orange-50/40');
        item.querySelector('.notif-dot')?.remove();
        item.dataset.lue = '1';

        if (dejaLue) return;

        fetch(`{{ url('notifications') }}/${id}/lue`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
            .then((res) => res.json())
            .then((data) => majBadge(data.nonLues ?? 0))
            .catch(() => {
                // En cas d'échec réseau, on laisse l'UI telle quelle plutôt
                // que de bloquer l'utilisateur ; un rechargement de page
                // resynchronisera l'état réel avec le serveur.
            });
    });
});

notifMarquerToutesLuesBtn?.addEventListener('click', () => {
    fetch(`{{ url('notifications/marquer-toutes-lues') }}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
    })
        .then((res) => res.json())
        .then(() => {
            document.querySelectorAll('.notif-item').forEach((item) => {
                item.classList.remove('bg-orange-50/40');
                item.querySelector('.notif-dot')?.remove();
                item.dataset.lue = '1';
            });
            majBadge(0);
            notifMarquerToutesLuesBtn.classList.add('hidden');
        })
        .catch(() => {});
});

</script>

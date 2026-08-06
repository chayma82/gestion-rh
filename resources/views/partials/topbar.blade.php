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

            default                                        => 'Portail RH',
        };
    @endphp

    <!-- Titre de la page -->
    <div class="flex items-center gap-2 min-w-0">
        <span class="text-sm text-gray-400 hidden sm:inline">Portail RH</span>
        <i class="fa-solid fa-chevron-right text-gray-300 text-xs hidden sm:inline"></i>
        <h1 class="text-base font-semibold text-gray-800 truncate">{{ $pageTitle }}</h1>
    </div>

    <div class="flex-1"></div>

    <!-- Notifications -->
    <div class="relative">

        <button
            id="notifBtn"
            class="relative w-10 h-10 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#E2721B] transition">
            <i class="fa-regular fa-bell text-base"></i>
            <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-[#E2721B] ring-2 ring-white"></span>
        </button>

        <!-- Panneau déroulant -->
        <div
            id="notifPanel"
            class="absolute right-0 top-full mt-3 w-80 max-h-[28rem] overflow-y-auto bg-white rounded-2xl border border-gray-100 shadow-xl origin-top opacity-0 scale-95 -translate-y-2 pointer-events-none transition-all duration-200 z-50 p-5">

            <h3 class="text-base font-bold text-gray-900 mb-4">
                Notifications
            </h3>

            <div class="divide-y divide-gray-100">

                @php
                    $notifications = $notifications ?? [
                        ['icon' => 'fa-money-bill-wave', 'color' => 'bg-teal-50 text-teal-600', 'titre' => 'Paiement Reçu', 'texte' => "La facture #INV-2024-001 doit être réglée dans 6 jours", 'temps' => 'Il y a 2 heures'],
                        ['icon' => 'fa-user-plus', 'color' => 'bg-orange-50 text-[#E2721B]', 'titre' => 'Nouvel Employé', 'texte' => "Jean Dupont a rejoint l'équipe Marketing.", 'temps' => 'Il y a 5 heures'],
                        ['icon' => 'fa-circle-exclamation', 'color' => 'bg-red-50 text-red-500', 'titre' => 'Retard de Paiement', 'texte' => "La facture #INV-2023-098 est en retard de 5 jours.", 'temps' => 'Hier'],
                        ['icon' => 'fa-file-lines', 'color' => 'bg-gray-100 text-gray-500', 'titre' => 'Document Signé', 'texte' => "Le contrat de Marie Curie a été validé.", 'temps' => 'Il y a 2 jours'],
                    ];
                @endphp

                @foreach($notifications as $n)
                    <div class="flex items-start gap-3 py-3.5 {{ $loop->first ? 'pt-0' : '' }}">

                        <div class="w-8 h-8 rounded-full {{ $n['color'] }} flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $n['icon'] }} text-xs"></i>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $n['titre'] }}</p>
                            <p class="text-sm text-gray-500 leading-snug">{{ $n['texte'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $n['temps'] }}</p>
                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    </div>

    <!-- Séparateur -->
    <div class="w-px h-8 bg-gray-200"></div>

    <!-- Utilisateur -->
    <div class="flex items-center gap-3 pr-1 cursor-pointer group">

        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-100 to-orange-50 text-[#9A2A00] flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-sm">
            JD
        </div>

        <div class="leading-tight hidden sm:block">
            <p class="text-sm font-semibold text-gray-800">
                Johnny Deep
            </p>
            <p class="text-xs text-gray-400">
                Admin Global
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

</script>

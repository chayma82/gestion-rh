<div class="flex items-center h-[72px] bg-white/80 backdrop-blur-sm border-b border-gray-100 px-6 gap-4 sticky top-0 z-30">

    <!-- Bouton Sidebar -->
    <button
        id="toggleSidebar"
        class="w-10 h-10 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#E2721B] transition shrink-0">
        <i class="fa-solid fa-bars text-base"></i>
    </button>

    <!-- Barre de recherche -->
    <div class="relative flex-1 max-w-md">

        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

        <input
            type="text"
            placeholder="Rechercher des employés, Factures..."
            class="w-full pl-11 pr-4 py-2.5 bg-gray-100 border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400 outline-none focus:bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">

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

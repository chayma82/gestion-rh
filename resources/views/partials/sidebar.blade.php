<div class="flex flex-col h-full bg-white">

    <!-- Logo -->
    <div class="flex  h-[70px] items-center gap-3 px-6 py-6 border-b border-gray-200">

        <div class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-[#E2721B] text-white shrink-0">
            <i class="fa-solid fa-building text-sm"></i>
        </div>

        <div class="leading-tight overflow-hidden">
            <h2 class="text-base font-bold text-gray-900 truncate">
                Portail RH
            </h2>
            <p class="text-xs text-gray-400 truncate">
                Admin Global
            </p>
        </div>

    </div>

    <!-- Menu -->
    <ul class="mt-4 flex-1 space-y-1 px-2">

        <li>
            <a href="{{ route('Dashboard.index') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg cursor-pointer text-gray-600 hover:bg-orange-50 hover:text-[#E2721B] transition">
                <i class="fa-solid fa-table-columns w-4 text-center"></i>
                <span class="text-sm font-medium">Tableau de bord</span>
            </a>
        </li>

        <li>

    <button onclick="toggleEmployes()"
        class="w-full flex items-center justify-between px-4 py-3 rounded-lg hover:bg-orange-50 hover:text-[#E2721B]">

        <div class="flex items-center gap-3">
            <i class="fa-solid fa-users"></i>
            <span>Employés</span>
        </div>

        <i class="fa-solid fa-chevron-down text-xs"></i>

    </button>

    <ul id="menuEmployes" class="hidden ml-10 mt-2 space-y-2">

        <li>
            <a href="{{ route('employes.index') }}"
                class="block text-sm text-gray-600 hover:text-[#E2721B]">
                Liste des employés
            </a>
        </li>

        <li>
            <a href="{{ route('employes.contrats.index') }}"
                class="block text-sm text-gray-600 hover:text-[#E2721B]">
                Contrats
            </a>
        </li>

        <li>
            <a href="{{ route('employes.conges.index') }}"
                class="block text-sm text-gray-600 hover:text-[#E2721B]">
                Congés
            </a>
        </li>

        <li>
            <a href="{{ route('employes.salaires.index') }}"
                class="block text-sm text-gray-600 hover:text-[#E2721B]">
                Salaires
            </a>
        </li>

    </ul>

</li>

<script>
function toggleEmployes() {
    document.getElementById("menuEmployes").classList.toggle("hidden");
}
</script>

        <li>
            <a href="{{ route('factures.index') ?? '#' }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg cursor-pointer text-gray-600 hover:bg-orange-50 hover:text-[#E2721B] transition">
                <i class="fa-solid fa-file-invoice w-4 text-center"></i>
                <span class="text-sm font-medium">Factures</span>
            </a>
        </li>

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

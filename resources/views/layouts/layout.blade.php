<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portail RH</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-white">

<div class="min-h-screen relative">

    <!-- Overlay (fond assombri derrière le sidebar) -->
    <div
        id="sidebarOverlay"
        class="fixed inset-0 bg-black/30 opacity-0 pointer-events-none transition-opacity duration-300 z-40">
    </div>

    <!-- Sidebar (overlay, ne prend pas de place dans le flux) -->
    <aside
        id="sidebar"
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-2xl -translate-x-full transition-transform duration-300 z-50">

        @include('partials.sidebar')

    </aside>

    <!-- Contenu (occupe toute la largeur, jamais décalé) -->
    <div id="main" class="min-h-screen">

        @include('partials.topbar')

        <main class="p-8">
            @yield('content')
        </main>

    </div>

</div>

<script>

const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const btn = document.getElementById('toggleSidebar');

function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    sidebar.classList.add('translate-x-0');

    overlay.classList.remove('opacity-0', 'pointer-events-none');
    overlay.classList.add('opacity-100', 'pointer-events-auto');
}

function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    sidebar.classList.remove('translate-x-0');

    overlay.classList.add('opacity-0', 'pointer-events-none');
    overlay.classList.remove('opacity-100', 'pointer-events-auto');
}

btn.addEventListener('click', (e) => {
    e.stopPropagation();

    const isOpen = sidebar.classList.contains('translate-x-0');

    if (isOpen) {
        closeSidebar();
    } else {
        openSidebar();
    }
});

overlay.addEventListener('click', closeSidebar);

</script>

</body>
</html>

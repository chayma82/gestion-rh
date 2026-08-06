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

<div class="min-h-screen flex">

    <!-- Sidebar : visible par défaut (dans le flux, pas en overlay).
         Le hamburger réduit sa largeur à 0 pour la faire disparaître,
         c'est l'inverse du comportement précédent. -->
    <aside
        id="sidebar"
        class="w-64 shrink-0 border-r border-gray-200 overflow-hidden transition-all duration-300 ease-in-out">

        {{-- Largeur fixe à l'intérieur : le contenu ne se réarrange pas
             pendant l'animation, il est juste "coupé" par overflow-hidden
             sur le parent quand celui-ci se réduit à w-0. --}}
        <div class="w-64 h-full">
            @include('partials.sidebar')
        </div>

    </aside>

    <!-- Contenu -->
    <div id="main" class="flex-1 min-h-screen min-w-0">

        @include('partials.topbar')

        <main class="p-8">
            @yield('content')
        </main>

    </div>

</div>

<script>

const sidebar = document.getElementById('sidebar');
const btn = document.getElementById('toggleSidebar');

function closeSidebar() {
    sidebar.classList.remove('w-64');
    sidebar.classList.add('w-0', 'border-r-0');
}

function openSidebar() {
    sidebar.classList.remove('w-0', 'border-r-0');
    sidebar.classList.add('w-64');
}

btn.addEventListener('click', () => {
    const isOpen = sidebar.classList.contains('w-64');
    isOpen ? closeSidebar() : openSidebar();
});

</script>

</body>
</html>

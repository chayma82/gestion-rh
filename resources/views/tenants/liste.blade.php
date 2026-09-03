<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des tenants</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-[#F9FAFB] min-h-screen py-10">

    <div class="max-w-6xl mx-auto px-4">

        {{-- En-tête --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                    Gestion des tenants
                </h1>
                <p class="text-gray-500 mt-1 text-sm">
                    Vue d'ensemble des entreprises inscrites sur la plateforme.
                </p>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-gray-600 text-sm font-medium hover:bg-gray-50 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    Déconnexion
                </button>
            </form>
        </div>

        {{-- Messages --}}
        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filtres --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 mb-6">
            <form action="{{ route('tenants.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">

                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Rechercher un tenant par nom..."
                        class="w-full rounded-lg border border-gray-300 pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                </div>

                <div class="relative">
                    <select
                        name="statut"
                        class="appearance-none rounded-lg border border-gray-300 pl-4 pr-9 py-2.5 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 bg-white text-sm">
                        <option value="" @selected(request('statut') == '')>Tous les statuts</option>
                        <option value="en_attente" @selected(request('statut') == 'en_attente')>En attente de validation</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>

                <button type="submit"
                    class="px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                    Filtrer
                </button>

                @if (request('q') || request('statut'))
                    <a href="{{ route('tenants.index') }}"
                        class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-600 font-medium text-sm hover:bg-gray-50 transition text-center">
                        Réinitialiser
                    </a>
                @endif

            </form>
        </div>

        {{-- Tableau --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wide">
                        <th class="text-left px-6 py-3 font-medium">Tenant</th>
                        <th class="text-left px-6 py-3 font-medium">Catégorie</th>
                        <th class="text-left px-6 py-3 font-medium">Utilisateurs</th>
                        <th class="text-left px-6 py-3 font-medium">Statut</th>
                        <th class="text-left px-6 py-3 font-medium">Inscrit le</th>
                        <th class="text-right px-6 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tenants as $tenant)
                        @php
                            $aUnCompteActif = $tenant->utilisateurs()->where('actif', true)->exists();
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('tenants.show', $tenant) }}" class="font-semibold text-gray-900 hover:text-[#E2721B]">
                                    {{ $tenant->nom }}
                                </a>
                                <div class="text-xs text-gray-400">#{{ $tenant->id }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $tenant->tenantCategorie->nom ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $tenant->utilisateurs_count }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($aUnCompteActif)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                        En attente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ optional($tenant->date_creation)->format('d/m/Y') ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end items-center gap-2">

                                    <a href="{{ route('tenants.show', $tenant) }}"
                                        class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 text-xs font-medium hover:bg-gray-50 transition">
                                        Voir
                                    </a>

                                    @if (!$aUnCompteActif)
                                        <form action="{{ route('tenants.valider', $tenant) }}" method="POST"
                                            onsubmit="return confirm('Valider ce tenant et activer son compte administrateur ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-xs font-medium transition">
                                                Valider
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('tenants.suspendre', $tenant) }}" method="POST"
                                            onsubmit="return confirm('Suspendre tous les comptes de ce tenant ?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-3 py-1.5 rounded-lg border border-red-300 text-red-600 text-xs font-medium hover:bg-red-50 transition">
                                                Suspendre
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                Aucun tenant ne correspond à votre recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        @if ($tenants->hasPages())
            <div class="mt-6">
                {{ $tenants->links() }}
            </div>
        @endif

    </div>

</body>

</html>

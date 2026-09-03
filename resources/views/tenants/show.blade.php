<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->nom }} - Détail tenant</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-[#F9FAFB] min-h-screen py-10">

    <div class="max-w-4xl mx-auto px-4">

        {{-- Fil d'ariane --}}
        <a href="{{ route('tenants.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 mb-4">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Retour à la liste des tenants
        </a>

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

        {{-- En-tête + actions --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8 mb-6">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                        {{ $tenant->nom }}
                    </h1>
                    <p class="text-gray-500 mt-1 text-sm">
                        Tenant #{{ $tenant->id }} · Inscrit le {{ optional($tenant->date_creation)->format('d/m/Y à H:i') ?? '—' }}
                    </p>
                </div>

                
            </div>

        </div>

        {{-- Utilisateurs --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden mb-6">

            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">
                    Utilisateurs ({{ $tenant->utilisateurs->count() }})
                </h2>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wide">
                        <th class="text-left px-6 py-3 font-medium">Nom</th>
                        <th class="text-left px-6 py-3 font-medium">Email</th>
                        <th class="text-left px-6 py-3 font-medium">Rôle</th>
                        <th class="text-left px-6 py-3 font-medium">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tenant->utilisateurs as $utilisateur)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $utilisateur->prenom }} {{ $utilisateur->nom }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $utilisateur->email }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $utilisateur->role->nom ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($utilisateur->actif)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                        Inactif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">
                                Aucun utilisateur pour ce tenant.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Rôles --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">
                    Rôles définis ({{ $tenant->roles->count() }})
                </h2>
            </div>

            <div class="p-6 flex flex-wrap gap-2">
                @forelse ($tenant->roles as $role)
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-medium">
                        {{ $role->nom }}
                    </span>
                @empty
                    <p class="text-gray-400 text-sm">Aucun rôle défini pour ce tenant.</p>
                @endforelse
            </div>
        </div>

    </div>

</body>

</html>

@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Annuaire des employés
            </h1>

            <p class="mt-1 text-gray-500 text-sm">
                Gérer vos effectifs et la structure organisationnelle mondiale.
            </p>
        </div>

        <a href="{{ route('employes.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
            <i class="fa-solid fa-user-plus"></i>
            Ajouter un employé
        </a>
    </div>

    <!-- Recherche -->
    <div class="flex gap-3 mb-6">

        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                placeholder="Rechercher par nom ou numéro de contrat..."
                class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
        </div>

        <button
            class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 px-5 rounded-lg text-sm font-medium text-gray-700 transition">
            <i class="fa-solid fa-sliders"></i>
            Filtres avancés
        </button>

    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

        <table class="w-full">

            <thead class="bg-orange-50">

                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Num contrat</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nom</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Département</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>

            </thead>

            <tbody class="divide-y divide-gray-100">

                @php
                    $employes = [

                        [
                            'id' => 1,
                            'num_contrat' => 4,
                            'nom' => 'Elena Rodriguez',
                            'email' => 'elena.rodriguez@rhms.cloud',
                            'departement' => 'Ingénierie',
                            'statut' => 'Actif'
                        ],

                        [
                            'id' => 2,
                            'num_contrat' => 4,
                            'nom' => 'Julian Vance',
                            'email' => 'j.vance@rhms.cloud',
                            'departement' => 'Design',
                            'statut' => 'demissionnaire'
                        ],

                        [
                            'id' => 3,
                            'num_contrat' => 4,
                            'nom' => 'Sarah Sterling',
                            'email' => 's.sterling@rhms.cloud',
                            'departement' => 'Opérations',
                            'statut' => 'En congé'
                        ],

                    ];


                @endphp

                @foreach($employes as $employe)

                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $employe['num_contrat'] }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-900 text-sm">{{ $employe['nom'] }}</div>
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $employe['departement'] }}
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $employe['statut'] }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">

                            {{-- Voir --}}
                            <a href="{{ route('employes.info') }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </a>

                            {{-- Modifier --}}
                            <a href="{{ route('employes.edit') }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-[#E2721B] hover:bg-orange-100 transition">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </a>

                            {{-- Supprimer --}}
                            <form action="#" method="POST"
                                onsubmit="return confirm('Supprimer cet employé ?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

   

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Effectif total</h4>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">1,056</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Actifs</h4>
                <p class="text-2xl font-bold text-green-600 mt-0.5">1,012</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <i class="fa-solid fa-plane"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">En congé</h4>
                <p class="text-2xl font-bold text-yellow-600 mt-0.5">42</p>
            </div>
        </div>

    </div>

</div>

@endsection

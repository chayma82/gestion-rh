@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Annuaire des Contrats
            </h1>

            <p class="mt-1 text-gray-500 text-sm">
                Gérer vos effectifs et la structure organisationnelle mondiale.
            </p>
        </div>

        <a href="{{ route('employes.contrats.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
            <i class="fa-solid fa-user-plus"></i>
            Ajouter un Contrat
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
                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                    Employé
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                    Type
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                    Début
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                    Fin
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                    Durée
                </th>



                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                    Actions
                </th>

            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">

@php
$conges = [

[
'employe'=>'Ahmed Ben Ali',
'type'=>'CDI',
'debut'=>'01/07/2025',
'fin'=>'10/07/2025',
'duree'=>'10 jours',
'statut'=>'Approuvé'
],

[
'employe'=>'Sarra Trabelsi',
'type'=>'Stage',
'debut'=>'15/06/2025',
'fin'=>'18/06/2025',
'duree'=>'4 jours',
'statut'=>'En attente'
],

[
'employe'=>'Mohamed Salah',
'type'=>'Freelance',
'debut'=>'22/06/2025',
'fin'=>'28/06/2025',
'duree'=>'7 jours',
'statut'=>'Refusé'
]

];
@endphp

@foreach($conges as $conge)

<tr class="hover:bg-gray-50 transition">

<td class="px-6 py-4">
<div class="font-semibold text-gray-900">
{{ $conge['employe'] }}
</div>
</td>

<td class="px-6 py-4 text-sm">
{{ $conge['type'] }}
</td>

<td class="px-6 py-4 text-sm">
{{ $conge['debut'] }}
</td>

<td class="px-6 py-4 text-sm">
{{ $conge['fin'] }}
</td>

<td class="px-6 py-4 text-sm">
{{ $conge['duree'] }}
</td>



<td class="px-6 py-4">

<div class="flex items-center gap-2">

    <a href="{{ route('employes.contrats.info') }}"
    class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
    <i class="fa-regular fa-eye text-xs"></i>
    </a>


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

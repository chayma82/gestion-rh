@extends('layouts.layout')

@section('content')

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

    <div class="flex gap-3">

        <x-annuler />

        <a href="{{ route('employes.contrats.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
            <i class="fa-solid fa-user-plus"></i>
            Ajouter un Contrat
        </a>

    </div>
</div>


<!-- Recherche & filtres -->
<form action="{{ route('employes.contrats.index') }}"
    method="GET"
    class="flex flex-col md:flex-row gap-3 mb-6">

    <!-- Recherche -->
    <div class="relative flex-1">

        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

        <input
            id="searchContrat"
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Rechercher contrat ou employé..."
            class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">

    </div>


    <!-- Filtre statut -->
    <div class="relative">

        <select
            name="statut"
            class="appearance-none bg-white border border-gray-300 hover:bg-gray-50 rounded-lg pl-4 pr-10 py-3 text-sm font-medium text-gray-700 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">

            <option value="">Tous les statuts</option>

            @foreach(\App\Models\Contrat::statuts() as $valeur => $libelle)

                <option
                    value="{{ $valeur }}"
                    @selected(request('statut') == $valeur)>
                    {{ $libelle }}
                </option>

            @endforeach

        </select>

        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

    </div>


    <!-- Filtre mois -->
    <div class="relative">

        <select
            name="mois"
            class="appearance-none bg-white border border-gray-300 hover:bg-gray-50 rounded-lg pl-4 pr-10 py-3 text-sm font-medium text-gray-700 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">

            <option value="">Tous les mois</option>

            <option value="01" @selected(request('mois') == '01')>Janvier</option>
            <option value="02" @selected(request('mois') == '02')>Février</option>
            <option value="03" @selected(request('mois') == '03')>Mars</option>
            <option value="04" @selected(request('mois') == '04')>Avril</option>
            <option value="05" @selected(request('mois') == '05')>Mai</option>
            <option value="06" @selected(request('mois') == '06')>Juin</option>
            <option value="07" @selected(request('mois') == '07')>Juillet</option>
            <option value="08" @selected(request('mois') == '08')>Août</option>
            <option value="09" @selected(request('mois') == '09')>Septembre</option>
            <option value="10" @selected(request('mois') == '10')>Octobre</option>
            <option value="11" @selected(request('mois') == '11')>Novembre</option>
            <option value="12" @selected(request('mois') == '12')>Décembre</option>

        </select>

        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

    </div>


    <!-- Bouton filtrer -->
    <button
        type="submit"
        class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 px-5 rounded-lg text-sm font-medium text-gray-700 transition">

        <i class="fa-solid fa-sliders"></i>
        Filtrer

    </button>

</form>


<!-- Tableau -->
<div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

    <div class="overflow-x-auto max-h-[650px] overflow-y-auto">

        <table class="w-full min-w-[900px]">

            <!-- Header tableau -->
            <thead class="bg-orange-50">

                <tr>

                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                        Num Contrat
                    </th>

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
                        Statut
                    </th>

                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                        Actions
                    </th>

                </tr>

            </thead>


            <!-- Corps -->
            <tbody id="tableContrats" class="divide-y divide-gray-100">

                @forelse($contrats as $contrat)

                    @php

                        /*
                         * Les contrats résiliés et expirés sont considérés
                         * comme historiques.
                         *
                         * Ils ont exactement le même comportement :
                         * - modification désactivée
                         * - résiliation désactivée
                         * - boutons gris
                         */
                        $estHistorique = in_array($contrat->statut, [
                            'resilie',
                            'expire'
                        ]);

                    @endphp


                    <tr class="hover:bg-gray-50 transition">


                        <!-- Numéro contrat -->
                        <td class="px-6 py-4 text-sm">

                            {{ $contrat->numcontrat }}

                        </td>


                        <!-- Employé -->
                        <td class="px-6 py-4">

                            <div class="font-semibold text-gray-900">

                                {{ $contrat->employe->nom_complet }}

                            </div>

                        </td>


                        <!-- Type -->
                        <td class="px-6 py-4 text-sm">

                            {{ $contrat->typeContrat }}

                        </td>


                        <!-- Date début -->
                        <td class="px-6 py-4 text-sm">

                            {{ $contrat->date_debut->format('Y-m-d') }}

                        </td>


                        <!-- Date fin -->
                        <td class="px-6 py-4 text-sm">

                            @if($contrat->statut == 'resilie')

                                {{ $contrat->date_modification?->format('Y-m-d') }}

                            @elseif($contrat->date_fin)

                                {{ $contrat->date_fin->format('Y-m-d') }}

                            @else

                                Contrat en cours

                            @endif

                        </td>


                        <!-- Statut -->
                        <td class="px-6 py-4">

                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $contrat->statut_badge['classes'] }}">

                                {{ $contrat->statut_badge['label'] }}

                            </span>

                        </td>


                        <!-- Actions -->
                        <td class="px-6 py-4">

                            <div class="flex items-center gap-2">


                                <!-- ========================= -->
                                <!-- VOIR -->
                                <!-- ========================= -->

                                <a
                                    href="{{ route('employes.info', $contrat->employe->id) }}"
                                    title="Voir le contrat"
                                    class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition">

                                    <i class="fa-regular fa-eye text-xs"></i>

                                </a>


                                <!-- ========================= -->
                                <!-- MODIFIER -->
                                <!-- ========================= -->

                                @if(!$estHistorique)

                                    <!-- Contrat actif / à venir -->

                                    <a
                                        href="{{ route('employes.contrats.edit', $contrat->id) }}"
                                        title="Modifier le contrat"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-[#E2721B] hover:bg-orange-100 transition">

                                        <i class="fa-solid fa-pen text-xs"></i>

                                    </a>

                                @else

                                    <!-- Contrat résilié OU expiré -->

                                    <span
                                        title="Contrat terminé : non modifiable"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-300 cursor-not-allowed">

                                        <i class="fa-solid fa-pen text-xs"></i>

                                    </span>

                                @endif


                                <!-- ========================= -->
                                <!-- RÉSILIER -->
                                <!-- ========================= -->

                                @if(!$estHistorique)

                                    <!-- Actif / À venir -->

                                    <form
                                        action="{{ route('employes.contrats.resilier', $contrat->id) }}"
                                        method="POST"
                                        class="js-form-resiliation"
                                        onsubmit="return confirmerResiliation(this);">

                                        @csrf

                                        @method('PUT')

                                        <input
                                            type="hidden"
                                            name="archiver_employe"
                                            value="0">


                                        <button
                                            type="submit"
                                            title="Résilier le contrat"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition">

                                            <i class="fa-solid fa-trash text-xs"></i>

                                        </button>

                                    </form>

                                @else

                                    <!-- Contrat résilié OU expiré -->

                                    <span
                                        title="Contrat terminé : action indisponible"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-300 cursor-not-allowed">

                                        <i class="fa-solid fa-trash text-xs"></i>

                                    </span>

                                @endif


                            </div>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="px-6 py-12 text-center text-gray-400 text-sm">

                            Aucun contrat trouvé.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>


<!-- Pagination -->
@if(isset($contrats) && $contrats instanceof \Illuminate\Pagination\LengthAwarePaginator)

    <div class="flex justify-between items-center mt-5">

        <span class="text-sm text-gray-500">

            Affichage
            {{ $contrats->firstItem() ?? 0 }}
            -
            {{ $contrats->lastItem() ?? 0 }}
            sur
            {{ $contrats->total() }}
            contrats

        </span>


        <div class="flex items-center gap-2">

            {{ $contrats->onEachSide(1)->links() }}

        </div>

    </div>

@endif


<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">


    <!-- Total -->
    <div
        class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">

        <div
            class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">

            <i class="fa-solid fa-users"></i>

        </div>


        <div>

            <h4 class="text-xs text-gray-500 uppercase tracking-wide">

                Effectif total

            </h4>

            <p class="text-2xl font-bold text-gray-900 mt-0.5">

                {{ $totalcontrat }}

            </p>

        </div>

    </div>


    <!-- Actifs -->
    <div
        class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">

        <div
            class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">

            <i class="fa-solid fa-user-check"></i>

        </div>


        <div>

            <h4 class="text-xs text-gray-500 uppercase tracking-wide">

                Actifs

            </h4>

            <p class="text-2xl font-bold text-green-600 mt-0.5">

                {{ $totalcontratActif }}

            </p>

        </div>

    </div>


    <!-- Expirés -->
    <div
        class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">

        <div
            class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">

            <i class="fa-solid fa-plane"></i>

        </div>


        <div>

            <h4 class="text-xs text-gray-500 uppercase tracking-wide">

                Expiré

            </h4>

            <p class="text-2xl font-bold text-yellow-600 mt-0.5">

                {{ $totalcontratExpire }}

            </p>

        </div>

    </div>

</div>

@endsection

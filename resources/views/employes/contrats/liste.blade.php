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
    <form action="{{ route('employes.contrats.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-6">

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

        <div class="relative">
            <select name="mois"
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

        <button type="submit"
            class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 px-5 rounded-lg text-sm font-medium text-gray-700 transition">
            <i class="fa-solid fa-sliders"></i>
            Filtrer
        </button>

    </form>

    <!-- Tableau -->
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4_25px_rgba(0,0,0,0.04)] overflow-hidden">

        <div class="overflow-x-auto max-h-[650px] overflow-y-auto">
            <table class="w-full min-w-[900px]">

                <thead class="bg-orange-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Num Contrat</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Employé</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Type</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Début</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Fin</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Statut</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody id="tableContrats" class="divide-y divide-gray-100">

                    @foreach($contrats as $contrat)

                    @php
                        // Un contrat résilié appartient à l'historique : ni
                        // modifiable, ni résiliable une seconde fois (même
                        // logique que pour un employé archivé).
                        $estResilie = $contrat->statut === 'resilie';
                    @endphp

                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm">{{ $contrat->numcontrat }}</td>
                        <td class="px-6 py-4"><div class="font-semibold text-gray-900">{{ $contrat->employe->nom_complet }}</div></td>
                        <td class="px-6 py-4 text-sm">{{ $contrat->typeContrat }}</td>
                        <td class="px-6 py-4 text-sm">{{ $contrat->date_debut->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($contrat->statut == 'resilie')
                                {{ $contrat->date_modification?->format('Y-m-d') }}
                            @elseif($contrat->date_fin)
                                {{ $contrat->date_fin->format('Y-m-d') }}
                            @else
                                Contrat en cours
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $contrat->statut }}</td>


                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">

                                        {{-- Voir --}}
                                        <a href="{{ route('employes.info', $contrat->employe->id) }}"
                                            class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                            <i class="fa-regular fa-eye text-xs"></i>
                                        </a>

                                        {{-- Modifier : désactivé si le contrat est déjà résilié --}}
                                        @if(!$estResilie)
                                        <a href="{{ route('employes.contrats.edit', $contrat->id) }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-[#E2721B] hover:bg-orange-100 transition">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        @else
                                        <span title="Contrat résilié : non modifiable"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-300 cursor-not-allowed">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </span>
                                        @endif

                                        {{-- Résilier (remplace la "suppression") : deux
                                             confirmations successives.
                                             1) confirme la résiliation elle-même
                                             2) demande si on archive l'employé tout de
                                                suite, ou si on le laisse en "fin_contrat"
                                                pour l'archiver plus tard depuis sa fiche --}}
                                        @if(!$estResilie)
                                        <form action="{{ route('employes.contrats.resilier', $contrat->id) }}"
                                            method="POST"
                                            class="js-form-resiliation"
                                            onsubmit="return confirmerResiliation(this);">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="archiver_employe" value="0">

                                            <button type="submit"
                                                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        @else
                                        <span title="Contrat déjà résilié"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-300 cursor-not-allowed">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </span>
                                        @endif

                                    </div>
                                </td>

                    </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Effectif total</h4>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $totalcontrat }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Actifs</h4>
                <p class="text-2xl font-bold text-green-600 mt-0.5">{{ $totalcontratActif }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <i class="fa-solid fa-plane"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Expiré</h4>
                <p class="text-2xl font-bold text-yellow-600 mt-0.5">{{ $totalcontratExpire }}</p>
            </div>
        </div>

    </div>

</div>

<script>

// Résiliation en 2 temps :
// 1) "Résilier ce contrat ?" -> si Annuler, on arrête tout, rien n'est envoyé.
// 2) "Archiver l'employé associé ?" -> pose la valeur du champ caché
//    archiver_employe (1 = archive tout de suite, 0 = reste en "fin_contrat").
function confirmerResiliation(form) {

    const confirmationResiliation = confirm(
        'Résilier ce contrat ?  '
    );

    if (!confirmationResiliation) {
        return false;
    }

    const confirmationArchivage = confirm(
        'Voulez-vous archiver directement l\'employé associé ? '
    );

    form.querySelector('input[name="archiver_employe"]').value = confirmationArchivage ? '1' : '0';

    return true;
}

let timer;

document.getElementById('searchContrat')
.addEventListener('input', function () {

    clearTimeout(timer);

    let recherche = this.value;

    timer = setTimeout(function () {

        fetch("{{ route('employes.contrats.index') }}?q=" + recherche)

        .then(response => response.text())

        .then(html => {

            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');

            document.getElementById('tableContrats').innerHTML =
                doc.getElementById('tableContrats').innerHTML;

        });

    }, 300);

});

</script>

@endsection

@extends('layouts.layout')

@section('content')

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

    <div class="flex gap-3">

        <x-annuler />

        <a href="{{ route('employes.archives') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-sm font-medium transition">
            <i class="fa-solid fa-box-archive text-gray-400"></i>
            Archives
        </a>

        <a href="{{ route('employes.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium">
            <i class="fa-solid fa-user-plus"></i>
            Ajouter un employé
        </a>

    </div>
</div>


<!-- Recherche & filtres -->
<form action="{{ route('employes.index') }}" method="GET"
    class="flex flex-col md:flex-row gap-3 mb-6">

    <div class="relative flex-1">

        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

        <input
            id="searchEmploye"
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Rechercher par nom ou matricule d'employe..."
            class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">

    </div>


    {{-- Statut = statutEmploye (colonne employe.statutEmploye) --}}
    <div class="relative">

        <select name="statut"
            class="appearance-none bg-white border border-gray-300 hover:bg-gray-50 rounded-lg pl-4 pr-10 py-3 text-sm font-medium text-gray-700 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">

            <option value="">Tous les statuts</option>

            <option value="attente_contrat"
                @selected(request('statut') == 'attente_contrat')>
                En attente de contrat
            </option>

            <option value="attente_prise_poste"
                @selected(request('statut') == 'attente_prise_poste')>
                Attente prise de poste
            </option>

            <option value="actif"
                @selected(request('statut') == 'actif')>
                Actif
            </option>

            <option value="en_conge"
                @selected(request('statut') == 'en_conge')>
                En congé
            </option>

            <option value="suspendu"
                @selected(request('statut') == 'suspendu')>
                Suspendu
            </option>

            <option value="fin_contrat"
                @selected(request('statut') == 'fin_contrat')>
                Fin de contrat
            </option>

            <option value="demissionnaire"
                @selected(request('statut') == 'demissionnaire')>
                Démissionnaire
            </option>

        </select>

        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

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

    <table class="w-full">

        <thead class="bg-orange-50">

            <tr>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Matricule
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Nom
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Département
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Poste
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Statut
                </th>

                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    Actions
                </th>

            </tr>

        </thead>


        <tbody id="tableEmployes" class="divide-y divide-gray-100">

            @forelse($employes as $employe)

                @php
    $contrat = $employe->contrat_recent;
    $statutBadge = $employe->statut_badge;
@endphp


                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $employe->matricule }}
                    </td>


                    <td class="px-6 py-4">

                        <div class="font-semibold text-gray-900 text-sm">
                            {{ $employe->nom_complet }}
                        </div>

                    </td>


                    {{-- Département provenant du contrat --}}
                    <td class="px-6 py-4 text-sm text-gray-600">

                        {{ $contrat?->departement?->libelle ?? '—' }}

                    </td>


                    {{-- Poste provenant du contrat --}}
                    <td class="px-6 py-4 text-sm text-gray-600">

                        {{ $contrat?->poste?->libelle ?? '—' }}

                    </td>


                    {{-- Statut provenant de l'employé (statutEmploye) --}}
                    <td class="px-6 py-4">

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statutBadge['classes'] }}">

                            {{ $statutBadge['label'] }}

                        </span>

                    </td>


                    <td class="px-6 py-4">

                        <div class="flex items-center gap-2">

                            {{-- Voir --}}
                            <a href="{{ route('employes.info', $employe->id) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition">

                                <i class="fa-regular fa-eye text-xs"></i>

                            </a>


                            {{-- Modifier --}}
                            <a href="{{ route('employes.edit', $employe->id) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-[#E2721B] hover:bg-orange-100 transition">

                                <i class="fa-solid fa-pen text-xs"></i>

                            </a>


                            {{-- Archiver --}}
                            <form action="{{ route('employes.destroy', $employe->id) }}"
                                method="POST"
                                onsubmit="return confirm('Archiver cet employé ?');">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-600 hover:bg-red-100 transition">

                                    <i class="fa-solid fa-box-archive text-xs"></i>

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>


            @empty

                <tr>

                    <td colspan="6"
                        class="px-6 py-12 text-center text-gray-400 text-sm">

                        Aucun employé trouvé.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>


<!-- Pagination -->

@if(isset($employes) && $employes instanceof \Illuminate\Pagination\LengthAwarePaginator)

    <div class="flex justify-between items-center mt-5">

        <span class="text-sm text-gray-500">

            Affichage
            {{ $employes->firstItem() ?? 0 }}
            -
            {{ $employes->lastItem() ?? 0 }}
            sur
            {{ $employes->total() }}
            employés

        </span>


        <div class="flex items-center gap-2">

            {{ $employes->onEachSide(1)->links() }}

        </div>

    </div>

@endif


<!-- Statistiques -->

<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">


    <!-- Effectif -->

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">

        <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">

            <i class="fa-solid fa-users"></i>

        </div>

        <div>

            <h4 class="text-xs text-gray-500 uppercase tracking-wide">
                Effectif total
            </h4>

            <p class="text-2xl font-bold text-gray-900 mt-0.5">
                {{ $totalemploye }}
            </p>

        </div>

    </div>


    <!-- Actifs -->

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">

        <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">

            <i class="fa-solid fa-user-check"></i>

        </div>

        <div>

            <h4 class="text-xs text-gray-500 uppercase tracking-wide">
                Actifs
            </h4>

            <p class="text-2xl font-bold text-green-600 mt-0.5">
                {{ $employesActifs }}
            </p>

        </div>

    </div>


    <!-- En congé -->

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">

        <div class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">

            <i class="fa-solid fa-plane"></i>

        </div>

        <div>

            <h4 class="text-xs text-gray-500 uppercase tracking-wide">
                En congé
            </h4>

            <p class="text-2xl font-bold text-yellow-600 mt-0.5">
                {{ $employesConge }}
            </p>

        </div>

    </div>

</div>


<script>

let timer;

const searchInput = document.getElementById('searchEmploye');

if (searchInput) {

    searchInput.addEventListener('input', function () {

        clearTimeout(timer);

        const recherche = this.value;

        timer = setTimeout(function () {

            const url = new URL("{{ route('employes.index') }}", window.location.origin);

            url.searchParams.set('q', recherche);

            // Conserver le filtre statut
            const statut = document.querySelector('select[name="statut"]')?.value;

            if (statut) {
                url.searchParams.set('statut', statut);
            }

            // Conserver le filtre mois
            const mois = document.querySelector('select[name="mois"]')?.value;

            if (mois) {
                url.searchParams.set('mois', mois);
            }

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })

            .then(response => {

                if (!response.ok) {
                    throw new Error('Erreur lors de la recherche.');
                }

                return response.text();

            })

            .then(html => {

                const parser = new DOMParser();

                const doc = parser.parseFromString(html, 'text/html');

                const nouveauTableau =
                    doc.getElementById('tableEmployes');

                const tableauActuel =
                    document.getElementById('tableEmployes');

                if (nouveauTableau && tableauActuel) {

                    tableauActuel.innerHTML =
                        nouveauTableau.innerHTML;

                }

            })

            .catch(error => {

                console.error(error);

            });

        }, 300);

    });

}

</script>

@endsection

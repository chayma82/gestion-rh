@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Annuaire des Congés
            </h1>

            <p class="mt-1 text-gray-500 text-sm">
                Suivre les demandes et l'historique des congés des employés.
            </p>
        </div>
        <div class="flex gap-3">
        <x-annuler />
        <a href="{{ route('employes.conges.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
            <i class="fa-solid fa-user-plus"></i>
            Ajouter un conge
        </a>
        </div>

    </div>

    {{-- Message de succès --}}
    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Recherche & filtres -->
    <form action="{{ route('employes.conges.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-6">

        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                id="searchConge"
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher par employé..."
                class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
        </div>

        <div class="relative">
            <select name="type"
                class="appearance-none bg-white border border-gray-300 hover:bg-gray-50 rounded-lg pl-4 pr-10 py-3 text-sm font-medium text-gray-700 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                <option value="">Tous les types</option>
                <option value="paye" @selected(request('type') == 'paye')>Congés Payés (CP)</option>
                <option value="sans_solde" @selected(request('type') == 'sans_solde')>Sans solde</option>
                <option value="maladie" @selected(request('type') == 'maladie')>Maladie</option>
            </select>
            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        {{-- Filtre par statut (a_venir / en_cours / termine), même
             vocabulaire que le badge affiché dans le tableau --}}
        <div class="relative">
            <select name="statut"
                class="appearance-none bg-white border border-gray-300 hover:bg-gray-50 rounded-lg pl-4 pr-10 py-3 text-sm font-medium text-gray-700 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                <option value="">Tous les statuts</option>
                @foreach(\App\Models\Conge::statuts() as $valeur => $libelle)
                    <option value="{{ $valeur }}" @selected(request('statut') == $valeur)>{{ $libelle }}</option>
                @endforeach
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
<div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

    <div class="overflow-x-auto max-h-[650px] overflow-y-auto">

        <table class="w-full min-w-[900px]">

            <thead class="bg-orange-50">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Employé
                    </th>

                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Type de congé
                    </th>

                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Date début
                    </th>

                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Date fin
                    </th>

                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Durée
                    </th>

                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Statut
                    </th>
                </tr>
            </thead>

            <tbody id="tableConges" class="divide-y divide-gray-100">

                @forelse($conges as $conge)

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-900 text-sm">
                                {{ $conge->employe?->matricule_nom_complet
                                    ?? (($conge->employe?->nom ?? '') . ' ' . ($conge->employe?->prenom ?? '')) }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-600">
                                {{ $conge->type_conge }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                        </td>

                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($conge->date_debut)
                                ->diffInDays(\Carbon\Carbon::parse($conge->date_fin)) + 1 }}
                            jour(s)
                        </td>

                        <td class="px-6 py-4">

                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium {{ $conge->statut_badge['classes'] }}">

                                {{ $conge->statut_badge['label'] }}

                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6"
                            class="px-6 py-12 text-center text-gray-400 text-sm">
                            Aucun congé trouvé.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>
    <!-- Pagination -->
    @if(isset($conges) && $conges instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="flex justify-between items-center mt-5">
            <span class="text-sm text-gray-500">
                Affichage {{ $conges->firstItem() ?? 0 }} - {{ $conges->lastItem() ?? 0 }} sur {{ $conges->total() }} congés
            </span>
            <div class="flex items-center gap-2">
                {{ $conges->onEachSide(1)->links() }}
            </div>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Employés en congé aujourd'hui</h4>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $enCongeAujourdhui }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
                <i class="fa-solid fa-umbrella-beach"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Employés en congé demain</h4>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $congesDemain}}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Employés en congé cette semaine</h4>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $congesCetteSemaine  }}</p>
            </div>
        </div>

    </div>

</div>

<script>

let timer;

document.getElementById('searchConge')
.addEventListener('input', function () {

    clearTimeout(timer);

    let recherche = this.value;

    timer = setTimeout(function () {

        fetch("{{ route('employes.conges.index') }}?q=" + recherche)

        .then(response => response.text())

        .then(html => {

            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');

            document.getElementById('tableConges').innerHTML =
                doc.getElementById('tableConges').innerHTML;

        });

    }, 300);

});

</script>

@endsection

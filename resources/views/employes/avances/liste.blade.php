@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Annuaire des avances
            </h1>
            <p class="mt-1 text-gray-500 text-sm">
                Suivre les avances sur salaire accordées aux employés.
            </p>
        </div>
        <div class="flex gap-3">
            <x-annuler />
            <a href="{{ route('employes.avances.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                Ajouter une avance
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
    <form action="{{ route('employes.avances.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-6">

        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher par employé..."
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
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

        <div class="overflow-x-auto max-h-[650px] overflow-y-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Employé</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Motif</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($avances as $avance)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900 text-sm">{{ $avance->employe->nom_complet ?? '_' }}</span>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $avance->date_avance->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $avance->motif ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-red-500">
                                -{{ number_format($avance->montant, 2) }} DT
                            </td>

                            form>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                Aucune avance trouvée.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

    </div>

    <!-- Pagination -->
    @if(isset($avances) && $avances instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="flex justify-between items-center mt-5">
            <span class="text-sm text-gray-500">
                Affichage {{ $avances->firstItem() ?? 0 }} - {{ $avances->lastItem() ?? 0 }} sur {{ $avances->total() }} avances
            </span>
            <div class="flex items-center gap-2">
                {{ $avances->onEachSide(1)->links() }}
            </div>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Total Avances</h4>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($totalAvances ?? 0, 2) }} DT</p>
            </div>
        </div>
    </div>

</div>

@endsection

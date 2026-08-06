@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Annuaire des salaires
            </h1>

            <p class="mt-1 text-gray-500 text-sm flex items-center gap-2">
                Gérer la paie et suivre les versements des employés.
                <span class="inline-flex items-center gap-1 text-xs text-gray-400">
                    · Jour de paie configuré :
                    <strong class="text-gray-600">parametrePaie->jour_paiement ?? 3 </strong>
                    <button type="button" onclick="toggleConfigPaie()" class="text-orange-600 hover:underline">
                        (modifier)
                    </button>
                </span>
            </p>
        </div>

        <div class="flex items-center gap-3">

            <x-annuler />
            <form action="{{ route('employes.salaires.payerTous') }}" method="POST"
                  onsubmit="return confirm('Confirmer le paiement de tous les salaires en attente pour ce mois ?');">
                @csrf
                <input type="hidden" name="periode" value="{{ request('mois') ? now()->format('Y') . '-' . request('mois') : now()->format('Y-m') }}">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium shadow-md shadow-green-600/10 transition">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    Payer tout
                </button>
            </form>

            <a href="{{ route('employes.salaires.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-user-plus"></i>
                changer le salaire
            </a>

        </div>

    </div>

    <!-- Panneau de configuration du jour de paie (masqué par défaut) -->
    <div id="configPaie" class="hidden mb-6 bg-orange-50 border border-orange-200 rounded-xl p-4">
        <form action="#" method="POST" class="flex items-end gap-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Jour du mois pour générer les salaires</label>
                <input type="number" name="jour_paiement" min="1" max="28"
                       value="{{ $parametrePaie->jour_paiement ?? 3 }}"
                       class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium transition">
                Enregistrer
            </button>
            <button type="button" onclick="toggleConfigPaie()"
                class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">
                Annuler
            </button>
        </form>
    </div>

    {{-- Message de succès --}}
    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Recherche & filtres -->
    <form action="{{ route('employes.salaires.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-6">

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
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4_25px_rgba(0,0,0,0.04)] overflow-hidden">

   <div class="overflow-x-auto max-h-[650px] overflow-y-auto">
        <table class="w-full min-w-[900px]">
            <thead class="bg-orange-50">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Employé</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Période</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Salaire brut</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Primes</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Avance</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Net à payer</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse($salaires as $salaire)
                    @php
                        $net = $salaire->salaire_brut + $salaire->total_primes - $salaire->total_avances;
                    @endphp
                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4">
                            <span class="font-semibold text-gray-900 text-sm">{{ $salaire->employe->matricule_nom_complet ?? '_' }}</span>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $salaire->periode }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ number_format($salaire->salaire_brut, 2) }} DT
                        </td>

                        <td class="px-6 py-4 text-sm text-green-600 font-medium">
                            +{{ number_format($salaire->total_primes, 2) }} DT
                        </td>

                        <td class="px-6 py-4 text-sm font-semibold text-red-500">
                            -{{ number_format($salaire->total_avances, 2) }} DT
                        </td>

                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            {{ number_format($net, 2) }} DT
                        </td>

                        <td class="px-6 py-4">
                            @if($salaire->statut === 'paye')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                                    <i class="fa-solid fa-check"></i> Payé
                                </span>
                                @if($salaire->date_paiement)
                                    <div class="text-[11px] text-gray-400 mt-1">
                                        le {{ $salaire->date_paiement->format('d/m/Y') }}
                                    </div>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-medium">
                                    <i class="fa-solid fa-clock"></i> En attente
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            @if($salaire->statut === 'paye')
                                <form action="{{ route('employes.salaires.annuler', $salaire->id) }}" method="POST"
                                        onsubmit="return confirm('Annuler ce paiement ?');">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium transition">
                                        Annuler paiement
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('employes.salaires.payer', $salaire->id) }}" method="POST"
                                        onsubmit="return confirm('Marquer ce salaire comme payé ?');">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-xs font-medium transition">
                                        Marquer payé
                                    </button>
                                </form>
                            @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Aucun salaire trouvé.
                        </td>
                    </tr>

                @endforelse

            </tbody>

                </table>
    </div>

</div>

    <!-- Pagination -->
    @if(isset($salaires) && $salaires instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="flex justify-between items-center mt-5">
            <span class="text-sm text-gray-500">
                Affichage {{ $salaires->firstItem() ?? 0 }} - {{ $salaires->lastItem() ?? 0 }} sur {{ $salaires->total() }} salaires
            </span>
            <div class="flex items-center gap-2">
                {{ $salaires->onEachSide(1)->links() }}
            </div>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Masse Salariale</h4>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($masseSalariale ?? 0, 2) }} DT</p>
            </div>
        </div>
    </div>

</div>

<script>
function toggleConfigPaie() {
    document.getElementById('configPaie').classList.toggle('hidden');
}
</script>

@endsection

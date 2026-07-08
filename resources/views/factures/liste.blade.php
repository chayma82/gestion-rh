@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Annuaire des Factures
            </h1>

            <p class="mt-1 text-gray-500 text-sm">
                Gérer votre facturation et suivre les paiements clients.
            </p>
        </div>

        
    </div>

    {{-- Message de succès --}}
    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Recherche & filtres -->
    <form action="{{ route('factures.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-6">

        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher par numéro ou client..."
                class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
        </div>

        <div class="relative">
            <select name="periode"
                class="appearance-none bg-white border border-gray-300 hover:bg-gray-50 rounded-lg pl-4 pr-10 py-3 text-sm font-medium text-gray-700 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                <option value="30">Derniers 30 jours</option>
                <option value="90">Derniers 90 jours</option>
                <option value="365">Cette année</option>
            </select>
            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        <div class="relative">
            <select name="statut"
                class="appearance-none bg-white border border-gray-300 hover:bg-gray-50 rounded-lg pl-4 pr-10 py-3 text-sm font-medium text-gray-700 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                <option value="">Tous les statuts</option>
                <option value="payee">Payée</option>
                <option value="attente">En attente</option>
                <option value="retard">En retard</option>
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

        <table class="w-full">

            <thead class="bg-orange-50">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Numéro</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date d'émission</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date échéance</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @php
                    $factures = $factures ?? [
                        ['numero' => '#FAC-2023-0892', 'emission' => '12 Oct 2023', 'echeance' => '12 Dec 2023', 'montant' => '1,240.00 €', 'statut' => 'Payée'],
                        ['numero' => '#FAC-2023-0893', 'emission' => '15 Oct 2023', 'echeance' => '15 Dec 2023', 'montant' => '450.00 €', 'statut' => 'En attente'],
                        ['numero' => '#FAC-2023-0894', 'emission' => '01 Oct 2023', 'echeance' => '01 Dec 2023', 'montant' => '3,890.50 €', 'statut' => 'En retard'],
                        ['numero' => '#FAC-2023-0895', 'emission' => '22 Oct 2023', 'echeance' => '22 Dec 2023', 'montant' => '915.00 €', 'statut' => 'Payée'],
                    ];
                @endphp

                @forelse($factures as $facture)

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 text-sm">{{ $facture['numero'] }}</div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $facture['emission'] }}
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $facture['echeance'] }}
                        </td>

                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                            {{ $facture['montant'] }}
                        </td>

                        <td class="px-6 py-4">
                            @php
                                $badgeClasses = match($facture['statut']) {
                                    'Payée'      => 'bg-green-100 text-green-700',
                                    'En attente' => 'bg-yellow-100 text-yellow-700',
                                    'En retard'  => 'bg-red-100 text-red-700',
                                    default      => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            <span class="{{ $badgeClasses }} px-3 py-1 rounded-full text-xs font-medium">
                                {{ $facture['statut'] }}
                            </span>
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">

                                <a href="#"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                    <i class="fa-regular fa-eye text-xs"></i>
                                </a>

                                <a href="#"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-[#E2721B] hover:bg-orange-100 transition">
                                    <i class="fa-solid fa-download text-xs"></i>
                                </a>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Aucune facture trouvée.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <!-- Pagination -->
    @if(isset($factures) && $factures instanceof \Illuminate\Pagination\LengthAwarePaginator)

        <div class="flex justify-between items-center mt-5">

            <span class="text-sm text-gray-500">
                Affichage {{ $factures->firstItem() ?? 0 }} - {{ $factures->lastItem() ?? 0 }} sur {{ $factures->total() }} factures
            </span>

            <div class="flex items-center gap-2">
                {{ $factures->onEachSide(1)->links() }}
            </div>

        </div>

    @endif

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8">

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Total Facturé</h4>
                <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $totalFacture ?? '120' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">En Attente</h4>
                <p class="text-2xl font-bold text-yellow-600 mt-0.5">{{ $enAttente ?? '100' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 class="text-xs text-gray-500 uppercase tracking-wide">Impayés</h4>
                <p class="text-2xl font-bold text-red-600 mt-0.5">{{ $impayes ?? '20' }}</p>
            </div>
        </div>

    </div>

</div>

@endsection

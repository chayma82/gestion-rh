@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Factures fournisseurs (Achats)
            </h1>
            <p class="mt-1 text-gray-500 text-sm">
                Gérer les factures d'achat et suivre les paiements fournisseurs.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('factures.achats.archives') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-sm font-medium transition">
                <i class="fa-solid fa-box-archive text-gray-400"></i>
                Archives
            </a>
            <form action="{{ route('factures.achats.payerTout') }}" method="POST"
                    onsubmit="return confirm('Marquer toutes les factures non payées comme payées ?');">
                @csrf
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium shadow-md shadow-green-600/10 transition">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    Payer tout
                </button>
            </form>

            <a href="{{ route('factures.achats.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-file-invoice"></i>
                Nouvelle facture
            </a>
        </div>

    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <!-- Recherche & filtres -->
    <form action="{{ route('factures.achats.index') }}" method="GET" id="formRechercheFactures" class="flex flex-col md:flex-row gap-3 mb-6">

        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                id="searchFacture"
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher par numéro de facture ou fournisseur..."
                class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
        </div>

        <div class="relative">
<select name="mois" id="moisFacture" onchange="this.form.submit()"
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

    </form>

    <script>
        let timer;
        document.getElementById('searchFacture').addEventListener('input', function () {
            clearTimeout(timer);
            let recherche = this.value;
            timer = setTimeout(function () {
                fetch("{{ route('factures.achats.index') }}?q=" + recherche)
                    .then(response => response.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        document.getElementById('tableFactures').innerHTML =
                            doc.getElementById('tableFactures').innerHTML;
                    });
            }, 300);
        });
    </script>

    <!-- Tableau -->
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

        <div class="overflow-x-auto max-h-[650px] overflow-y-auto" id="tableFactures">
            <table class="w-full min-w-[900px]">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">N° Facture</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Fournisseur</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date d'émission</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Échéance</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant TTC</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Paiement</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($factures as $facture)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900 text-sm">{{ $facture->numFacture }}</span>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $facture->fournisseur?->nom ?? '-' }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $facture->dateEmissionFacture->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $facture->date_echeance ? $facture->date_echeance->format('d/m/Y') : '—' }}
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($facture->montant_ttc, 2) }} DT
                            </td>

                            <td class="px-6 py-4">
                                @switch($facture->statut)
                                    @case('payee')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                                            <i class="fa-solid fa-check"></i> Payée
                                        </span>
                                        @break
                                    @case('en_retard')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-medium">
                                            <i class="fa-solid fa-triangle-exclamation"></i> En retard
                                        </span>
                                        @break
                                    @case('en_attente')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-medium">
                                            <i class="fa-solid fa-paper-plane"></i> En attente
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-orange-50 text-orange-600 text-xs font-medium">
                                            <i class="fa-solid fa-clock"></i> Brouillon
                                        </span>
                                @endswitch
                            </td>

                            <td class="px-6 py-4">
                                @if($facture->statut === 'payee')
                                    <form action="{{ route('factures.achats.annulerPaiement', $facture->id) }}" method="POST"
                                            onsubmit="return confirm('Annuler le paiement de cette facture ?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs font-medium transition">
                                            Annuler paiement
                                        </button>
                                        <p class="mt-1 text-[11px] text-gray-400">le {{ $facture->updated_at->format('d/m/Y') }}</p>
                                    </form>
                                @else
                                    <form action="{{ route('factures.achats.marquerPayee', $facture->id) }}" method="POST"
                                            onsubmit="return confirm('Marquer cette facture comme payée ?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-xs font-medium transition">
                                            Marquer payée
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">

                                    <a href="{{ route('factures.achats.info', $facture->id) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                        <i class="fa-regular fa-eye text-xs"></i>
                                    </a>

                                    @if($facture->chemin_pdf)
                                        <a href="{{ asset('storage/' . $facture->chemin_pdf) }}" target="_blank"
                                            class="px-3 py-1.5 rounded-lg bg-white border border-gray-300 hover:bg-gray-50 text-[#128b2c] text-xs font-medium transition">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('factures.achats.edit', $facture->id) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-[#E2721B] hover:bg-orange-100 transition">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>

                                    <form action="{{ route('factures.achats.destroy', $facture->id) }}" method="POST"
                                        onsubmit="return confirm('Archiver cette facture ?');">
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

                    @empty

                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                                Aucune facture trouvée.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

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

</div>

<!-- Statistiques -->
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-5 mt-8">
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
            <i class="fa-solid fa-file-invoice"></i>
        </div>
        <div>
            <h4 class="text-xs text-gray-500 uppercase tracking-wide">Total factures</h4>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $totalFactures }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
            <i class="fa-solid fa-check"></i>
        </div>
        <div>
            <h4 class="text-xs text-gray-500 uppercase tracking-wide">Payées</h4>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $facturesPayees }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
            <h4 class="text-xs text-gray-500 uppercase tracking-wide">En retard</h4>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $facturesEnRetard }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div>
            <h4 class="text-xs text-gray-500 uppercase tracking-wide">Montant total TTC</h4>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($montantTotalTtc, 2) }} DT</p>
        </div>
    </div>
</div>
@endsection

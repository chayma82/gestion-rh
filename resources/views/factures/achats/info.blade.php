@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Facture {{ $facture->numFacture }}</h1>
            <p class="mt-1 text-gray-500 text-sm">Détail de la facture d'achat.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('factures.achats.edit', $facture->id) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-pen"></i> Modifier
            </a>
            <a href="{{ route('factures.achats.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Informations générales</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <p class="text-xs text-gray-500 mb-1">Fournisseur</p>
                <p class="text-sm font-medium text-gray-900">{{ $facture->fournisseur->nom ?? '—' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-1">Date d'émission</p>
                <p class="text-sm font-medium text-gray-900">{{ $facture->dateEmissionFacture->format('d/m/Y') }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-1">Date échéance</p>
                <p class="text-sm font-medium text-gray-900">{{ $facture->date_echeance ? $facture->date_echeance->format('d/m/Y') : '—' }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-1">Statut</p>
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
                    @default
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-medium">
                            <i class="fa-solid fa-paper-plane"></i> En attente
                        </span>
                @endswitch
            </div>

            <div>
                <p class="text-xs text-gray-500 mb-1">PDF joint</p>
                @if($facture->chemin_pdf)
                    <a href="{{ asset('storage/' . $facture->chemin_pdf) }}" target="_blank"
                        class="inline-flex items-center gap-1 text-sm font-medium text-[#E2721B] hover:underline">
                        <i class="fa-solid fa-file-pdf"></i> {{ $facture->nom_pdf ?? 'Voir le PDF' }}
                    </a>
                @else
                    <p class="text-sm text-gray-400">Aucun fichier joint</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Produits achetés</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Référence</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Désignation</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Quantité</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Prix unitaire</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant ligne</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($facture->details as $detail)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $detail->reference_produit ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $detail->description }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($detail->quantite, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ number_format($detail->prix_unitaire, 2) }} DT</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ number_format($detail->montant_ligne, 2) }} DT</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 flex items-center justify-end">
            <div class="text-right text-sm text-gray-600 space-y-1">
                <p>Montant Hors Taxe (HT) : <span class="font-semibold text-gray-900">{{ number_format($facture->montant_ht, 2) }} DT</span></p>
                <p>Montant TVA : <span class="font-semibold text-gray-900">{{ number_format($facture->montant_tva, 2) }} DT</span></p>
                <p class="text-base">Montant TTC : <span class="font-bold text-[#E2721B]">{{ number_format($facture->montant_ttc, 2) }} DT</span></p>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <form action="{{ route('factures.achats.destroy', $facture->id) }}" method="POST"
                onsubmit="return confirm('Archiver cette facture ? Elle disparaîtra de la liste mais restera consultable en base.');">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white border border-red-200 hover:bg-red-50 text-red-500 text-sm font-medium transition">
                <i class="fa-solid fa-box-archive"></i> Archiver la facture
            </button>
        </form>

        <a href="{{ route('factures.achats.edit', $facture->id) }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
            <i class="fa-solid fa-pen"></i> Modifier la facture
        </a>
    </div>

</div>

@endsection

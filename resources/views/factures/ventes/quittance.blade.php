@extends('layouts.layout')

@section('content')

<style>
    .quittance-card {
        position: relative;
    }

    @media print {
        @page {
            size: A4;
            margin: 15mm;
        }

        body * {
            visibility: hidden;
        }

        .quittance-card, .quittance-card * {
            visibility: visible;
        }

        .quittance-card {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            border-radius: 0 !important;
        }

        .no-print {
            display: none !important;
        }
    }
</style>

<div class="max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-6 no-print">
        <h1 class="text-2xl font-bold text-gray-900">Quittance {{ $paiement->numero_quittance }}</h1>
        <div class="flex items-center gap-3">
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-print"></i> Imprimer
            </button>
            <a href="{{ route('factures.ventes.quittance.pdf', $paiement->id) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                <i class="fa-solid fa-download"></i> Enregistrer
            </a>
            <a href="{{ route('factures.ventes.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                <i class="fa-solid fa-arrow-left"></i> Annuler
            </a>
        </div>
    </div>

    <div class="quittance-card bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Quittance de paiement</h2>
                <p class="text-sm text-gray-500">N° {{ $paiement->numero_quittance }}</p>
            </div>
            <p class="text-sm text-gray-500">{{ $paiement->date_paiement->format('d/m/Y') }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-8 text-sm">
            <div>
                <p class="text-xs text-gray-500 mb-1">Facture</p>
                <p class="font-medium text-gray-900">{{ $facture->numFacture }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Client</p>
                <p class="font-medium text-gray-900">{{ $facture->nom_client }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Méthode de paiement</p>
                <p class="font-medium text-gray-900">{{ ucfirst($paiement->methode_paiement) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Montant reçu (ce paiement)</p>
                <p class="font-bold text-green-600">{{ number_format($paiement->montant, 2) }} DT</p>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4 text-right text-sm text-gray-600 space-y-1">
            <p>Montant total de la facture : <span class="font-semibold text-gray-900">{{ number_format($facture->montant_ttc, 2) }} DT</span></p>
            <p>Total payé à ce jour : <span class="font-semibold text-gray-900">{{ number_format($facture->montant_paye, 2) }} DT</span></p>

            @if($facture->montant_restant > 0 && $facture->date_echeance)
                <p class="text-xs text-gray-500">Échéance du reste : {{ $facture->date_echeance->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

</div>
@endsection

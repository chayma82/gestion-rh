@extends('layouts.layout')

@section('content')

<style>
    .facture-card {
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

        .facture-card, .facture-card * {
            visibility: visible;
        }

        .facture-card {
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

        /* Le tableau doit s'adapter à la largeur de la page imprimée,
           sans min-width ni scrollbar */
        .facture-card .overflow-x-auto {
            overflow: visible !important;
        }

        .facture-card table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed !important;
            font-size: 9px !important;
        }

        .facture-card table th,
        .facture-card table td {
            padding: 4px 3px !important;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
    }
</style>

<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Facture {{ $facture->numFacture }}</h1>
            <p class="mt-1 text-gray-500 text-sm">Détail de la facture de vente.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                <i class="fa-solid fa-print"></i> Imprimer
            </button>

            <a href="{{ route('factures.ventes.facture.pdf', $facture->id) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-download"></i> Enregistrer
            </a>

            <a href="{{ route('factures.ventes.edit', $facture->id) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                <i class="fa-solid fa-pen"></i> Modifier
            </a>

            <a href="{{ route('factures.ventes.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                <i class="fa-solid fa-arrow-left"></i> Annuler
            </a>
        </div>
    </div>

        {{-- Statut --}}
    <div class="mb-6 no-print">
        @if($facture->statut === 'payee')
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                <i class="fa-solid fa-circle-check"></i> Payée
            </span>
        @elseif($facture->statut === 'en_retard')
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-semibold">
                <i class="fa-solid fa-triangle-exclamation"></i> En retard
            </span>
        @elseif($facture->statut === 'archive')
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">
                <i class="fa-solid fa-box-archive"></i> Archivée
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-orange-50 text-[#E2721B] text-xs font-semibold">
                <i class="fa-solid fa-clock"></i> En attente
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- Zone imprimable / téléchargeable : la facture elle-même -->
        <div class="facture-card lg:col-span-2 bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Facture de vente</h2>
                    <p class="text-sm text-gray-500">N° {{ $facture->numFacture }}</p>
                </div>
                <p class="text-sm text-gray-500">{{ $facture->dateEmissionFacture->format('d/m/Y') }}</p>
            </div>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-8">
                <div>
                    <dt class="text-xs text-gray-500 mb-1">Client</dt>
                    <dd class="font-semibold text-gray-900">{{ $facture->nom_client }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500 mb-1">Date d'échéance</dt>
                    <dd class="text-gray-700">{{ $facture->date_echeance?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px]">
                    <colgroup>
                        <col style="width:12%">
                        <col style="width:26%">
                        <col style="width:9%">
                        <col style="width:10%">
                        <col style="width:15%">
                        <col style="width:10%">
                        <col style="width:18%">
                    </colgroup>
                    <thead class="bg-orange-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Référence</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Désignation</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Qté</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Unité</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Prix unitaire</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">TVA (%)</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($facture->details as $ligne)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ligne->reference ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $ligne->designation }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ rtrim(rtrim(number_format($ligne->quantite, 2), '0'), '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $ligne->unite ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ number_format($ligne->prix_unitaire, 2) }} DT</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ rtrim(rtrim(number_format($ligne->taux_tva, 2), '0'), '.') }}%</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ number_format($ligne->montant_ligne, 2) }} DT</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">Aucune ligne enregistrée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 pt-4 mt-4 text-right text-sm text-gray-600 space-y-1">
                <p>Montant Hors Taxe (HT) : <span class="font-semibold text-gray-900">{{ number_format($facture->montant_ht, 2) }} DT</span></p>
                <p>Montant TVA : <span class="font-semibold text-gray-900">{{ number_format($facture->montant_tva, 2) }} DT</span></p>
                <p class="text-base">Montant TTC : <span class="font-bold text-[#E2721B]">{{ number_format($facture->montant_ttc, 2) }} DT</span></p>
            </div>
        </div>

        <!-- Résumé paiement : hors zone imprimable -->
        <div class="no-print bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Paiement</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Montant TTC</span>
                    <span class="font-semibold text-gray-900">{{ number_format($facture->montant_ttc, 2) }} DT</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Payé</span>
                    <span class="font-semibold text-green-600">{{ number_format($facture->montant_paye, 2) }} DT</span>
                </div>
                <div class="flex items-center justify-between border-t border-gray-100 pt-3">
                    <span class="text-gray-500">Reste à payer</span>
                    <span class="font-bold text-[#E2721B]">{{ number_format($facture->montant_restant, 2) }} DT</span>
                </div>
            </div>

            <a href="{{ route('factures.ventes.paiement', $facture->id) }}"
                class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-money-bill-wave"></i>
                {{ $facture->statut === 'payee' ? 'Voir le paiement' : 'Effectuer le paiement' }}
            </a>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-1">Fichier PDF joint (fournisseur/original)</p>
                @if($facture->chemin_pdf)
                    <a href="{{ asset('storage/' . $facture->chemin_pdf) }}" target="_blank" class="text-sm text-[#E2721B] hover:underline">
                        <i class="fa-solid fa-paperclip"></i> {{ $facture->nom_pdf ?? 'voir le fichier' }}
                    </a>
                @else
                    <span class="text-sm text-gray-400">Aucun fichier joint</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Historique des paiements : hors zone imprimable -->
    @if($facture->paiements->isNotEmpty())
        <div class="no-print bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Historique des paiements</h2>
            </div>
            <table class="w-full">
                <tbody class="divide-y divide-gray-100">
                    @foreach($facture->paiements as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $p->date_paiement->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ ucfirst($p->methode_paiement) }}</td>
                            <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ number_format($p->montant, 2) }} DT</td>
                            <td class="px-6 py-3 text-right">
                                <a href="{{ route('factures.ventes.quittance', $p->id) }}" target="_blank"
                                    class="text-xs font-medium text-[#E2721B] hover:underline">
                                    <i class="fa-solid fa-receipt"></i> Quittance
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection

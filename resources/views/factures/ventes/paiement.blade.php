@extends('layouts.layout')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Paiement — {{ $facture->numFacture }}</h1>
        <a href="{{ route('factures.ventes.index') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
            <i class="fa-solid fa-arrow-left"></i> Annuler
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6 mb-6">
        <div class="grid grid-cols-2 gap-4 text-center">
            <div>
                <p class="text-xs text-gray-500 mb-1">Montant TTC</p>
                <p class="text-lg font-bold text-gray-900">{{ number_format($facture->montant_ttc, 2) }} DT</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Statut</p>
                @if($facture->statut === 'payee')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                        <i class="fa-solid fa-circle-check"></i> Payée
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-orange-50 text-[#E2721B] text-xs font-semibold">
                        <i class="fa-solid fa-clock"></i> Non payée
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- ÉTAT 1 : facture déjà payée --}}
    @if($facture->statut === 'payee')

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8 text-center">
            <div class="w-14 h-14 rounded-full bg-green-50 text-green-600 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-circle-check text-2xl"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-900 mb-1">Cette facture est payée</h2>
            <p class="text-sm text-gray-500 mb-6">Le paiement total a déjà été enregistré.</p>

            @php $quittance = $facture->paiements->first(); @endphp

            @if($quittance)
                <a href="{{ route('factures.ventes.quittance', $quittance->id) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                    <i class="fa-solid fa-receipt"></i> Voir / Imprimer la quittance
                </a>
            @endif
        </div>

    {{-- ÉTAT 2 : facture non payée — paiement total, une seule fois --}}
    @else

        <form action="{{ route('factures.ventes.paiement.store', $facture->id) }}" method="POST"
              class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6 space-y-4">
            @csrf

            <div class="px-4 py-3 rounded-lg bg-orange-50 text-sm text-gray-700 border border-orange-100">
                Vous allez enregistrer le paiement <span class="font-bold">total</span> de cette facture, d'un montant de
                <span class="font-bold text-[#E2721B]">{{ number_format($facture->montant_ttc, 2) }} DT</span>.
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Méthode de paiement</label>
                <select name="methode_paiement" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                    <option value="especes">Espèces</option>
                    <option value="cheque">Chèque</option>
                    <option value="virement">Virement</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Date du paiement</label>
                <input type="date" name="date_paiement" required value="{{ old('date_paiement', date('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    onclick="return confirm('Confirmer le paiement total de {{ number_format($facture->montant_ttc, 2) }} DT ?');"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                    <i class="fa-solid fa-check"></i> Payer la facture ({{ number_format($facture->montant_ttc, 2) }} DT)
                </button>
            </div>
        </form>

    @endif

    @if($facture->paiements->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden mt-6">
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

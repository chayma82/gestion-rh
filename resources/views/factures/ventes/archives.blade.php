@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Factures de vente archivées</h1>
            <p class="mt-1 text-gray-500 text-sm">Consulter et restaurer les factures archivées.</p>
        </div>

        <a href="{{ route('factures.ventes.index') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
            <i class="fa-solid fa-arrow-left"></i>
            Retour factures
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto max-h-[650px] overflow-y-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">N° Facture</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date émission</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant TTC</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($factures as $facture)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4"><span class="font-semibold text-gray-900 text-sm">{{ $facture->numFacture }}</span></td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $facture->nom_client }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $facture->dateEmissionFacture->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ number_format($facture->montant_ttc,2) }} DT</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">
                                    <i class="fa-solid fa-box-archive"></i> Archivée
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('factures.ventes.desarchiver',$facture->id) }}" method="POST"
                                    onsubmit="return confirm('Restaurer cette facture ?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 text-xs font-semibold transition">
                                        <i class="fa-solid fa-rotate-left"></i> Restaurer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center">
                                        <i class="fa-solid fa-file-circle-xmark text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium">Aucune facture archivée</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($factures->hasPages())
        <div class="mt-6">{{ $factures->links() }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4 mt-8 w-fit">
        <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
            <i class="fa-solid fa-box-archive"></i>
        </div>
        <div>
            <h4 class="text-xs text-gray-500 uppercase tracking-wide">Factures archivées</h4>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $factures->total() }}</p>
        </div>
    </div>

</div>
@endsection

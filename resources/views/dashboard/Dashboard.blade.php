@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        Tableau de bord exécutif
    </h1>

    {{-- ============================================================ --}}
    {{-- Cartes statistiques RH --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Employés</h4>
                <div class="w-9 h-9 rounded-lg bg-[#9A2A00] text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalEmployes, 0, ',', ' ') }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Contrats Actifs</h4>
                <div class="w-9 h-9 rounded-lg bg-[#E2721B] text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-file-contract text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($contratsActifs, 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400">Actifs ce mois</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Congés en cours</h4>
                <div class="w-9 h-9 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-calendar-xmark text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($congesAujourdhui, 0, ',', ' ') }}</p>
            <p class="text-xs text-gray-400">Employé(s) aujourd'hui</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Masse Salariale (mois)</h4>
                <div class="w-9 h-9 rounded-lg bg-[#9A2A00] text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-file-invoice text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ number_format($masseSalarialeMois, 2) }} DT</p>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- Cartes statistiques FACTURES (achats & ventes) --}}
    {{-- ============================================================ --}}
    <div class="flex items-center justify-between mb-3 mt-2">
        <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Facturation</h2>
        <div class="flex items-center gap-3 text-xs">
            <a href="{{ route('factures.ventes.index') }}" class="text-[#E2721B] hover:underline font-medium">
                Factures ventes <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
            <a href="{{ route('factures.achats.index') }}" class="text-[#E2721B] hover:underline font-medium">
                Factures achats <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Chiffre d'affaires (mois)</h4>
                <div class="w-9 h-9 rounded-lg bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-arrow-trend-up text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($caVentesMois, 2) }} DT</p>
            <p class="text-xs text-gray-400">Ventes émises ce mois-ci</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Achats (mois)</h4>
                <div class="w-9 h-9 rounded-lg bg-orange-100 text-[#E2721B] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-arrow-trend-down text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ number_format($achatsMois, 2) }} DT</p>
            <p class="text-xs text-gray-400">Factures fournisseurs ce mois-ci</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">À recevoir / À payer</h4>
                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-scale-balanced text-sm"></i>
                </div>
            </div>
            <p class="text-lg font-bold text-green-600">+{{ number_format($montantARecevoir, 2) }} DT</p>
            <p class="text-lg font-bold text-red-500">-{{ number_format($montantAPayer, 2) }} DT</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Factures en retard</h4>
                <div class="w-9 h-9 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold {{ $facturesEnRetardTotal > 0 ? 'text-red-500' : 'text-gray-900' }} mb-1">
                {{ number_format($facturesEnRetardTotal, 0, ',', ' ') }}
            </p>
            <p class="text-xs text-gray-400">Achats + ventes confondus</p>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Graphique de croissance --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6">

            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-semibold text-gray-900">
                    Croissance des employés
                </h3>
            </div>

            @php
                $max = collect($croissance)->max('valeur') ?: 1;
            @endphp

            <div class="flex items-end justify-between gap-3 h-64">

                @forelse($croissance as $m)
                    <div class="flex-1 flex flex-col items-center justify-end h-full relative">

                        @if($loop->last)
                            <div class="absolute -top-8 bg-gray-900 text-white text-xs font-semibold px-2 py-1 rounded">
                                {{ number_format($m['valeur'], 0, ',', ' ') }}
                            </div>
                        @endif

                        <div class="w-full rounded-t-md transition-all
                            {{ $loop->last ? 'bg-[#9A2A00]' : 'bg-orange-200' }}"
                            style="height: {{ $m['valeur'] > 0 ? ($m['valeur'] / $max) * 100 : 2 }}%">
                        </div>

                        <span class="text-xs text-gray-400 mt-2">{{ $m['label'] }}</span>

                    </div>
                @empty
                    <p class="text-sm text-gray-400 w-full text-center">Aucune donnée disponible.</p>
                @endforelse

            </div>

        </div>

        {{-- Notifications : mêmes données que le topbar (table notification réelle) --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6">

            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-semibold text-gray-900">
                    Notifications
                </h3>
                <a href="{{ route('notifications.index') }}" class="text-xs text-[#E2721B] hover:underline font-medium">
                    Tout voir
                </a>
            </div>

            <div class="space-y-5">

                @forelse($notifications as $n)
                    <div class="flex items-start gap-3">

                        <div class="w-9 h-9 rounded-full {{ $n->couleur }} flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $n->icon }} text-xs"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $n->titre }}</p>
                            <p class="text-sm text-gray-500 leading-snug">{{ $n->message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $n->date_reception?->diffForHumans() }}</p>
                        </div>

                    </div>
                @empty
                    <p class="text-sm text-gray-400">Aucune notification pour le moment.</p>
                @endforelse

            </div>

        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- Prochaines échéances (achats + ventes) --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Prochaines échéances</h3>
            <span class="text-xs text-gray-400">Factures non payées, triées par urgence</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead class="bg-orange-50">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">N° Facture</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tiers</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Échéance</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($prochainesEcheances as $e)
                        <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ $e['lien'] }}'">
                            <td class="px-6 py-3">
                                @if($e['type'] === 'vente')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 text-green-700 text-xs font-medium">
                                        <i class="fa-solid fa-arrow-trend-up"></i> Vente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-orange-50 text-[#E2721B] text-xs font-medium">
                                        <i class="fa-solid fa-arrow-trend-down"></i> Achat
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ $e['numero'] }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $e['tiers'] }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600">{{ $e['echeance']->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-sm font-semibold text-gray-900">{{ number_format($e['montant'], 2) }} DT</td>
                            <td class="px-6 py-3">
                                @if($e['statut'] === 'en_retard')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-xs font-medium">
                                        <i class="fa-solid fa-triangle-exclamation"></i> En retard
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-medium">
                                        <i class="fa-solid fa-clock"></i> En attente
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center">
                                        <i class="fa-solid fa-circle-check text-xl text-green-400"></i>
                                    </div>
                                    <p class="text-sm font-medium">Aucune échéance en attente — tout est à jour !</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">
        Tableau de bord exécutif
    </h1>

    {{-- Cartes statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Employés</h4>
                <div class="w-9 h-9 rounded-lg bg-[#9A2A00] text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-3">{{ $totalEmployes ?? '1 284' }}</p>
            <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full bg-[#9A2A00] rounded-full" style="width: 78%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Contrats Actifs</h4>
                <div class="w-9 h-9 rounded-lg bg-[#E2721B] text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-file-contract text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $contratsActifs ?? '942' }}</p>
            <p class="text-xs text-gray-400 mb-2">Actifs ce mois</p>
            <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full bg-[#E2721B] rounded-full" style="width: 62%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Congés en Attente</h4>
                <div class="w-9 h-9 rounded-lg bg-red-100 text-red-500 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-calendar-xmark text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-1">{{ $congesEnAttente ?? '18' }}</p>
            <p class="text-xs text-red-500 font-medium mb-2">Action requise</p>
            <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full bg-red-400 rounded-full" style="width: 20%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Facturation Totale (MTD)</h4>
                <div class="w-9 h-9 rounded-lg bg-[#9A2A00] text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-file-invoice text-sm"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mb-3">{{ $facturationTotale ?? '4.2M€' }}</p>
            <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-full bg-[#9A2A00] rounded-full" style="width: 85%"></div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Graphique de croissance --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6">

            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-semibold text-gray-900">
                    Croissance des employés
                </h3>

                <div class="relative">
                    <select class="appearance-none border border-gray-300 rounded-lg pl-4 pr-9 py-2 text-sm text-gray-600 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option>Derniers 12 mois</option>
                        <option>Derniers 6 mois</option>
                        <option>Cette année</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            @php
                $mois = $croissance ?? [
                    ['label' => 'Jan', 'valeur' => 980],
                    ['label' => 'Fév', 'valeur' => 1005],
                    ['label' => 'Mar', 'valeur' => 995],
                    ['label' => 'Avr', 'valeur' => 1040],
                    ['label' => 'Mai', 'valeur' => 1060],
                    ['label' => 'Juin', 'valeur' => 1110],
                    ['label' => 'Juil', 'valeur' => 1140],
                    ['label' => 'Août', 'valeur' => 1180],
                    ['label' => 'Sep', 'valeur' => 1220],
                    ['label' => 'Oct', 'valeur' => 1284],
                ];
                $max = collect($mois)->max('valeur');
            @endphp

            <div class="flex items-end justify-between gap-3 h-64">

                @foreach($mois as $i => $m)
                    <div class="flex-1 flex flex-col items-center justify-end h-full relative">

                        @if($loop->last)
                            <div class="absolute -top-8 bg-gray-900 text-white text-xs font-semibold px-2 py-1 rounded">
                                {{ number_format($m['valeur'], 0, ',', ' ') }}
                            </div>
                        @endif

                        <div class="w-full rounded-t-md transition-all
                            {{ $loop->last ? 'bg-[#9A2A00]' : 'bg-orange-200' }}"
                            style="height: {{ ($m['valeur'] / $max) * 100 }}%">
                        </div>

                        <span class="text-xs text-gray-400 mt-2">{{ $m['label'] }}</span>

                    </div>
                @endforeach

            </div>

        </div>

        {{-- Notifications --}}
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6">

            <h3 class="text-lg font-semibold text-gray-900 mb-5">
                Notifications
            </h3>

            <div class="space-y-5">

                @php
                    $notifications = $notifications ?? [
                        ['icon' => 'fa-sack-dollar', 'color' => 'bg-green-50 text-green-600', 'titre' => 'Paiement Reçu', 'texte' => "La facture #INV-2024-001 doit être réglée dans 6 jours", 'temps' => 'Il y a 2 heures'],
                        ['icon' => 'fa-user-plus', 'color' => 'bg-orange-50 text-[#E2721B]', 'titre' => 'Nouvel Employé', 'texte' => "Jean Dupont a rejoint l'équipe Marketing.", 'temps' => 'Il y a 5 heures'],
                        ['icon' => 'fa-circle-exclamation', 'color' => 'bg-red-50 text-red-500', 'titre' => 'Retard de Paiement', 'texte' => "La facture #INV-2023-098 est en retard de 5 jours.", 'temps' => 'Hier'],
                        ['icon' => 'fa-file-signature', 'color' => 'bg-blue-50 text-blue-600', 'titre' => 'Document Signé', 'texte' => "Le contrat de Marie Curie a été validé.", 'temps' => 'Il y a 2 jours'],
                    ];
                @endphp

                @foreach($notifications as $n)
                    <div class="flex items-start gap-3">

                        <div class="w-9 h-9 rounded-full {{ $n['color'] }} flex items-center justify-center shrink-0">
                            <i class="fa-solid {{ $n['icon'] }} text-xs"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $n['titre'] }}</p>
                            <p class="text-sm text-gray-500 leading-snug">{{ $n['texte'] }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $n['temps'] }}</p>
                        </div>

                    </div>
                @endforeach

            </div>

        </div>

    </div>

</div>

@endsection

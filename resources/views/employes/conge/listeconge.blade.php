@extends('layouts.layout')

@section('content')

<div class="max-w-4xl mx-auto">

    <x-Employeinfo_conge :employee="$employee ?? null" active="conges">
        <x-slot:actions>
            <a href="{{ route('employes.index') }}"
            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
            Annuler
        </a>
        </x-slot:actions>
    </x-Employeinfo_conge>

    <x-card>

        <h3 class="text-sm font-semibold text-[#9A2A00] mb-5">
            Historique des demandes
        </h3>

        <table class="w-full">

            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type de congé</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Période</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Durée</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @php
                    $conges = $conges ?? [
                        ['type' => 'Congés Payés (CP)', 'note' => "Congés d'été", 'debut' => '15/07/2024', 'fin' => '19/07/2024', 'duree' => '5 jours'],
                        ['type' => 'RTT', 'note' => 'Pont Ascension', 'debut' => '10/05/2024', 'fin' => '10/05/2024', 'duree' => '1 jour'],
                        ['type' => 'Sans solde', 'note' => 'Convenance personnelle', 'debut' => '01/04/2024', 'fin' => '03/04/2024', 'duree' => '3 jours'],
                        ['type' => 'Maladie', 'note' => 'Certificat transmis', 'debut' => '12/02/2024', 'fin' => '14/02/2024', 'duree' => '3 jours'],
                    ];
                @endphp

                @foreach($conges as $conge)
                    <tr>
                        <td class="py-4">
                            <div class="flex items-start gap-2">
                                    <span class="w-2 h-2 rounded-full mt-1.5 shrink-0"></span>                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $conge['type'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $conge['note'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 text-sm text-gray-600">
                            {{ $conge['debut'] }} au<br>{{ $conge['fin'] }}
                        </td>
                        <td class="py-4 text-sm text-gray-800 font-medium">
                            {{ $conge['duree'] }}
                        </td>
                    </tr>
                @endforeach

            </tbody>

        </table>

    </x-card>

</div>

@endsection

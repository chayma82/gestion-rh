@extends('layouts.layout')

@section('content')

<div class="max-w-4xl mx-auto">

    <x-Employeinfo_conge :employe="$employe" active="contrats">
        <x-slot:actions>
             <x-annuler />
        </x-slot:actions>
    </x-Employeinfo_conge>

    <x-card>

        <h3 class="text-sm font-semibold text-[#E2721B] mb-5">
            Historique des contrats
        </h3>

        <table class="w-full">

            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">N° contrat</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Poste / Département</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Période</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Salaire de base</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($contrats as $contrat)
                    <tr>
                        <td class="py-4 text-sm text-gray-800 font-medium">
                            {{ $contrat->numcontrat ?? '-' }}
                        </td>
                        <td class="py-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $contrat->poste?->libelle ?? '—' }}</p>
                                <p class="text-xs text-gray-400">{{ $contrat->departement?->libelle ?? '—' }}</p>
                            </div>
                        </td>
                        <td class="py-4 text-sm text-gray-600">
                            {{ $contrat->typeContrat ?? '-' }}
                        </td>
                        <td class="py-4 text-sm text-gray-600">
                            {{ $contrat->date_debut?->format('d/m/Y') }}
                            @if($contrat->date_fin)
                                au<br>{{ $contrat->date_fin->format('d/m/Y') }}
                            @else
                                <br><span class="text-xs text-gray-400">en cours</span>
                            @endif
                        </td>
                        <td class="py-4 text-sm text-gray-800 font-medium">
                            {{ number_format($contrat->salaire_base, 2) }} DT
                        </td>
                        <td class="py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $contrat->statut_badge['classes'] }}">
                                {{ $contrat->statut_badge['label'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-sm text-gray-400">
                            Aucun contrat enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </x-card>

</div>

@endsection

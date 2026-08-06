@extends('layouts.layout')

@section('content')

<div class="max-w-4xl mx-auto">

    <x-Employeinfo_conge :employe="$employe" active="conges">
        <x-slot:actions>
             <x-annuler />
        </x-slot:actions>
    </x-Employeinfo_conge>

    <x-card>

        <h3 class="text-sm font-semibold text-[#E2721B] mb-5">
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
                @forelse($conges as $conge)
                    <tr>
                        <td class="py-4">
                            <div class="flex items-start gap-2">
                                <span class="w-2 h-2 rounded-full mt-1.5 shrink-0"></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $conge->type_conge }}</p>
                                    <p class="text-xs text-gray-400">{{ $conge->note ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 text-sm text-gray-600">
                            {{ $conge->date_debut->format('d/m/Y') }} au<br>{{ $conge->date_fin->format('d/m/Y') }}
                        </td>
                        <td class="py-4 text-sm text-gray-800 font-medium">
                            {{ $conge->date_debut->diffInDays($conge->date_fin) + 1 }} jour(s)
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-6 text-center text-sm text-gray-400">
                            Aucune demande de congé enregistrée.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </x-card>

</div>

@endsection

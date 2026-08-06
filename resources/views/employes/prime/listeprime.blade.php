@extends('layouts.layout')

@section('content')

<div class="max-w-4xl mx-auto">

    <x-Employeinfo_conge :employe="$employe" active="primes">
        <x-slot:actions>
             <x-annuler />
        </x-slot:actions>
    </x-Employeinfo_conge>

    <x-card>

        <h3 class="text-sm font-semibold text-[#E2721B] mb-5">
            Historique des primes
        </h3>

        <table class="w-full">

            <thead>
                <tr class="border-b border-gray-100">

                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                    <th class="text-left pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Motif</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($primes as $prime)
                    <tr>
                        <td class="py-4 text-sm text-gray-800 font-medium">
                            {{ number_format($prime->montant, 2) }} DT
                        </td>
                        <td class="py-4 text-sm text-gray-600">
                            {{ $prime->date_creation?->format('d/m/Y') }}
                        </td>
                        <td class="py-4">
                            <div class="flex items-start gap-2">
                                <span class="w-2 h-2 rounded-full mt-1.5 shrink-0"></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $prime->type ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $prime->note ?? '' }}</p>
                                </div>
                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-6 text-center text-sm text-gray-400">
                            Aucune prime enregistrée.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </x-card>

</div>

@endsection

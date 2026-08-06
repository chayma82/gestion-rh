@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Employés archivés
            </h1>
            <x-annuler />
        </div>

        <a href="{{ route('employes.index') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
            <i class="fa-solid fa-arrow-left"></i>
            Annuler
        </a>

    </div>



    <!-- Tableau -->
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

        <div class="overflow-x-auto max-h-[650px] overflow-y-auto">
            <table class="w-full min-w-[700px]">

                <thead class="bg-orange-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Matricule</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nom</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($employes as $employe)

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $employe->matricule }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900 text-sm">{{ $employe->nom_complet }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <form action="{{ route('employes.desarchiver', $employe->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Désarchiver cet employé ?');">
                                @csrf
                                @method('PUT')

                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-50 text-green-700 hover:bg-green-100 text-xs font-semibold transition">
                                    <i class="fa-solid fa-rotate-left"></i>
                                    Désarchiver
                                </button>
                            </form>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center">
                                    <i class="fa-solid fa-box-open text-xl"></i>
                                </div>
                                <p class="text-sm font-medium">Aucun employé archivé</p>
                            </div>
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>
        </div>
    </div>

    @if($employes->hasPages())
        <div class="mt-6">
            {{ $employes->links() }}
        </div>
    @endif

    <!-- Compteur -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-8"></div>
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-5 flex items-center gap-4 mb-6 w-fit">
        <div class="w-11 h-11 rounded-xl bg-orange-50 text-[#E2721B] flex items-center justify-center">
                <i class="fa-solid fa-box-archive"></i>
        </div>
        <div>
            <h4 class="text-xs text-gray-500 uppercase tracking-wide">Employés archivés</h4>
            <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $employes->total() }}</p>
        </div>
    </div>

</div>
</div>

@endsection




@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Départements</h1>
            <p class="mt-1 text-gray-500 text-sm">
                Gérer les départements de l'entreprise.
            </p>
        </div>

        <a href="{{ route('departements.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
            <i class="fa-solid fa-plus"></i>
            Ajouter un département
        </a>

    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Recherche -->
    <form action="{{ route('departements.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-6">

        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher par code ou libellé..."
                class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
        </div>

        <button type="submit"
            class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 px-5 rounded-lg text-sm font-medium text-gray-700 transition">
            <i class="fa-solid fa-sliders"></i>
            Filtrer
        </button>

    </form>

    <!-- Tableau -->
    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

        <table class="w-full">

            <thead class="bg-orange-50">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Code</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Libellé</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Postes</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Contrats</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse($departements as $departement)

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-[#E2721B]">
                                {{ $departement->code }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $departement->libelle }}</td>

                        <td class="px-6 py-4 text-sm text-gray-600">{{ $departement->postes_count }}</td>

                        <td class="px-6 py-4 text-sm text-gray-600">{{ $departement->contrats_count }}</td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">

                                <a href="{{ route('departements.edit', $departement->id) }}"
                                    class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center"
                                    title="Modifier">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </a>

                                <form action="{{ route('departements.destroy', $departement->id) }}" method="POST"
                                    onsubmit="return confirm('Supprimer définitivement ce département ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center"
                                        title="Supprimer">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>

                            </div>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Aucun département trouvé.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <!-- Pagination -->
    @if($departements instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="flex justify-between items-center mt-5">
            <span class="text-sm text-gray-500">
                Affichage {{ $departements->firstItem() ?? 0 }} - {{ $departements->lastItem() ?? 0 }} sur {{ $departements->total() }} départements
            </span>
            <div class="flex items-center gap-2">
                {{ $departements->onEachSide(1)->links() }}
            </div>
        </div>
    @endif

</div>

@endsection

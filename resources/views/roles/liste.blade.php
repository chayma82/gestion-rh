@extends('layouts.layout')

@section('content')

<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rôles</h1>
            <p class="mt-1 text-gray-500 text-sm">Définir les rôles disponibles pour les utilisateurs de votre entreprise.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('utilisateur.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                <i class="fa-solid fa-users"></i>
                Utilisateurs
            </a>

            <a href="{{ route('roles.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-plus"></i>
                Ajouter un rôle
            </a>
        </div>
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

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

        <table class="w-full">
            <thead class="bg-orange-50">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Nom</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Utilisateurs</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">

                @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $role->nom }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $role->utilisateurs_count }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('roles.edit', $role->id) }}"
                                    class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center" title="Modifier">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </a>

                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                    onsubmit="return confirm('Supprimer ce rôle ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center" title="Supprimer">
                                        <i class="fa-regular fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 text-sm">Aucun rôle trouvé.</td>
                    </tr>
                @endforelse

            </tbody>
        </table>

    </div>

</div>

@endsection

@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">Utilisateurs</h1>
            <p class="mt-1 text-gray-500 text-sm">
                Gérer les comptes ayant accès au site et leurs rôles.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('roles.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                <i class="fa-solid fa-shield-halved"></i>
                Gérer les rôles
            </a>

            <a href="{{ route('utilisateurs.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-user-plus"></i>
                Ajouter un utilisateur
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

    <!-- Recherche & filtres -->
    <form action="{{ route('utilisateur.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-6">

        <div class="relative flex-1">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher par nom ou e-mail..."
                class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
        </div>

        <div class="relative">
            <select name="role_id"
                class="appearance-none bg-white border border-gray-300 hover:bg-gray-50 rounded-lg pl-4 pr-10 py-3 text-sm font-medium text-gray-700 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
                <option value="">Tous les rôles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ $role->nom }}</option>
                @endforeach
            </select>
            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
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
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Nom</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">E-mail</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Rôle</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Statut</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                @forelse($utilisateurs as $utilisateur)

                    <tr class="hover:bg-gray-50 transition">

                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $utilisateur->nom }}</div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-600">{{ $utilisateur->email }}</td>

                        <td class="px-6 py-4 text-sm">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-[#E2721B]">
                                {{ $utilisateur->role->nom ?? '—' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-sm">
                            @if($utilisateur->actif)
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">ACTIF</span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">DÉSACTIVÉ</span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">

                                <a href="{{ route('utilisateurs.edit', $utilisateur->id) }}"
                                    class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center"
                                    title="Modifier">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </a>

                                <form action="{{ route('utilisateurs.toggle-actif', $utilisateur->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-full flex items-center justify-center {{ $utilisateur->actif ? 'bg-yellow-50 text-yellow-600' : 'bg-green-50 text-green-600' }}"
                                        title="{{ $utilisateur->actif ? 'Désactiver l\'accès' : 'Réactiver l\'accès' }}">
                                        <i class="fa-solid {{ $utilisateur->actif ? 'fa-lock' : 'fa-lock-open' }} text-xs"></i>
                                    </button>
                                </form>

                                <form action="{{ route('utilisateurs.destroy', $utilisateur->id) }}" method="POST"
                                    onsubmit="return confirm('Supprimer définitivement cet utilisateur ?');">
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
                            Aucun utilisateur trouvé.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <!-- Pagination -->
    @if($utilisateurs instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="flex justify-between items-center mt-5">
            <span class="text-sm text-gray-500">
                Affichage {{ $utilisateurs->firstItem() ?? 0 }} - {{ $utilisateurs->lastItem() ?? 0 }} sur {{ $utilisateurs->total() }} utilisateurs
            </span>
            <div class="flex items-center gap-2">
                {{ $utilisateurs->onEachSide(1)->links() }}
            </div>
        </div>
    @endif

</div>

@endsection

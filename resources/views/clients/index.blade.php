@extends('layouts.layout')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Clients</h1>
            <p class="mt-1 text-gray-500 text-sm">Gérer la liste de vos clients.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('clients.archives') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 hover:border-gray-400 text-sm font-medium transition">
                <i class="fa-solid fa-box-archive text-gray-400"></i>
                Archives
            </a>
            <a href="{{ route('clients.create') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-user-plus"></i> Nouveau client
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('clients.index') }}" method="GET" id="formRechercheClients" class="mb-6">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input
                id="searchClient"
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher un client..."
                class="w-full border border-gray-300 rounded-lg pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
        </div>
    </form>

    <script>
        let timerClient;
        document.getElementById('searchClient').addEventListener('input', function () {
            clearTimeout(timerClient);
            let recherche = this.value;
            timerClient = setTimeout(function () {
                fetch("{{ route('clients.index') }}?q=" + encodeURIComponent(recherche))
                    .then(response => response.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        document.getElementById('tableClients').innerHTML =
                            doc.getElementById('tableClients').innerHTML;
                        document.getElementById('paginationClients').innerHTML =
                            doc.getElementById('paginationClients')?.innerHTML ?? '';
                    });
            }, 300);
        });
    </script>

   <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] overflow-hidden">

    <div class="overflow-x-auto max-h-[650px] overflow-y-auto" id="tableClients">
        <table class="w-full min-w-[800px]">
            <thead class="bg-orange-50">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nom</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Téléphone</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">MF</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($clients as $client)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $client->nom }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->email ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->telephone ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $client->matricule_fiscal ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('clients.edit', $client->id) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-orange-50 text-[#E2721B] hover:bg-orange-100 transition">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </a>
                                <!-- Archiver au lieu de supprimer -->
                                <a href="{{ route('clients.archiver', $client->id) }}"
                                    onclick="return confirm('Archiver ce client ?');"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition"
                                    title="Archiver">
                                    <i class="fa-solid fa-box-archive text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">Aucun client trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>

    <div id="paginationClients">
        @if($clients->hasPages())
            <div class="mt-6">{{ $clients->links() }}</div>
        @endif
    </div>

</div>
@endsection

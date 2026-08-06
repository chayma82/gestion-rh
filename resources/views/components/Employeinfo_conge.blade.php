@props([
    'employe',
    'active' => 'informations'
])

<div class="flex items-start justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            {{ $employe->nom ?? '-' }} {{ $employe->prenom ?? '-' }}
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            {{ $employe->poste?->libelle ?? '-' }} • {{ $employe->departement->libelle ?? '-' }}
        </p>
    </div>

    <div class="flex items-center gap-3 shrink-0">

    <a href="{{ route('employes.edit', $employe->id) }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
              bg-[#E2721B] hover:bg-[#D16212]
              text-white text-sm font-medium transition">

        <i class="fa-solid fa-user-pen"></i>

        Modifier profil
    </a>


    <x-annuler />

</div>

</div>

<div class="flex gap-6 border-b border-gray-200 mb-8">

    <a href="{{ route('employes.info',$employe->id) }}"
        class="pb-3 text-sm font-medium border-b-2 transition
        {{ $active === 'informations' ? 'border-[#E2721B] text-[#E2721B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Informations
    </a>

    <a href="{{ route('employe.conge.index', $employe->id) }}"
        class="pb-3 text-sm font-medium border-b-2 transition
        {{ $active === 'conges' ? 'border-[#E2721B] text-[#E2721B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Liste des Congés
    </a>

    <a href="{{ route('employe.avance.index', $employe->id) }}"
        class="pb-3 text-sm font-medium border-b-2 transition
        {{ $active === 'avances' ? 'border-[#E2721B] text-[#E2721B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Liste des Avances
    </a>

    <a href="{{ route('employe.prime.index', $employe->id) }}"
        class="pb-3 text-sm font-medium border-b-2 transition
        {{ $active === 'primes' ? 'border-[#E2721B] text-[#E2721B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Liste des Primes
    </a>

</div>

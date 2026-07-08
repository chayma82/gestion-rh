@props([
    'employee',
    'active' => 'informations'
])

<div class="flex items-start justify-between mb-6">

    <div>
        <p class="text-xs text-gray-400 mb-2">
            <a href="{{ route('employes.index') }}" class="hover:underline">Employés</a>
            / Fiche Employé
        </p>

        <h1 class="text-2xl font-bold text-gray-900">
            {{ $employee->nom_complet ?? 'Jean-Marc Bernard' }}
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            {{ $employee->poste_occupe ?? 'Senior Project Manager' }} • {{ $employee->departement ?? 'Direction Technique' }}
        </p>
    </div>

    <div class="flex items-center gap-3 shrink-0">
        {{ $actions ?? '' }}
    </div>

</div>

<div class="flex gap-6 border-b border-gray-200 mb-8">

    <a href="{{ route('employes.info') }}"
        class="pb-3 text-sm font-medium border-b-2 transition
        {{ $active === 'informations' ? 'border-[#E2721B] text-[#E2721B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Informations
    </a>

    <a href="{{ route('employes.conge.index') }}"
        class="pb-3 text-sm font-medium border-b-2 transition
        {{ $active === 'conges' ? 'border-[#E2721B] text-[#E2721B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Liste des Congés
    </a>

</div>

@extends('layouts.layout')

@section('content')

<div class="max-w-4xl mx-auto">


        <div class="flex items-center justify-between mb-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Information sur le contrat
        </h1>

        <a href="{{ route('employes.contrats.index') }}"
            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
            Annuler
        </a>

    </div>


    {{-- Détails Personnels --}}
    <x-card>
        <x-section-title number="" title="Détails Personnels" icon="fa-user"/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
            <x-detail label="Matricule">{{ $employee->matricule ?? '123456789' }}</x-detail>
            <x-detail label="Date de Naissance">{{ $employee->date_naissance ?? '14 Mai 1985 (40 ans)' }}</x-detail>

            <x-detail label="Nom Complet">{{ $employee->nom_complet ?? 'Jean-Marc Bernard' }}</x-detail>
            <x-detail label="Lieu de Naissance">{{ $employee->lieu_naissance ?? 'Lyon, France' }}</x-detail>

            <x-detail label="Genre">{{ $employee->sexe ?? 'Masculin' }}</x-detail>
            <x-detail label="CIN / Passeport">{{ $employee->cin_passeport ?? '00000000' }}</x-detail>

            <x-detail label="Nationalité">{{ $employee->nationalite ?? 'Française' }}</x-detail>
            <x-detail label="Nombre d'enfants">{{ $employee->nb_enfants ?? '—' }}</x-detail>

            <x-detail label="Situation Familiale">{{ $employee->situation_familiale ?? '—' }}</x-detail>
        </div>
    </x-card>




    {{-- Informations Professionnelles --}}
    <x-card>
        <x-section-title number="" title="Informations Professionnelles" icon="fa-briefcase"/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

            <x-detail label="Département">{{ $employee->departement ?? 'Direction Technique / IT' }}</x-detail>
            <x-detail label="Intitulé du Poste">{{ $employee->poste_occupe ?? 'Senior Project Manager' }}</x-detail>

            <div>
                <p class="text-xs text-gray-400 mb-1">Statut Employé</p>
                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                    {{ strtoupper($employee->statut_employe ?? 'ACTIF') }}
                </span>
            </div>

            <x-detail label="Date d'embauche">{{ $employee->date_embauche ?? '01 Mars 2018' }}</x-detail>

            <x-detail label="Date de début de fonction">{{ $employee->date_prisePoste ?? '15 Mars 2018' }}</x-detail>

            <div>
                <p class="text-xs text-gray-400 mb-1">Congés Cumulés</p>
                <p class="text-sm font-medium text-gray-800">
                    {{ $employee->conges_cumules ?? '24.5 jours' }}
                    <span class="text-xs text-gray-400 font-normal">(Période en cours)</span>
                </p>
            </div>

        </div>
    </x-card>




    {{-- Informations Contractuelles --}}
    <x-card>
        <x-section-title number="" title="Informations Contractuelles" icon="fa-file-contract"/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
            <x-detail label="Type de contrat">{{ $employee->type_contrat_final ?? 'CDI' }}</x-detail>
            <x-detail label="Numéro de contrat">{{ $employee->numero_contrat ?? 'CONT-2018-0452' }}</x-detail>

            <x-detail label="Date de début">{{ $employee->date_debut ?? '01 Mars 2018' }}</x-detail>
            <x-detail label="Date de fin">{{ $employee->date_fin ?? 'Indéterminée' }}</x-detail>

            <x-detail label="Salaire de base">{{ $employee->salaire ?? '65,000 €' }} / an</x-detail>
            <x-detail label="Ancienneté">{{ $employee->anciennete ?? '5 ans et 10 mois' }}</x-detail>

            <x-detail label="Chargé du Recrutement">{{ $employee->NomRecruteur ?? 'Marc Antoine' }}</x-detail>
        </div>
    </x-card>

</div>

@endsection

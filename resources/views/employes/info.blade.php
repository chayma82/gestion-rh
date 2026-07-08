@extends('layouts.layout')

@section('content')

<div class="max-w-4xl mx-auto">

    <x-Employeinfo_conge :employee="$employee ?? null" active="informations">
        <x-slot:actions>
            <a href="{{ route('employes.index') }}"
            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
            Annuler
            </a>
            <a href="{{ route('employes.create') }}"
                class="px-5 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white text-sm font-medium shadow-md shadow-orange-600/10 transition">
                Modifier Profil
            </a>
        </x-slot:actions>
    </x-Employeinfo_conge>

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

    {{-- Coordonnées --}}
    <x-card>
        <x-section-title number="" title="Coordonnées" icon="fa-address-card"/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

            <div>
                <p class="text-xs text-gray-400 mb-1">Téléphone Professionnel</p>
                <p class="text-sm font-medium text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-phone text-gray-400 text-xs"></i>
                    {{ $employee->tel_pro ?? '+33 1 23 45 67 89' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400 mb-1">E-mail Professionnel</p>
                <p class="text-sm font-medium text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-gray-400 text-xs"></i>
                    {{ $employee->email_pro ?? 'j.bernard@lumina-hrms.com' }}
                </p>
            </div>

            <x-detail label="Téléphone Personnel">{{ $employee->tel_perso ?? '+33 6 12 34 56 78' }}</x-detail>

            <div>
                <p class="text-xs text-gray-400 mb-1">E-mail Personnel</p>
                <p class="text-sm font-medium text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-gray-400 text-xs"></i>
                    {{ $employee->email_perso ?? 'jm.bernard.private@email.com' }}
                </p>
            </div>

            <div class="md:col-span-2">
                <p class="text-xs text-gray-400 mb-1">Adresse Domicile</p>
                <p class="text-sm font-medium text-gray-800">
                    {{ $employee->adresse ?? '12 Avenue des Champs-Élysées,' }}<br>
                    {{ $employee->code_postal ?? '75008' }} {{ $employee->ville ?? 'Paris, France' }}
                </p>
            </div>

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

    {{-- Contact d'Urgence --}}
    <x-card accent="true">
        <div class="flex items-center gap-2.5 mb-6">
            <i class="fa-solid fa-triangle-exclamation text-[#E2721B] text-sm"></i>
            <h3 class="text-sm font-bold text-[#E2721B] uppercase tracking-wide">
                Contact d'Urgence
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
            <x-detail label="Nom du Contact">{{ $employee->nom_urgence ?? 'Marie-Claire Bernard' }}</x-detail>
            <x-detail label="Relation">{{ $employee->lien_parente ?? 'Épouse' }}</x-detail>

            <div>
                <p class="text-xs text-gray-400 mb-1">Téléphone</p>
                <p class="text-sm font-semibold ">{{ $employee->tel_urgence ?? '+33 6 88 77 66 55' }}</p>
            </div>

            <x-detail label="Adresse">{{ $employee->adresse_urgence ?? 'Idem domicile principal' }}</x-detail>
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

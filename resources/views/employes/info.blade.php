@extends('layouts.layout')

@section('content')


    <x-Employeinfo_conge :employe="$employe ?? null" active="informations">
        <x-slot:actions>
            <x-annuler />
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
            <x-detail label="Matricule">{{ $employe->matricule ?? '_' }}</x-detail>
            <x-detail label="Date de Naissance">{{ $employe->date_naissance?->format('Y-m-d') ?? '_' }}</x-detail>

            <x-detail label="Nom Complet">{{ $employe->nom ?? '_' }} {{ $employe->prenom ?? '' }}</x-detail>
            <x-detail label="Lieu de Naissance">{{ $employe->lieu_naissance ?? '_' }}</x-detail>

            {{-- Correction : $employee -> $employe (faute de frappe qui
                 rendait le genre et la nationalité toujours vides) --}}
            <x-detail label="Genre">
                {{ $employe->sexe == 'M' ? 'Homme' : ($employe->sexe == 'F' ? 'Femme' : '_') }}
            </x-detail>            <x-detail label="CIN / Passeport">{{ $employe->cin_passeport ?? '_' }}</x-detail>

            <x-detail label="Nationalité">{{ $employe->nationalite ?? '_' }}</x-detail>

            <x-detail label="Situation Familiale">{{ $employe->situation_familiale ?? '—' }}</x-detail>
            <x-detail label="Nombre d'enfants">{{ $employe->nb_enfants ?? '—' }}</x-detail>

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
                    {{ $employe->tel_pro ?? '_' }}
                </p>
            </div>

            <div>
                <p class="text-xs text-gray-400 mb-1">E-mail Professionnel</p>
                <p class="text-sm font-medium text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-gray-400 text-xs"></i>
                    {{ $employe->email_pro ?? '_' }}
                </p>
            </div>

            <x-detail label="Téléphone Personnel">{{ $employe->tel_perso ?? '_' }}</x-detail>

            <div>
                <p class="text-xs text-gray-400 mb-1">E-mail Personnel</p>
                <p class="text-sm font-medium text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-gray-400 text-xs"></i>
                    {{ $employe->email_perso ?? '_' }}
                </p>
            </div>

            <div class="md:col-span-2">
                <p class="text-xs text-gray-400 mb-1">Adresse Domicile</p>
                <p class="text-sm font-medium text-gray-800">
                    {{ $employe->adresse ?? '12 Avenue des Champs-Élysées,' }}<br>
                    {{ $employe->code_postal ?? '_' }} {{ $employe->ville ?? '_' }}
                </p>
            </div>

        </div>
    </x-card>

    {{-- Informations Professionnelles --}}
    <x-card>
        <x-section-title number="" title="Informations Professionnelles" icon="fa-briefcase"/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

<x-detail label="Département">{{ $employe->departement?->libelle ?? '_' }}</x-detail>
            {{-- Correction : le champ fillable s'appelle posteOccupe (camelCase),
                 pas poste_occupe --}}
<x-detail label="Intitulé du Poste">{{ $employe->poste?->libelle ?? '_' }}</x-detail>
            <div>
                <p class="text-xs text-gray-400 mb-1">Statut Employé</p>
                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                    {{ strtoupper($employe->statutEmploye ?? '_') }}
                </span>
            </div>

            <x-detail label="Date d'embauche">{{ $employe->date_embauche?->format('Y-m-d') ?? 'Indéterminée' }}</x-detail>


            {{-- Correction : conges_cumules n'existait pas comme accessor.
                 Utilisation de l'accessor getCongesCumulesAttribute() ajouté
                 au modèle Employe, plus affichage du solde restant --}}
            <div>
                <p class="text-xs text-gray-400 mb-1">Congés Cumulés</p>
                <p class="text-sm font-medium text-gray-800">
                    {{ $employe->conges_cumules ?? 0 }} jours pris
                    <span class="text-xs text-gray-400 font-normal">
                        / {{ $employe->nbreJourCongeAqcuise ?? 0 }} acquis
                        (solde : {{ $employe->solde_conge ?? 0 }})
                    </span>
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
            <x-detail label="Nom du Contact">{{ $employe->nom_contact_urgence ?? '_' }}</x-detail>
            <x-detail label="Relation">{{ $employe->lien_parente ?? '_' }}</x-detail>

            <div>
                <p class="text-xs text-gray-400 mb-1">Téléphone</p>
                <p class="text-sm font-semibold ">{{ $employe->telephone_urgence ?? '_' }}</p>
            </div>

            <x-detail label="Adresse">{{ $employe->adresse_urgence ?? '_' }}</x-detail>
        </div>
    </x-card>


@php
    // On récupère le contrat "en cours de vie" (actif OU à venir) et non plus
    // uniquement le contrat actif : sinon un contrat signé mais pas encore
    // démarré n'apparaissait jamais sur la fiche employé.
    $contrat = $employe->contratEnCours;
@endphp

    {{-- Informations Contractuelles --}}
    <x-card>
        <x-section-title number="" title="Informations Contractuelles" icon="fa-file-contract"/>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
            <x-detail label="Type de contrat">{{ $contrat->typeContrat ?? 'Pas de contrat' }}</x-detail>
            <x-detail label="Numéro de contrat">{{ $contrat->numcontrat ?? 'Pas de contrat' }}</x-detail>

            <x-detail label="Date de début">{{ $contrat?->date_debut?->format('Y-m-d') ?? 'Indéterminée' }}</x-detail>
<x-detail label="Date de fin">{{ $contrat?->date_fin?->format('Y-m-d') ?? 'Indéterminée' }}</x-detail>
            {{-- Correction : accessor salaire_net (voir Employe.php),
                 corrigé pour trier par date_creation --}}
            <x-detail label="Salaire de base">{{ $employe->salaire_net ?? 0 }} DT / mois</x-detail>

            <x-detail label="Chargé du Recrutement">{{ $contrat->recreteur ?? '_' }}</x-detail>
        </div>
    </x-card>

@endsection

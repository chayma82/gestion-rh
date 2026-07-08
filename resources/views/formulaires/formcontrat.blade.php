@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Informations du contrat
        </h1>

        <a href="{{ route('employes.contrats.index') }}"
            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
            Annuler
        </a>

    </div>

    <form action="{{ route('employes.contrats.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        {{-- =========================
            1. Informations employé
        ========================= --}}
        <x-card>

            <x-section-title
                number="1"
                title="Informations de l'employé"
                icon="fa-user"/>

            <div class="grid grid-cols-1 mb-6">

                <x-select
                    name="employe"
                    label="Employé"
                    :options="[
                        'EMP001 - Ahmed Ben Ali',
                        'EMP002 - Sarra Trabelsi',
                        'EMP003 - Mohamed Salah'
                    ]"
                    required="true"/>

            </div>


        </x-card>

        {{-- =========================
            2. Informations professionnelles
        ========================= --}}
        <x-card>

            <x-section-title
                number="2"
                title="Informations Professionnelles"
                icon="fa-briefcase"/>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <x-field
                    name="departement"
                    label="Département"
                    placeholder="Ex: Informatique"/>

                <x-field
                    name="poste_occupe"
                    label="Poste occupé"
                    placeholder="Ex: Développeur Senior"/>

                
                <x-field
                    type="date"
                    name="date_embauche"
                    label="Date d'embauche"/>

                <x-field
                    type="date"
                    name="date_prisePoste"
                    label="Date de prise de poste"/>

                <x-field
                    type="number"
                    name="jours_conges"
                    label="Jours de congés acquis"/>

            </div>

        </x-card>

        {{-- =========================
            3. Informations contractuelles
        ========================= --}}
        <x-card>

            <x-section-title
                number="3"
                title="Informations Contractuelles"
                icon="fa-file-contract"/>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <x-field
                    name="numero_contrat"
                    label="Numéro du contrat"
                    placeholder="CTR-2025-001"/>

                <x-select
                    name="type_contrat"
                    label="Type de contrat"
                    :options="['CDI','CDD','Stage','Freelance','Consultant','Intérim']"/>

                <x-field
                    type="date"
                    name="date_debut"
                    label="Date de début"/>

                <x-field
                    type="date"
                    name="date_fin"
                    label="Date de fin"/>

                <x-field
                    name="recruteur"
                    label="Nom du recruteur"/>

                <x-field
                    type="number"
                    name="salaire"
                    label="Salaire"/>

            </div>

        </x-card>

        <div class="flex justify-end mt-6">

            <button
                type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium">
                <i class="fa-solid fa-check"></i>
                Valider
            </button>

        </div>

    </form>

</div>

@endsection

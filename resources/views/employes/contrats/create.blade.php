@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <!-- En-tête -->
    <div class="flex items-center justify-between mb-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Informations du contrat
        </h1>

        <x-annuler />
    </div>

    <!-- Formulaire -->
    <form action="{{ route('employes.contrats.store') }}"
        method="POST">

        @csrf

        @include('formulaires.formcontrat')

        <div class="flex justify-end mt-6">

            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-check"></i>
                Valider
            </button>

        </div>

    </form>

</div>

@endsection

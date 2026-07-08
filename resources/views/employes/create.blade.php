@extends('layouts.layout')

@section('content')

<div class="max-w-7xl mx-auto">

    <div class="flex items-center justify-between mb-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Les Coordonnées de l'Employé
        </h1>

        <a href="{{ route('employes.index') }}"
            class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
            Annuler
        </a>

    </div>

    <form action="{{ route('employes.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf

        @include('formulaires.form')

        <div class="flex justify-end mt-2">
             <button
                type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-check"></i>
                Valider
            </button>
        </div>

    </form>

</div>

@endsection

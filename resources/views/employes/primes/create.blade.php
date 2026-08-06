@extends('layouts.layout')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="flex items-center justify-between mb-6">

        <h1 class="text-2xl font-bold text-gray-900">
            Nouvelle prime
        </h1>

        <x-annuler />

    </div>

    <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6">

        <form action="{{ route('employes.primes.store') }}" method="POST">

            @csrf

            @include('formulaires.formprime')

            <div class="flex justify-end mt-4">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                    <i class="fa-solid fa-check"></i>
                    Valider
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

@extends('layouts.layout')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Modifier le client</h1>
        <x-annuler />
    </div>

    @if($errors->any())
        <div class="mb-6 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('clients.update', $client->id) }}" method="POST"
        class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-6 space-y-4">
        @csrf
        @method('PUT')

         <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Nom du fournisseur</label>
            <input type="text" name="nom" value="{{ $client->nom }}" readonly
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2.5 text-sm text-gray-600 cursor-not-allowed">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Matricule fiscal</label>
           <input type="text" name="matricule_fiscal" value="{{ $client->matricule_fiscal }}" readonly
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-3 py-2.5 text-sm text-gray-600 cursor-not-allowed">         
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $client->email) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone', $client->telephone) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Adresse</label>
            <input type="text" name="adresse" value="{{ old('adresse', $client->adresse) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition">
        </div>



        <div class="flex justify-end pt-2">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                <i class="fa-solid fa-check"></i> Enregistrer les modifications
            </button>
        </div>
    </form>

</div>
@endsection

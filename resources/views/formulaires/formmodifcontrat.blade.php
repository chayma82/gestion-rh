<x-card>
    <x-section-title number="" title="Informations du contrat (non modifiables)" icon="fa-lock"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Employé</label>
            <input type="text" value="{{ $contrat->employe->matricule_nom_complet }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Numéro de contrat</label>
            <input type="text" value="{{ $contrat->numcontrat }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Date de début</label>
            <input type="text" value="{{ $contrat->date_debut?->format('Y-m-d') }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Statut</label>
            <input type="text" value="{{ $contrat->statut }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>
        <div>
    <label class="text-xs text-gray-400 mb-1 block">Type de contrat</label>
    <input type="text" value="{{ $contrat->typeContrat }}" disabled
        class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">

    {{-- On envoie quand même la valeur au serveur --}}
    <input type="hidden" name="type_contrat" value="{{ $contrat->typeContrat }}">
</div>

    </div>
</x-card>

<x-card>
    <x-section-title number="" title="Informations Modifiables" icon="fa-file-pen"/>
        <div>
    <label class="text-xs text-gray-400 mb-1 block">Département</label>

    <input type="text"
        value="{{ $contrat->departement?->libelle ?? '_' }}"
        disabled
        class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">

    {{-- On envoie quand même l'id au serveur --}}
    <input type="hidden"
        name="departement_id"
        value="{{ $contrat->departement_id }}">
</div>
@if ($errors->any())
    <pre>{{ $errors }}</pre>
@endif

<div>
    <label class="text-xs text-gray-400 mb-1 block">Poste</label>

    <input type="text"
        value="{{ $contrat->poste?->libelle ?? '_' }}"
        disabled
        class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed">

    {{-- On envoie quand même l'id au serveur --}}
    <input type="hidden"
        name="poste_id"
        value="{{ $contrat->poste_id }}">
</div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Date de fin</label>
            <input type="date" name="date_fin" value="{{ old('date_fin', optional($contrat->date_fin)->format('Y-m-d')) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            @error('date_fin')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Jours de congés acquis</label>
            <input type="number" name="jours_conges" min="0" value="{{ old('jours_conges', $contrat->nbreJourCongeAqcuise) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            @error('jours_conges')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Salaire (DT)</label>
            <input type="number" step="0.01" name="salaire" min="0" value="{{ old('salaire', $contrat->salaire_base) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            @error('salaire')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="text-xs text-gray-400 mb-1 block">Nom du recruteur</label>
            <input type="text" name="recruteur" value="{{ old('recruteur', $contrat->recreteur) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            @error('recruteur')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
</x-card>

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

    </div>
</x-card>

<x-card>
    <x-section-title number="" title="Informations Modifiables" icon="fa-file-pen"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Type de contrat</label>
            <select name="type_contrat"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                <option value="CDI" @selected(old('type_contrat', $contrat->typeContrat) == 'CDI')>CDI</option>
                <option value="CDD" @selected(old('type_contrat', $contrat->typeContrat) == 'CDD')>CDD</option>
                <option value="Stage" @selected(old('type_contrat', $contrat->typeContrat) == 'Stage')>Stage</option>
                <option value="Freelance" @selected(old('type_contrat', $contrat->typeContrat) == 'Freelance')>Freelance</option>
                <option value="Consultant" @selected(old('type_contrat', $contrat->typeContrat) == 'Consultant')>Consultant</option>
                <option value="Interimaire" @selected(old('type_contrat', $contrat->typeContrat) == 'Interimaire')>Intérimaire</option>
            </select>
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Date de fin</label>
            <input type="date" name="date_fin" value="{{ old('date_fin', optional($contrat->date_fin)->format('Y-m-d')) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div class="md:col-span-2">
            <label class="text-xs text-gray-400 mb-1 block">Nom du recruteur</label>
            <input type="text" name="recruteur" value="{{ old('recruteur', $contrat->recreteur) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

    </div>
</x-card>

<div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-medium text-gray-700">Département</label>

            <div class="relative">
                <select name="departement_id"
                    class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-3 pr-10 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm bg-white">
                    <option value="">Sélectionner un département</option>
                    @foreach($departements as $departement)
                        <option value="{{ $departement->id }}"
                            @selected(old('departement_id', $poste->departement_id ?? '') == $departement->id)>
                            {{ $departement->libelle }} ({{ $departement->code }})
                        </option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Code</label>
            <input
                type="text"
                name="code"
                value="{{ old('code', $poste->code ?? '') }}"
                placeholder="Ex : DEV, COMPTA"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Libellé</label>
            <input
                type="text"
                name="libelle"
                value="{{ old('libelle', $poste->libelle ?? '') }}"
                placeholder="Ex : Développeur"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
        </div>

    </div>

</div>

<div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Code</label>
            <input
                type="text"
                name="code"
                value="{{ old('code', $departement->code ?? '') }}"
                placeholder="Ex : RH, IT, FIN"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
        </div>

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Libellé</label>
            <input
                type="text"
                name="libelle"
                value="{{ old('libelle', $departement->libelle ?? '') }}"
                placeholder="Ex : Ressources Humaines"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
        </div>

    </div>

</div>

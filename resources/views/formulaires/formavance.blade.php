@if ($errors->any())
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div class="md:col-span-2">

        <x-select
            id="employe_id"
            name="employe_id"
            label="Employé"
            :options="$employes->pluck('matricule_nom_complet', 'id')->toArray()"
            :value="old('employe_id')"
            required="true"
        />

    </div>


    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">
            Montant (DT)
        </label>

        <input type="number"
            step="0.01"
            min="0.01"
            name="montant"
            value="{{ old('montant') }}"
            required
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
            focus:outline-none focus:ring-2 focus:ring-orange-500
            focus:border-orange-500 transition">
    </div>


    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">
            Date
        </label>

        <input type="date"
            name="date_avance"
            value="{{ old('date_avance', now()->format('Y-m-d')) }}"
            required
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
            focus:outline-none focus:ring-2 focus:ring-orange-500
            focus:border-orange-500 transition">
    </div>


    <div class="md:col-span-2">

        <label class="block text-xs font-medium text-gray-600 mb-1">
            Motif (optionnel)
        </label>

        <input type="text"
            name="motif"
            value="{{ old('motif') }}"
            maxlength="255"
            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
            focus:outline-none focus:ring-2 focus:ring-orange-500
            focus:border-orange-500 transition">

    </div>

</div>

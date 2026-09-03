<div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8 space-y-6">

    {{-- Nom du rôle --}}
    <div>
        <label for="nom" class="block mb-2 text-sm font-medium text-gray-700">
            Nom du rôle
        </label>
        <input type="text" name="nom" id="nom"
            value="{{ old('nom', $role->nom ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-700 text-sm"
            placeholder="Ex : Gestionnaire" required>
    </div>

    {{-- Accès --}}
    <div>
        <label class="block mb-3 text-sm font-medium text-gray-700">
            Accès accordés à ce rôle
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" name="acces_admin" value="1"
                    @checked(old('acces_admin', $role->acces_admin ?? false))
                    class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm font-medium text-gray-700">Admin</span>
            </label>

            <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" name="acces_facturation" value="1"
                    @checked(old('acces_facturation', $role->acces_facturation ?? false))
                    class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm font-medium text-gray-700">Facturation</span>
            </label>

            <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" name="acces_rh" value="1"
                    @checked(old('acces_rh', $role->acces_rh ?? false))
                    class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                <span class="text-sm font-medium text-gray-700">RH</span>
            </label>

        </div>
    </div>

</div>

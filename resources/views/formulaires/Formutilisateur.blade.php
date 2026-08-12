@php
    $isEdit = isset($utilisateur);
@endphp

<x-card>

    <x-section-title
        number="1"
        title="Informations personnelles"
        icon="fa-user"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <x-field
            name="nom"
            label="Nom"
            value="{{ old('nom', $utilisateur->nom ?? '') }}"
            required="true"/>

        <x-field
            name="prenom"
            label="Prénom"
            value="{{ old('prenom', $utilisateur->prenom ?? '') }}"
            required="true"/>

        <x-field
            type="email"
            name="email"
            label="E-mail"
            value="{{ old('email', $utilisateur->email ?? '') }}"
            required="true"/>

        <x-field
            name="telephone"
            label="Téléphone"
            value="{{ old('telephone', $utilisateur->telephone ?? '') }}"/>

    </div>

</x-card>

<x-card>

    <x-section-title
        number="2"
        title="Accès et rôle"
        icon="fa-shield-halved"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <x-select
            name="role_id"
            label="Rôle"
            :options="$roles->pluck('nom', 'id')->toArray()"
            :selected="old('role_id', $utilisateur->role_id ?? '')"
            required="true"/>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut du compte</label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="hidden" name="actif" value="0">
                <input
                    type="checkbox"
                    name="actif"
                    value="1"
                    class="w-4 h-4 rounded border-gray-300 text-[#E2721B] focus:ring-orange-500"
                    {{ old('actif', $utilisateur->actif ?? true) ? 'checked' : '' }}>
                <span class="text-sm text-gray-700">Compte actif (accès autorisé au site)</span>
            </label>
        </div>

        <x-field
            type="password"
            name="motdepasse"
            label="{{ $isEdit ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe' }}"
            required="{{ $isEdit ? 'false' : 'true' }}"/>

        <x-field
            type="password"
            name="motdepasse_confirmation"
            label="Confirmer le mot de passe"
            required="{{ $isEdit ? 'false' : 'true' }}"/>

    </div>

</x-card>

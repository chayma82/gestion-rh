<x-card>

    <x-section-title
        number="1"
        title="Informations du rôle"
        icon="fa-shield-halved"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <x-field
            name="nom"
            label="Nom du rôle"
            placeholder="Ex: RH, Manager, Comptable"
            value="{{ old('nom', $role->nom ?? '') }}"
            required="true"/>

    </div>

</x-card>

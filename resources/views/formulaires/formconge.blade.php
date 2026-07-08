<x-card>

    <x-section-title
        number="1"
        title="Informations de l'employé"
        icon="fa-user"/>

    <div class="grid grid-cols-1">

        <x-select
            name="employe"
            label="Employé"
            :options="[
                'EMP001 - Ahmed Ben Ali',
                'EMP002 - Sarra Trabelsi',
                'EMP003 - Mohamed Salah'
            ]"
            required="true"/>

    </div>

</x-card>
<x-card>

    <x-section-title
        number="2"
        title="Informations du congé"
        icon="fa-calendar-days"/>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <x-select
            name="type_conge"
            label="Type de congé"
            :options="[
                'Congé annuel',
                'Congé maladie',
                'Congé maternité',
                'Congé sans solde',
                'Autre'
            ]"
            required="true"/>

        <x-field
            type="date"
            name="date_debut"
            label="Date de début"
            required="true"/>

        <x-field
            type="date"
            name="date_fin"
            label="Date de fin"
            required="true"/>

    </div>

</x-card>
<x-card>

    <x-section-title
        number="3"
        title="Motif du congé"
        icon="fa-comment"/>

    <x-textarea
        name="motif"
        label="Motif"
        placeholder="Décrivez la raison du congé..."/>

</x-card>
<x-card>

    <x-section-title
        number="4"
        title="Justificatif"
        icon="fa-file-arrow-up"/>

    <label for="justificatif"
        class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-orange-200 rounded-xl py-10 cursor-pointer hover:bg-orange-50 transition">

        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400"></i>

        <p class="text-sm text-gray-600">
            Cliquez pour sélectionner un fichier
        </p>

        <p class="text-xs text-gray-400">
            PDF, JPG ou PNG (5 Mo max)
        </p>

        <input
            id="justificatif"
            type="file"
            name="justificatif"
            class="hidden">

    </label>

</x-card>

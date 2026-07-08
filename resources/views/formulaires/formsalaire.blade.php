{{-- ========================= 1. information ========================= --}}
<x-card>

    <x-section-title
        number="1"
        title="Informations de l'employé"
        icon="fa-user"/>

    {{-- Sélection de l'employé --}}
    <div class="grid grid-cols-1 mb-6">

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

{{-- Informations affichées automatiquement --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-5">

        <x-field
            name="matricule"
            label="Matricule"
            value="EMP001"
            readonly/>

        <x-field
            name="nom"
            label="Nom"
            value="Ben Ali"
            readonly/>

        <x-field
            name="prenom"
            label="Prénom"
            value="Ahmed"
            readonly/>

        <x-field
            name="numero_contrat"
            label="N° Contrat"
            value="CTR-2025-001"
            readonly/>

    </div>

</x-card>{{-- ========================= 2. SALAIRE ========================= --}}
<x-card>

    <x-section-title
        number="2"
        title="Modification du salaire"
        icon="fa-money-bill-wave"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <x-field
            name="ancien_salaire"
            label="Ancien salaire"
            value="3000 DT"
            disabled/>

        <x-field
            name="nouveau_salaire"
            label="Nouveau salaire"
            type="number"
            placeholder="3200"
            />

    </div>

</x-card>
{{-- ========================= 3. avance ========================= --}}
<x-card>

    <x-section-title
        number="3"
        title="Avance sur salaire"
        icon="fa-hand-holding-dollar"/>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <x-field
            name="avance"
            label="Montant"
            type="number"
            placeholder="0"/>

        <x-field
            name="date_avance"
            label="Date"
            type="date"/>

        <x-field
            name="motif_avance"
            label="Motif"
            placeholder="Ex : Urgence familiale"/>

    </div>

</x-card>
{{-- ========================= 4. prime ========================= --}}
<x-card>

    <x-section-title
        number="4"
        title="Ajouter une prime"
        icon="fa-gift"/>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <x-field
            name="montant_prime"
            label="Montant de la prime"
            type="number"
            placeholder="0"/>

        <x-field
            name="date_prime"
            label="Date"
            type="date"/>

        <x-field
            name="motif_prime"
            label="Motif"
            placeholder="Prime de rendement"/>

    </div>

</x-card>

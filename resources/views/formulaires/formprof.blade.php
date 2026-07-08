
{{-- =========================
    1. PROFESSIONNELLES
========================= --}}

<x-card>
    <x-section-title number="1" title="Informations Professionnelles" icon="fa-briefcase"/>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

        <x-field name="departement" label="Département"
            placeholder="Ex: Informatique"
            required="true"/>

        <x-field name="poste_occupe" label="Poste occupé"
            placeholder="Ex: Développeur Senior" required="true"/>

        <x-select name="statut_employe" label="Statut employé"
            :options="['CDI','CDD','Stage','Freelance','Intérim']"
            required="true"/>

        <x-field type="date" name="date_Emboche" label="Date d'embauche"
            :value="$employee->date_Emboche ?? ''" required="true"/>

        <x-field type="date" name="date_prisePoste" label="Date de prise de poste"
            :value="$employee->date_ProsePoste ?? ''" required="true"/>

        <x-field type="number" name="jours_conges" label="Nombre de jours de congés acquis"
            placeholder="0"
            :value="$employee->jours_conges ?? ''"/>

    </div>
</x-card>


{{-- =========================
    2. CONTRAT
========================= --}}

<x-card>
    <x-section-title number="2" title="Informations Contractuelles" icon="fa-file-contract"/>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

        <x-field name="numero_contrat" label="Numéro du contrat"
            placeholder="Ex: CNT-2024-001"
            :value="$employee->numero_contrat ?? ''" required="true"/>

        <x-select name="type_contrat_final" label="Type de contrat"
            :options="['CDI','CDD','Stage','Freelance','Consultant','Intérim']"
            :value="$employee->type_contrat_final ?? ''" required="true"/>

        <x-field type="date" name="date_debut" label="Date de début de contrat"
            :value="$employee->date_debut ?? ''" required="true"/>

        <x-field type="date" name="date_fin" label="Date de fin de contrat"
            :value="$employee->date_fin ?? ''"/>

        <x-field name="NomRecruteur" label="Nom du recruteur"
            placeholder="Ex: Marie Martin"
            :value="$employee->NomRecruteur ?? ''" required="true"/>

        <x-field type="number" name="salaire" label="Salaire"
            placeholder="Ex: 35000"
            :value="$employee->salaire ?? ''" required="true"/>

    </div>
</x-card>

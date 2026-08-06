{{-- =========================
    1. Informations employé
========================= --}}
<x-card>


    <x-select
        id="employe_id"
        name="employe_id"
        label="Employé"
        :options="$employes->pluck('matricule_nom_complet', 'id')->toArray()"
        required="true"
    />


</x-card>

{{-- =========================
    2. Informations professionnelles
========================= --}}
<x-card>

    <x-section-title
        number="2"
        title="Informations Professionnelles"
        icon="fa-briefcase"/>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <x-select
            id="departement_id"
            name="departement_id"
            label="Département"
            :options="$departements->pluck('libelle', 'id')->toArray()"
            required="true"/>

        <x-select
            id="poste_id"
            name="poste_id"
            label="Poste occupé"
            :options="$postes->pluck('libelle', 'id')->toArray()"
            required="true"/>

        <x-field
            type="date"
            name="date_embauche"
            label="Date d'embauche"
            required="true"/>



        <x-field
            type="number"
            name="jours_conges"
            label="Jours de congés acquis"
            required="true"/>

    </div>

</x-card>

{{-- =========================
    3. Informations contractuelles
    (le numéro de contrat n'apparaît pas ici : il est généré
    automatiquement côté serveur au format
    CNT-codeCategorie-idEmploye-idContrat-anneeEmbauche.
    Le matricule employé est lui aussi généré automatiquement côté serveur
    au format codeDepartement-codePoste-idEmploye-idClient-anneeEmbauche)
========================= --}}
<x-card>

    <x-section-title
        number="3"
        title="Informations Contractuelles"
        icon="fa-file-contract"/>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <x-select
            name="type_contrat"
            label="Type de contrat"
            :options="[
                'CDI' => 'CDI',
                'CDD' => 'CDD',
                'Stage' => 'Stage',
                'Freelance' => 'Freelance',
                'Consultant' => 'Consultant',
                'Interimaire' => 'Intérimaire'
            ]"
            required="true"
        />

        <x-field
            type="date"
            name="date_debut"
            label="Date de début"
            required="true"/>

        <x-field
            type="date"
            name="date_fin"
            label="Date de fin"/>

        <x-field
            name="recruteur"
            label="Nom du recruteur"
            required="true"/>

        <x-field
            type="number"
            name="salaire"
            label="Salaire"
            required="true"/>

    </div>

</x-card>

{{-- =========================
    Filtre JS : limite le select "Poste occupé" au département choisi.
    Ne modifie pas les composants x-select existants, s'appuie juste
    sur leurs id (departement_id / poste_id) déjà présents ci-dessus.
========================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // ---- Filtre Poste selon Département ----
        // Map id_poste => id_departement, construite depuis la collection $postes
        const posteDepartementMap = @json($postes->pluck('departement_id', 'id'));

        const departementSelect = document.querySelector('[name="departement_id"]');
        const posteSelect = document.querySelector('[name="poste_id"]');

        console.log(departementSelect);
        console.log(posteSelect);



        if (departementSelect && posteSelect) {
            departementSelect.addEventListener('change', function () {
                const departementId = this.value;

                [...posteSelect.options].forEach(function (option) {
                    if (!option.value) return; // garde l'option vide visible

                    const departementPoste = String(posteDepartementMap[option.value] ?? '');
                    option.hidden = departementId !== '' && departementPoste !== departementId;
                });

                const selected = posteSelect.options[posteSelect.selectedIndex];
                if (departementId !== '' && selected && selected.hidden) {
                    posteSelect.value = '';
                }
            });
        }
    });
</script>

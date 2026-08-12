<x-card>

    <div class="grid grid-cols-1 gap-6">

         <x-select
        id="employe_id"
        name="employe_id"
        label="Employé"
        :options="$employes->pluck('matricule_nom_complet', 'id')->toArray()"
        required="true"
    />

         <div id="soldeConge" class="hidden text-sm text-gray-600">
            Solde disponible :
            <span id="nombreJours" class="font-semibold"></span>
            jours
        </div>


        <div id="soldeInsuffisant"
             class="hidden text-sm text-red-600">
            Le solde de congé est insuffisant.
        </div>

    </div>

</x-card>


<x-card>

    <x-section-title
        number=""
        title="Informations du congé"
        icon="fa-calendar-days"/>


    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


        <x-select
            id="type_conge"
            name="type_conge"
            label="Type de congé"
            :options="[
                'paye'=>'Congé payé',
                'maladie'=>'Congé maladie',
                'sans_solde'=>'Congé sans solde'
            ]"
            required="true"/>



        <x-field
            type="date"
            id="date_debut"
            name="date_debut"
            label="Date début"
            required="true"/>



        <x-field
            type="date"
            id="date_fin"
            name="date_fin"
            label="Date fin"
            required="true"/>


    </div>


</x-card>




<x-card>

    <x-section-title
        number=""
        title="Solde et motif"
        icon="fa-info-circle"/>


    <div class="grid grid-cols-1 gap-6">






        <x-textarea
            name="motif"
            label="Motif"
            placeholder="Raison du congé"/>



        <div>

            <label class="block text-sm font-medium text-gray-700 mb-2">
                Justificatif
            </label>

            <input
                type="file"
                name="justificatif"
                class="border rounded-lg p-2 w-full">

        </div>


    </div>


</x-card>



<script>

document.addEventListener('DOMContentLoaded', function () {


    const employes = @json($employes);


    const employeSelect   = document.getElementById('employe_id');
    const typeSelect      = document.getElementById('type_conge');
    const dateDebutInput  = document.getElementById('date_debut');
    const dateFinInput    = document.getElementById('date_fin');

    const soldeConge       = document.getElementById('soldeConge');
    const nombreJours      = document.getElementById('nombreJours');
    const soldeInsuffisant = document.getElementById('soldeInsuffisant');

    const boutonValider = document.querySelector('button[type="submit"]');



    function getEmployeSelectionne()
    {
        const id = employeSelect.value;

        return employes.find(e => e.id == id) || null;
    }



    function afficherSolde()
    {

        const employe = getEmployeSelectionne();


        if(employe)
        {
            soldeConge.classList.remove('hidden');

            nombreJours.innerText =
                employe.solde_conge ?? 0;
        }
        else
        {
            soldeConge.classList.add('hidden');
        }


        verifierSolde();

    }



    function verifierSolde()
    {

        const employe = getEmployeSelectionne();


        soldeInsuffisant.classList.add('hidden');


        if(boutonValider)
        {
            boutonValider.disabled = false;
        }



        if(!employe || typeSelect.value !== 'paye')
        {
            return;
        }



        if(!dateDebutInput.value || !dateFinInput.value)
        {
            return;
        }



        const debut = new Date(dateDebutInput.value);

        const fin = new Date(dateFinInput.value);



        if(fin < debut)
        {
            return;
        }



        const joursDemandes =
            Math.round(
                (fin - debut) /
                (1000 * 60 * 60 * 24)
            ) + 1;



        if(joursDemandes > (employe.solde_conge ?? 0))
        {

            soldeInsuffisant.classList.remove('hidden');


            if(boutonValider)
            {
                boutonValider.disabled = true;
            }

        }

    }




    employeSelect.addEventListener(
        'change',
        afficherSolde
    );


    typeSelect.addEventListener(
        'change',
        verifierSolde
    );


    dateDebutInput.addEventListener(
        'change',
        verifierSolde
    );


    dateFinInput.addEventListener(
        'change',
        verifierSolde
    );


});

</script>

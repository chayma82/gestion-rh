{{-- ========================= 1. Employé ========================= --}}

<x-card>


    <x-select
        id="employe_id"
        name="employe_id"
        label="Employé"
        :options="$employes->pluck('matricule_nom_complet', 'id')->toArray()"
        required="true"
    />


</x-card>


{{-- ========================= 2. SALAIRE ========================= --}}

<x-card>

    <x-section-title
        number=""
        title="Modification du salaire"
        icon="fa-money-bill-wave"/>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">




        <x-field
            name="nouveau_salaire"
            label="Nouveau salaire"
            type="number"
            placeholder="Ex: 3200"
            required
        />


    </div>


</x-card>




<script>

document.addEventListener('DOMContentLoaded', function () {


    const employesData = @json($employesData ?? []);

    const employeSelect = document.getElementById('employe_id');

    const ancienSalaire = document.getElementById('ancien_salaire');



    function remplirSalaire() {


        let id = employeSelect.value;


        let data = employesData[id] ?? null;


        if(data){

            ancienSalaire.value = data.ancien_salaire ?? 0;

        }else{

            ancienSalaire.value = '';

        }

    }



    employeSelect.addEventListener('change', remplirSalaire);


    remplirSalaire();


});

</script>

{{--
    ===========================================================
    formmodifemploye.blade.php
    -----------------------------------------------------------
    - Sélection d'un employé existant dans la liste déroulante
    - Auto-remplissage de TOUS les champs à partir des données
      de l'employé sélectionné
    - Verrouillage automatique des champs "identité" qui ne
      doivent PAS être modifiables :
          matricule, nom, prenom, sexe, date_naissance,
          lieu_naissance, nationalite, cin_passeport
    - Les autres champs restent modifiables :
          situation_familiale, nb_enfants, coordonnées,
          contact d'urgence...
    ===========================================================
--}}

<form method="POST" action="# }}" id="formModifEmploye">
    @csrf
    @method('PUT')

    {{-- =========================
        0. SÉLECTION DE L'EMPLOYÉ
    ========================= --}}
    <x-card>
        <div class="grid grid-cols-1 mb-6">

            <x-select
                name="employe_select"
                id="employe_select"
                label="Employé"
                :options="[
                    'EMP001 - Ahmed Ben Ali',
                    'EMP002 - Sarra Trabelsi',
                    'EMP003 - Mohamed Salah'
                ]"
                required="true"/>

        </div>
    </x-card>

    {{-- =========================
        1. INFORMATIONS PERSONNELLES
        (champs verrouillés après sélection)
    ========================= --}}

    <x-card>
        <x-section-title number="1" title="Informations Personnelles" icon="fa-user"/>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">


            {{-- select : pas d'attribut "readonly" HTML valide sur un <select>.
                 On le "disabled" via JS + on ajoute un input hidden pour que
                 la valeur soit quand même envoyée au submit. --}}
            <x-select name="sexe" id="sexe" label="Sexe"
                :options="['Homme','Femme']"
                :value="$employee->sexe ?? ''"
                data-locked="true"
                required="true"/>
            <input type="hidden" name="sexe_hidden" id="sexe_hidden" value="{{ $employee->sexe ?? '' }}">

            <x-field type="date" name="date_naissance" id="date_naissance" label="Date de naissance"
                data-locked="true"
                :value="$employee->date_naissance ?? ''" required="true" readonly/>

            <x-field name="lieu_naissance" id="lieu_naissance" label="Lieu de naissance"
                placeholder="Ville, Pays"
                data-locked="true"
                :value="$employee->lieu_naissance ?? ''" required="true" readonly/>

            <x-field name="nationalite" id="nationalite" label="Nationalité"
                placeholder="Ex: Française"
                data-locked="true"
                :value="$employee->nationalite ?? ''" required="true" readonly/>

            <x-field name="cin_passeport" id="cin_passeport" label="CIN / Passeport"
                placeholder="Numéro de pièce d'identité"
                data-locked="true"
                :value="$employee->cin_passeport ?? ''" required="true" readonly/>

            {{-- Champs modifiables même après sélection --}}
            <x-select name="situation_familiale" id="situation_familiale" label="Situation familiale"
                :options="['Célibataire','Marié(e)','Divorcé(e)','Veuf(ve)']"
                :value="$employee->situation_familiale ?? ''" />

            <x-field type="number" name="nb_enfants" id="nb_enfants" label="Nombre d'enfants"
                placeholder="0"
                :value="$employee->nb_enfants ?? ''"/>

        </div>
    </x-card>

    {{-- =========================
        2. COORDONNÉES (modifiable)
    ========================= --}}

    <x-card>
        <x-section-title number="2" title="Coordonnées de Contact" icon="fa-envelope"/>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

            <x-textarea name="adresse" id="adresse" label="Adresse"
                placeholder="Numéro et nom de rue"
                :value="$employee->adresse ?? ''" required="true"/>

            <x-field name="ville" id="ville" label="Ville"
                placeholder="Ville"
                :value="$employee->ville ?? ''" required="true"/>

            <x-field name="code_postal" id="code_postal" label="Code Postal"
                placeholder="Ex: 75001"
                :value="$employee->code_postal ?? ''" required="true"/>

            <x-field name="tel_perso" id="tel_perso" label="Téléphone personnel"
                placeholder="+33 6 00 00 00 00"
                :value="$employee->tel_perso ?? ''" required="true"/>

            <x-field name="tel_pro" id="tel_pro" label="Téléphone professionnel"
                placeholder="+33 1 00 00 00 00"
                :value="$employee->tel_pro ?? ''"/>

            <x-field type="email" name="email_perso" id="email_perso" label="Email personnel"
                placeholder="jean.luc@example.com"
                :value="$employee->email_perso ?? ''" required="true"/>

            <x-field type="email" name="email_pro" id="email_pro" label="Email professionnel"
                placeholder="j.dupont@lumina.com"
                :value="$employee->email_pro ?? ''"/>

        </div>
    </x-card>

    {{-- =========================
        3. CONTACT URGENCE (modifiable)
    ========================= --}}

    <x-card accent="true">
        <x-section-title number="3" title="Contact d'Urgence" icon="fa-phone"/>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

            <x-field name="nom_urgence" id="nom_urgence" label="Nom du contact d'urgence"
                placeholder="Nom et Prénom"
                :value="$employee->nom_urgence ?? ''" required="true"/>

            <x-field name="lien_parente" id="lien_parente" label="Lien de parenté"
                placeholder="Ex: Conjoint, Parent, Ami"
                :value="$employee->lien_parente ?? ''" required="true"/>

            <x-field name="tel_urgence" id="tel_urgence" label="Téléphone d'urgence"
                placeholder="+33 6 00 00 00 00"
                :value="$employee->tel_urgence ?? ''" required="true"/>

            <x-textarea name="adresse_urgence" id="adresse_urgence" label="Adresse d'urgence"
                placeholder="Adresse complète du contact..."
                :value="$employee->adresse_urgence ?? ''" required="true"/>

        </div>
    </x-card>

   
</form>

{{--
    ===========================================================
    DONNÉES DES EMPLOYÉS
    -----------------------------------------------------------
    Idéalement, ces données viennent du contrôleur Laravel,
    par ex. dans EmployeController@edit :

        $employes = Employee::all();
        return view('formmodifemploye', compact('employes'));

    et ici on ferait simplement :

        const employesData = @json(
            $employes->keyBy(fn($e) => $e->matricule)
        );

    En attendant, exemple statique ci-dessous (à remplacer).
    ===========================================================
--}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---- Remplacer ceci par les vraies données Laravel (voir commentaire ci-dessus) ----
    const employesData = {
        'EMP001': {
            matricule: 'EMP001',
            nom: 'Ben Ali',
            prenom: 'Ahmed',
            sexe: 'Homme',
            date_naissance: '1990-05-12',
            lieu_naissance: 'Tunis, Tunisie',
            nationalite: 'Tunisienne',
            cin_passeport: '08123456',
            situation_familiale: 'Marié(e)',
            nb_enfants: 2,
            adresse: '12 Rue de la République',
            ville: 'Tunis',
            code_postal: '1000',
            tel_perso: '+216 20 000 000',
            tel_pro: '+216 71 000 000',
            email_perso: 'ahmed.benali@example.com',
            email_pro: 'a.benali@societe.com',
            nom_urgence: 'Fatma Ben Ali',
            lien_parente: 'Épouse',
            tel_urgence: '+216 22 111 222',
            adresse_urgence: '12 Rue de la République, Tunis'
        },
        'EMP002': {
            matricule: 'EMP002',
            nom: 'Trabelsi',
            prenom: 'Sarra',
            sexe: 'Femme',
            date_naissance: '1988-11-03',
            lieu_naissance: 'Sfax, Tunisie',
            nationalite: 'Tunisienne',
            cin_passeport: '07654321',
            situation_familiale: 'Célibataire',
            nb_enfants: 0,
            adresse: '5 Avenue Habib Bourguiba',
            ville: 'Sfax',
            code_postal: '3000',
            tel_perso: '+216 21 222 333',
            tel_pro: '+216 74 555 666',
            email_perso: 'sarra.trabelsi@example.com',
            email_pro: 's.trabelsi@societe.com',
            nom_urgence: 'Karim Trabelsi',
            lien_parente: 'Frère',
            tel_urgence: '+216 23 444 555',
            adresse_urgence: '5 Avenue Habib Bourguiba, Sfax'
        },
        'EMP003': {
            matricule: 'EMP003',
            nom: 'Salah',
            prenom: 'Mohamed',
            sexe: 'Homme',
            date_naissance: '1995-02-20',
            lieu_naissance: 'Sousse, Tunisie',
            nationalite: 'Tunisienne',
            cin_passeport: '09876543',
            situation_familiale: 'Marié(e)',
            nb_enfants: 1,
            adresse: '20 Rue Ibn Khaldoun',
            ville: 'Sousse',
            code_postal: '4000',
            tel_perso: '+216 25 666 777',
            tel_pro: '+216 73 888 999',
            email_perso: 'mohamed.salah@example.com',
            email_pro: 'm.salah@societe.com',
            nom_urgence: 'Amira Salah',
            lien_parente: 'Épouse',
            tel_urgence: '+216 26 777 888',
            adresse_urgence: '20 Rue Ibn Khaldoun, Sousse'
        }
    };

    const select = document.getElementById('employe_select');

    // Champs "identité" verrouillés après sélection
    const lockedFieldIds = [
        'matricule', 'nom', 'prenom', 'sexe',
        'date_naissance', 'lieu_naissance',
        'nationalite', 'cin_passeport'
    ];

    // Champs restant toujours modifiables
    const editableFieldIds = [
        'situation_familiale', 'nb_enfants',
        'adresse', 'ville', 'code_postal',
        'tel_perso', 'tel_pro', 'email_perso', 'email_pro',
        'nom_urgence', 'lien_parente', 'tel_urgence', 'adresse_urgence'
    ];

    function extractCode(optionText) {
        // "EMP001 - Ahmed Ben Ali" -> "EMP001"
        return optionText.split(' - ')[0].trim();
    }

    function lockField(id) {
        const el = document.getElementById(id);
        if (!el) return;

        if (el.tagName === 'SELECT') {
            el.setAttribute('disabled', 'disabled');
        } else {
            el.setAttribute('readonly', 'readonly');
        }
        el.classList.add('bg-gray-100', 'cursor-not-allowed', 'text-gray-500');
    }

    function unlockField(id) {
        const el = document.getElementById(id);
        if (!el) return;

        el.removeAttribute('readonly');
        el.removeAttribute('disabled');
        el.classList.remove('bg-gray-100', 'cursor-not-allowed', 'text-gray-500');
    }

    function fillForm(data) {
        Object.keys(data).forEach(function (key) {
            const el = document.getElementById(key);
            if (el) el.value = data[key];
        });

        // Champ hidden pour transmettre la valeur du select "sexe" verrouillé
        const sexeHidden = document.getElementById('sexe_hidden');
        if (sexeHidden) sexeHidden.value = data.sexe ?? '';
    }

    select.addEventListener('change', function () {
        const code = extractCode(this.value);
        const data = employesData[code];

        if (!data) return;

        fillForm(data);

        lockedFieldIds.forEach(lockField);
        editableFieldIds.forEach(unlockField);
    });

    // Avant l'envoi du formulaire : recopier la valeur du select désactivé "sexe"
    document.getElementById('formModifEmploye').addEventListener('submit', function () {
        const sexeSelect = document.getElementById('sexe');
        const sexeHidden = document.getElementById('sexe_hidden');
        if (sexeSelect && sexeHidden) {
            sexeSelect.removeAttribute('disabled'); // pour qu'il soit quand même soumis
        }
    });
});
</script>

{{-- =========================
    1. INFORMATIONS PERSONNELLES
========================= --}}

<x-card>
    <x-section-title number="1" title="Informations Personnelles" icon="fa-user"/>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

        <x-field name="nom" label="Nom" placeholder="Ex: DUPONT"
            :value="$employee->nom ?? ''" required="true"/>

        <x-field name="prenom" label="Prénom" placeholder="Ex: Jean-Luc"
            :value="$employee->prenom ?? ''" required="true"/>

        <x-select name="sexe" label="Sexe"
            :options="['Homme','Femme']"
            :value="$employee->sexe ?? ''"
            required="true"/>

        <x-field type="date" name="date_naissance" label="Date de naissance"
            :value="$employee->date_naissance ?? ''" required="true"/>

        <x-field name="lieu_naissance" label="Lieu de naissance"
            placeholder="Ville, Pays"
            :value="$employee->lieu_naissance ?? ''" required="true"/>

        <x-field name="nationalite" label="Nationalité"
            placeholder="Ex: Française"
            :value="$employee->nationalite ?? ''" required="true"/>

        <x-field name="cin_passeport" label="CIN / Passeport"
            placeholder="Numéro de pièce d'identité"
            :value="$employee->cin_passeport ?? ''" required="true"/>

        <x-select name="situation_familiale" label="Situation familiale"
            :options="['Célibataire','Marié(e)','Divorcé(e)','Veuf(ve)']"
            :value="$employee->situation_familiale ?? ''" />

        <x-field type="number" name="nb_enfants" label="Nombre d'enfants"
            placeholder="0"
            :value="$employee->nb_enfants ?? ''"/>

    </div>
</x-card>

{{-- =========================
    2. COORDONNÉES
========================= --}}

<x-card>
    <x-section-title number="2" title="Coordonnées de Contact" icon="fa-envelope"/>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

        <x-textarea name="adresse" label="Adresse"
            placeholder="Numéro et nom de rue"
            :value="$employee->adresse ?? ''" required="true"/>

        <x-field name="ville" label="Ville"
            placeholder="Ville"
            :value="$employee->ville ?? ''" required="true"/>

        <x-field name="code_postal" label="Code Postal"
            placeholder="Ex: 75001"
            :value="$employee->code_postal ?? ''" required="true"/>

        <x-field name="tel_perso" label="Téléphone personnel"
            placeholder="+33 6 00 00 00 00"
            :value="$employee->tel_perso ?? ''" required="true"/>

        <x-field name="tel_pro" label="Téléphone professionnel"
            placeholder="+33 1 00 00 00 00"
            :value="$employee->tel_pro ?? ''"/>

        <x-field type="email" name="email_perso" label="Email personnel"
            placeholder="jean.luc@example.com"
            :value="$employee->email_perso ?? ''" required="true"/>

        <x-field type="email" name="email_pro" label="Email professionnel"
            placeholder="j.dupont@lumina.com"
            :value="$employee->email_pro ?? ''"/>



    </div>
</x-card>




{{-- =========================
    3. CONTACT URGENCE
========================= --}}

<x-card accent="true">
    <x-section-title number="3" title="Contact d'Urgence" icon="fa-phone"/>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">

        <x-field name="nom_urgence" label="Nom du contact d'urgence"
            placeholder="Nom et Prénom"
            :value="$employee->nom_urgence ?? ''" required="true"/>

        <x-field name="lien_parente" label="Lien de parenté"
            placeholder="Ex: Conjoint, Parent, Ami"
            :value="$employee->lien_parente ?? ''" required="true"/>

        <x-field name="tel_urgence" label="Téléphone d'urgence"
            placeholder="+33 6 00 00 00 00"
            :value="$employee->tel_urgence ?? ''" required="true"/>

        <x-textarea name="adresse_urgence" label="Adresse d'urgence"
            placeholder="Adresse complète du contact..."
            :value="$employee->adresse_urgence ?? ''" required="true"/>

    </div>
</x-card>


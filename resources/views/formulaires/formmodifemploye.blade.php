<x-card>
    <x-section-title number="" title="Détails Personnels " icon="fa-lock"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Nom</label>
            <input type="text" value="{{ $employe->nom }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Prénom</label>
            <input type="text" value="{{ $employe->prenom }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Date de Naissance</label>
            <input type="text" value="{{ $employe->date_naissance?->format('Y-m-d') }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">CIN / Passeport</label>
            <input type="text" value="{{ $employe->cin_passeport }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Genre</label>
            <input type="text" value="{{ $employe->sexe == 'M' ? 'Homme' : ($employe->sexe == 'F' ? 'Femme' : '-') }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Lieu de Naissance</label>
            <input type="text" value="{{ $employe->lieu_naissance }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Nationalité</label>
            <input type="text" value="{{ $employe->nationalite }}" disabled
                class="w-full border border-gray-200 bg-gray-100 rounded-lg px-4 py-2.5 text-sm text-gray-500">
        </div>

    </div>
</x-card>

<x-card>
    <x-section-title number="" title="Informations Modifiables" icon="fa-user-pen"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Situation Familiale</label>
            <select name="situation_familiale"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                <option value="celibataire" @selected(old('situation_familiale', $employe->situation_familiale) == 'celibataire')>Célibataire</option>
                <option value="marie" @selected(old('situation_familiale', $employe->situation_familiale) == 'marie')>Marié(e)</option>
                <option value="divorce" @selected(old('situation_familiale', $employe->situation_familiale) == 'divorce')>Divorcé(e)</option>
                <option value="veuf" @selected(old('situation_familiale', $employe->situation_familiale) == 'veuf')>Veuf(ve)</option>
            </select>
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Nombre d'enfants</label>
            <input type="number" name="nb_enfants" min="0" value="{{ old('nb_enfants', $employe->nb_enfants) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

    </div>
</x-card>
<x-card>
    <x-section-title number="" title="Coordonnées" icon="fa-address-card"/>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Téléphone Personnel</label>
            <input type="text" name="tel_perso" value="{{ old('tel_perso', $employe->tel_perso) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Téléphone Professionnel</label>
            <input type="text" name="tel_pro" value="{{ old('tel_pro', $employe->tel_pro) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">E-mail Personnel</label>
            <input type="email" name="email_perso" value="{{ old('email_perso', $employe->email_perso) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">E-mail Professionnel</label>
            <input type="email" name="email_pro" value="{{ old('email_pro', $employe->email_pro) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Ville</label>
            <input type="text" name="ville" value="{{ old('ville', $employe->ville) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Code Postal</label>
            <input type="text" name="code_postal" value="{{ old('code_postal', $employe->code_postal) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div class="md:col-span-2">
            <label class="text-xs text-gray-400 mb-1 block">Adresse</label>
            <textarea name="adresse" rows="2"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('adresse', $employe->adresse) }}</textarea>
        </div>

    </div>
</x-card>

<x-card accent="true">
    <div class="flex items-center gap-2.5 mb-6">
        <i class="fa-solid fa-triangle-exclamation text-[#E2721B] text-sm"></i>
        <h3 class="text-sm font-bold text-[#E2721B] uppercase tracking-wide">
            Contact d'Urgence
        </h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Nom du Contact</label>
            <input type="text" name="nom_contact_urgence" value="{{ old('nom_contact_urgence', $employe->nom_contact_urgence) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Relation</label>
            <input type="text" name="lien_parente" value="{{ old('lien_parente', $employe->lien_parente) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div>
            <label class="text-xs text-gray-400 mb-1 block">Téléphone</label>
            <input type="text" name="telephone_urgence" value="{{ old('telephone_urgence', $employe->telephone_urgence) }}"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
        </div>

        <div class="md:col-span-2">
            <label class="text-xs text-gray-400 mb-1 block">Adresse</label>
            <textarea name="adresse_urgence" rows="2"
                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('adresse_urgence', $employe->adresse_urgence) }}</textarea>
        </div>

    </div>
</x-card>

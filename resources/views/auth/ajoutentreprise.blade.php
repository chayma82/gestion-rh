<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte entreprise - La Luna</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-[#F9FAFB] min-h-screen py-12">

    <div class="max-w-3xl mx-auto px-4">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
                Créer un compte entreprise
            </h1>
            <p class="text-gray-500 mt-1 text-sm">
                Configurez votre environnement de travail en quelques minutes pour commencer à gérer vos talents.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('auth.store') }}" method="POST">
            @csrf

            {{-- Informations de l'entreprise --}}
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8 mb-6">

                <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-6">
                    <i class="fa-solid fa-building text-[#E2721B] text-sm"></i>
                    Informations de l'entreprise
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Nom de l'entreprise <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nom_entreprise"
                            value="{{ old('nom_entreprise') }}"
                            placeholder="Ex: Tech Holding"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Matricule fiscale <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="num_fiscal"
                            value="{{ old('num_fiscal') }}"
                            placeholder="12345678A"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Secteur d'activité <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select
                                name="secteur_activite"
                                class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-500 bg-white text-sm">
                                <option value="">Sélectionner...</option>
                                <option value="tech" @selected(old('secteur_activite') == 'tech')>Technologie</option>
                                <option value="finance" @selected(old('secteur_activite') == 'finance')>Finance</option>
                                <option value="sante" @selected(old('secteur_activite') == 'sante')>Santé</option>
                                <option value="industrie" @selected(old('secteur_activite') == 'industrie')>Industrie</option>
                                <option value="commerce" @selected(old('secteur_activite') == 'commerce')>Commerce</option>
                                <option value="autre" @selected(old('secteur_activite') == 'autre')>Autre</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Email de l'entreprise <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            name="email_entreprise"
                            value="{{ old('email_entreprise') }}"
                            placeholder="contact@entreprise.com"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                </div>

            </div>

            {{-- Coordonnées administratives de l'entreprise --}}
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8 mb-6">

                <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-6">
                    <i class="fa-solid fa-location-dot text-[#E2721B] text-sm"></i>
                    Coordonnées de l'entreprise
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Adresse du siège <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="adresse"
                            value="{{ old('adresse') }}"
                            placeholder="123 Rue de la République"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Téléphone de l'entreprise
                        </label>
                        <input
                            type="tel"
                            name="telephone_entreprise"
                            value="{{ old('telephone_entreprise') }}"
                            placeholder="+216 00 000 000"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Ville <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="ville"
                            value="{{ old('ville') }}"
                            placeholder="Tunis"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Code Postal <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="code_postal"
                            value="{{ old('code_postal') }}"
                            placeholder="1000"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                </div>

            </div>

            {{-- Compte administrateur --}}
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8 mb-6">

                <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-6">
                    <i class="fa-solid fa-user-gear text-[#E2721B] text-sm"></i>
                    Compte administrateur
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nom"
                            value="{{ old('nom') }}"
                            placeholder="Nom"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="prenom"
                            value="{{ old('prenom') }}"
                            placeholder="Prénom"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Email professionnel <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            name="email_admin"
                            value="{{ old('email_admin') }}"
                            placeholder="admin@entreprise.com"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Téléphone
                        </label>
                        <input
                            type="tel"
                            name="telephone_admin"
                            value="{{ old('telephone_admin') }}"
                            placeholder="+216 00 000 000"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="motdepasse"
                            placeholder="8 caractères minimum"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Confirmer le mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            name="motdepasse_confirmation"
                            placeholder="Confirmer"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                </div>

            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">

                <a href="{{ url()->previous() }}"
                    class="px-6 py-2.5 rounded-lg border border-orange-300 text-[#E2721B] font-medium text-sm hover:bg-orange-50 transition">
                    Annuler
                </a>

                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                    Valider
                </button>

            </div>

        </form>

    </div>

</body>

</html>

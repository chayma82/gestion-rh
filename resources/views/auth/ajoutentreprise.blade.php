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

        <form action="" method="POST">
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
                            Nom de l'organisation <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nom_organisation"
                            placeholder="Ex: Tech Holding"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Matricule Fiscale <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select
                                name="matricule_fiscale"
                                class="w-full appearance-none rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 bg-white text-sm">
                                <option value="12345678A">12345678A</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
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
                                <option value="tech">Technologie</option>
                                <option value="finance">Finance</option>
                                <option value="sante">Santé</option>
                                <option value="industrie">Industrie</option>
                                <option value="commerce">Commerce</option>
                                <option value="autre">Autre</option>
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
                            placeholder="admin@entreprise.com"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                </div>

            </div>

            {{-- Configuration --}}
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8 mb-6">

                <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-6">
                    <i class="fa-solid fa-gear text-[#E2721B] text-sm"></i>
                    Configuration
                </h2>

                <div class="space-y-5">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Nom de l'administrateur
                        </label>
                        <input
                            type="text"
                            name="nom_administrateur"
                            placeholder="Prénom Nom"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Email professionnel <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            name="email_professionnel"
                            placeholder="admin@entreprise.com"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                </div>

            </div>

            {{-- Coordonnées administratives --}}
            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] p-8 mb-6">

                <h2 class="flex items-center gap-2 text-base font-semibold text-gray-900 mb-6">
                    <i class="fa-solid fa-location-dot text-[#E2721B] text-sm"></i>
                    Coordonnées Administratives
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Adresse du siège <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="adresse_siege"
                            placeholder="123 Rue de la République"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Téléphone professionnel <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="tel"
                            name="telephone_professionnel"
                            placeholder="+33 1 00 00 00 00"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Ville <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="ville"
                            placeholder="Paris"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Code Postal <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="code_postal"
                            placeholder="75001"
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

                <a href="{{ route('auth.success') }}"
                    type="submit"
                    class="px-6 py-2.5 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
                    Envoyer la demande
                </a>

            </div>

        </form>

    </div>

</body>

</html>

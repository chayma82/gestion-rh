<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande envoyée </title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-[#F9FAFB] min-h-screen flex items-center justify-center py-12">

    <div class="max-w-xl w-full px-4 text-center">

        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-orange-50 mb-6">
            <i class="fa-solid fa-envelope-circle-check text-3xl text-[#9A2A00]"></i>
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-[#9A2A00] tracking-tight mb-3">
            Demande envoyée avec succès !
        </h1>

        <p class="text-gray-600 text-sm leading-relaxed max-w-lg mx-auto">
            Votre demande de création de compte entreprise pour
            <span class="font-semibold text-gray-800">{{ $nomEntreprise ?? 'Lumina HRMS' }}</span>
            a été transmise à nos équipes. Nous reviendrons vers vous par e-mail sous 24 à 48 heures
            pour finaliser la configuration de votre environnement.
        </p>

        <a href="{{ route('auth.authi') }}"
            class="inline-block mt-8 px-6 py-3 rounded-lg bg-[#E2721B] hover:bg-[#D16212] text-white font-medium text-sm shadow-md shadow-orange-600/10 transition">
            Retour à l'accueil
        </a>

    </div>

</body>

</html>

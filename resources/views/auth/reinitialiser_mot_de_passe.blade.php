<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-[#F9FAFB] min-h-screen flex flex-col justify-between py-12">

    <div class="flex items-center justify-center flex-grow">

        <div class="w-full max-w-[440px] px-4">

            <div class="text-center mb-6">

                <div class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-[#9A2A00] text-white shadow-sm">
                    <i class="fa-solid fa-lock text-sm"></i>
                </div>

                <h1 class="text-3xl font-bold mt-3 text-gray-900 tracking-tight">
                    Nouveau mot de passe
                </h1>

                <p class="text-gray-500 mt-1 text-sm">
                    Choisissez un nouveau mot de passe pour votre compte
                </p>

            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] px-10 py-10">

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <div>
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Adresse e-mail
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $email) }}"
                            placeholder="nom@entreprise.com"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Nouveau mot de passe
                        </label>
                        <input
                            type="password"
                            name="password"
                            placeholder="8 caractères minimum"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Confirmer le mot de passe
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            placeholder="Confirmer"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                    </div>

                    <button
                        type="submit"
                        class="w-full mt-2 bg-[#E2721B] hover:bg-[#D16212] text-white py-3 rounded-xl font-medium shadow-md shadow-orange-600/10 transition text-sm">
                        Réinitialiser le mot de passe
                    </button>

                </form>

            </div>

        </div>

    </div>

    <div class="flex justify-center gap-4 text-xs text-gray-500 mt-auto">
        <a href="#" class="hover:underline">Confidentialité</a>
        <a href="#" class="hover:underline">Conditions</a>
    </div>

</body>

</html>

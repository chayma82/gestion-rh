<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion </title>

    @vite(['resources/css/app.css','resources/js/app.js'])

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="bg-[#F9FAFB] min-h-screen flex flex-col justify-between py-12">

    <div class="flex items-center justify-center flex-grow">

        <div class="w-full max-w-[440px] px-4">

            <div class="text-center mb-6">

                <div class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-[#9A2A00] text-white shadow-sm">
                    <i class="fa-solid fa-cloud text-sm"></i>
                </div>

                <h1 class="text-3xl font-bold mt-3 text-gray-900 tracking-tight">
                    Connexion
                </h1>

                <p class="text-gray-500 mt-1 text-sm">
                    Portail de Gestion du Personnel
                </p>

            </div>

            <div class="bg-white rounded-2xl border border-gray-200/60 shadow-[0_4px_25px_rgba(0,0,0,0.04)] px-10 py-10">
                @if (session('status'))
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif
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
                <form action="{{ route('login') }}" method="POST">


                    @csrf

                    <div class="mb-5">

                        <label class="block mb-2 text-sm font-medium text-gray-700">
                            Adresse e-mail
                        </label>

                        <div class="relative">
                            <input
                                type="email"
                                name="email"
                                placeholder="nom@com"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm">
                        </div>

                    </div>

                    <div>

                        <div class="flex justify-between mb-2 items-center">

                            <label class="text-sm font-medium text-gray-700">
                                Mot de passe
                            </label>

                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#E2721B] hover:underline">
                                Mot de passe oublié ?
                            </a>

                        </div>

                        <div class="relative">

                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="********"
                                class="w-full rounded-lg border border-gray-300 pl-11 pr-11 py-3 outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-gray-800 placeholder-gray-400 bg-white text-sm tracking-widest">

                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                                <i id="eye" class="fa-regular fa-eye text-sm"></i>
                            </button>

                        </div>

                    </div>

                    <div class="flex items-center mt-5">

                        <input
                            type="checkbox"
                            id="remember"
                            class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-500">

                        <label for="remember"
                            class="ml-2.5 text-sm font-medium text-gray-600 selection:bg-transparent cursor-pointer">
                            Se souvenir de cet appareil
                        </label>

                    </div>

                    <button
                        type="submit"
                        class="w-full mt-6 bg-[#E2721B] hover:bg-[#D16212] text-white py-3 rounded-xl font-medium shadow-md shadow-orange-600/10 transition text-sm">
                        Se connecter
                    </button>

                </form>

                <div class="border-t border-gray-100 mt-8 pt-6">

                    <p class="text-gray-600 text-sm">
                        Pas encore de compte entreprise ?
                    </p>

                    <a href="{{ route('auth.create') }}"
                        class="text-[#9A2A00] font-medium text-sm hover:underline mt-1 inline-block">
                        Créer un compte
                    </a>

                </div>

            </div>

        </div>

    </div>

    <div class="flex justify-center gap-4 text-xs text-gray-500 mt-auto">
        <a href="#" class="hover:underline">Confidentialité</a>
        <a href="#" class="hover:underline">Conditions</a>
    </div>

    <script>
    function togglePassword(){
        let password = document.getElementById("password");
        let eye = document.getElementById("eye");

        if(password.type === "password"){
            password.type = "text";
            password.style.letterSpacing = "normal"; // Évite le grand espacement des puces sur le texte clair
            eye.classList.remove("fa-eye");
            eye.classList.add("fa-eye-slash");
        } else {
            password.type = "password";
            password.style.letterSpacing = "0.1em";
            eye.classList.remove("fa-eye-slash");
            eye.classList.add("fa-eye");
        }
    }
    </script>

</body>

</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Compte activé</title>
</head>
<body style="font-family: Arial, sans-serif; background:#F9FAFB; padding:24px; color:#1f2937;">

    <div style="max-width:520px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; border:1px solid #e5e7eb; text-align:center;">

        <div style="width:64px; height:64px; background:#fff7ed; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px;">
            <span style="font-size:28px;">✅</span>
        </div>

        <h2 style="color:#9A2A00; margin-top:0;">Votre compte est activé !</h2>

        <p style="text-align:left;">
            Bonjour {{ $utilisateur->prenom }} {{ $utilisateur->nom }},
        </p>

        <p style="text-align:left;">
            Bonne nouvelle ! Votre compte entreprise a été validé par notre équipe.
            Vous pouvez dès à présent vous connecter à votre espace .
        </p>

        <div style="margin:28px 0;">
            <a href="{{ $loginUrl }}"
                style="display:inline-block; padding:12px 28px; background:#E2721B; color:#ffffff;
                    text-decoration:none; border-radius:8px; font-weight:bold; font-size:14px;">
                Accéder à mon espace
            </a>
        </div>

        <p style="text-align:left; font-size:14px; color:#4b5563;">
            Identifiant de connexion : <strong>{{ $utilisateur->email }}</strong>
        </p>

        <p style="text-align:left; margin-top:24px;">
            À bientôt,<br>L'équipe La Luna HRMS
        </p>

        <p style="margin-top:32px; font-size:12px; color:#9ca3af;">
            Si vous n'êtes pas à l'origine de cette inscription, veuillez ignorer cet email.
        </p>

    </div>

</body>
</html>

<!-- Placez ce fichier dans : resources/views/emails/reinitialiser_mot_de_passe.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="font-family: Arial, sans-serif; background:#F9FAFB; padding:24px; color:#1f2937;">

    <div style="max-width:520px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; border:1px solid #e5e7eb; text-align:center;">

        <div style="width:64px; height:64px; background:#fff7ed; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:16px;">
            <span style="font-size:28px;">🔑</span>
        </div>

        <h2 style="color:#9A2A00; margin-top:0;">Réinitialisation de mot de passe</h2>

        <p style="text-align:left;">
            Bonjour {{ $utilisateur->prenom }} {{ $utilisateur->nom }},
        </p>

        <p style="text-align:left;">
            Vous avez demandé à réinitialiser votre mot de passe sur La Luna HRMS.
            Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe.
            Ce lien est valable <strong>60 minutes</strong> et ne peut être utilisé qu'une seule fois.
        </p>

        <div style="margin:28px 0;">
            <a href="{{ $resetUrl }}"
               style="display:inline-block; padding:12px 28px; background:#E2721B; color:#ffffff;
                      text-decoration:none; border-radius:8px; font-weight:bold; font-size:14px;">
                Réinitialiser mon mot de passe
            </a>
        </div>

        <p style="text-align:left; font-size:13px; color:#6b7280;">
            Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :<br>
            <span style="word-break:break-all;">{{ $resetUrl }}</span>
        </p>

        <p style="text-align:left; margin-top:24px; font-size:13px; color:#6b7280;">
            Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email :
            votre mot de passe actuel restera inchangé.
        </p>

        <p style="text-align:left; margin-top:24px;">
            À bientôt,<br>L'équipe La Luna HRMS
        </p>

    </div>

</body>
</html>

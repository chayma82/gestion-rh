<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande envoyée</title>
</head>
<body style="margin:0; padding:0; background-color:#F9FAFB; font-family: Arial, Helvetica, sans-serif;">

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F9FAFB; padding:48px 16px;">
        <tr>
            <td align="center">

                <table role="presentation" width="100%" style="max-width:560px;" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" style="padding-bottom:24px;">
                            {{-- Icône (cercle orange) --}}
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="80" height="80" align="center" valign="middle"
                                        style="background-color:#FFF3E9; border-radius:50%; font-size:32px; color:#9A2A00;">
                                        ✉️
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding-bottom:12px;">
                            <h1 style="margin:0; font-size:24px; font-weight:bold; color:#9A2A00; letter-spacing:-0.02em;">
                                Demande envoyée avec succès !
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:0 20px 32px 20px;">
                            <p style="margin:0; font-size:14px; line-height:1.7; color:#4B5563;">
                                Bonjour {{ $utilisateur->prenom }},<br><br>
                                Votre demande de création de compte entreprise pour
                                <strong style="color:#1F2937;">{{ $tenant->nom }}</strong>
                                a bien été transmise à nos équipes. Nous reviendrons vers vous par e-mail
                                sous 24 à 48 heures pour finaliser la configuration de votre environnement.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>

<!-- Placez ce fichier dans : resources/views/emails/nouvelle_demande.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande d'inscription</title>
</head>
<body style="font-family: Arial, sans-serif; background:#F9FAFB; padding:24px; color:#1f2937;">

    <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; border:1px solid #e5e7eb;">

        <h2 style="color:#9A2A00; margin-top:0;">Nouvelle demande de compte entreprise</h2>

        <p>Bonjour,</p>

        <p>Une nouvelle entreprise vient de faire une demande d'inscription sur ..... Voici le détail complet de la demande :</p>

        {{-- ===================== ENTREPRISE ===================== --}}
        <h3 style="color:#E2721B; font-size:14px; text-transform:uppercase; letter-spacing:0.05em; margin:24px 0 8px; border-bottom:1px solid #f3f4f6; padding-bottom:8px;">
            Informations de l'entreprise
        </h3>

        <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
            <tr>
                <td style="padding:6px 0; width:180px; color:#6b7280; vertical-align:top;">Nom de l'entreprise</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $entreprise->nom }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Matricule fiscale</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $entreprise->num_fiscal }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Secteur d'activité</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $entreprise->secteur_activite }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Email entreprise</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $entreprise->email }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Téléphone entreprise</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $entreprise->telephone ?? '—' }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Adresse</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $entreprise->adresse }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Ville</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $entreprise->ville }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Code postal</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $entreprise->code_postal }}</td>
            </tr>
        </table>

        {{-- ===================== ADMINISTRATEUR ===================== --}}
        <h3 style="color:#E2721B; font-size:14px; text-transform:uppercase; letter-spacing:0.05em; margin:24px 0 8px; border-bottom:1px solid #f3f4f6; padding-bottom:8px;">
            Compte administrateur demandé
        </h3>

        <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
            <tr>
                <td style="padding:6px 0; width:180px; color:#6b7280; vertical-align:top;">Nom</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $utilisateur->nom }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Prénom</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $utilisateur->prenom }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Email professionnel</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $utilisateur->email }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Téléphone</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $utilisateur->telephone ?? '—' }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Statut du compte</td>
                <td style="padding:6px 0; font-weight:bold; color:#b45309;">En attente de validation</td>
            </tr>
        </table>

        {{-- ===================== TENANT / DEMANDE ===================== --}}
        <h3 style="color:#E2721B; font-size:14px; text-transform:uppercase; letter-spacing:0.05em; margin:24px 0 8px; border-bottom:1px solid #f3f4f6; padding-bottom:8px;">
            Détails de la demande
        </h3>

        <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
            <tr>
                <td style="padding:6px 0; width:180px; color:#6b7280; vertical-align:top;">Identifiant du tenant</td>
                <td style="padding:6px 0; font-weight:bold;">#{{ $tenant->id }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Catégorie attribuée</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $tenant->tenantCategorie->nom ?? '—' }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0; color:#6b7280; vertical-align:top;">Date de la demande</td>
                <td style="padding:6px 0; font-weight:bold;">{{ $tenant->date_creation->format('d/m/Y à H:i') }}</td>
            </tr>
        </table>

        <p style="margin-top:24px;">Merci de vous rendre sur le back-office pour examiner et valider (ou refuser) cette demande.</p>

        <p style="margin-top:32px; font-size:12px; color:#9ca3af;">
            Cet email a été généré automatiquement par la plateforme La Luna HRMS.
        </p>

    </div>

</body>
</html>

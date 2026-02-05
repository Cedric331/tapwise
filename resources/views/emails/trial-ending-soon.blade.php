<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fin de periode d'essai</title>
</head>
<body style="margin:0;padding:0;background-color:#f8fafc;font-family:Arial,sans-serif;color:#1f2937;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #f1f5f9;">
                    <tr>
                        <td style="padding:28px 32px;background:linear-gradient(90deg,#f59e0b,#92400e);color:#ffffff;">
                            <h1 style="margin:0;font-size:22px;">Tapwise</h1>
                            <p style="margin:8px 0 0;font-size:14px;opacity:0.9;">Votre periode d'essai se termine bientot</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 16px;font-size:16px;">
                                Bonjour {{ $user->name }},
                            </p>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;">
                                Plus que {{ $daysLeft }} jour{{ $daysLeft > 1 ? 's' : '' }} avant la fin de votre periode d'essai Tapwise.
                                Pour continuer a partager vos recommandations via le QR code, pensez a activer l'abonnement de vos bars.
                            </p>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.6;">
                                Connectez-vous a votre tableau de bord pour choisir les bars a activer.
                            </p>
                            <a href="{{ url('/dashboard') }}"
                               style="display:inline-block;background:#f59e0b;color:#ffffff;text-decoration:none;padding:12px 18px;border-radius:8px;font-weight:bold;">
                                Acceder au tableau de bord
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px;background:#f8fafc;font-size:12px;color:#6b7280;">
                            Vous recevez cet email car vous utilisez Tapwise.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>


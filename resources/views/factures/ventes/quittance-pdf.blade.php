<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quittance {{ $paiement->numero_quittance }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 24px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .header .left, .header .right {
            display: table-cell;
            vertical-align: top;
        }
        .header .right {
            text-align: right;
            color: #6b7280;
        }
        h2 { margin: 0 0 4px 0; font-size: 18px; }
        .subtitle { color: #6b7280; font-size: 11px; }

        table.infos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        table.infos td {
            width: 50%;
            padding: 6px 0;
            vertical-align: top;
        }
        .label { color: #6b7280; font-size: 10px; display: block; margin-bottom: 2px; }
        .value { font-weight: bold; }
        .montant { color: #16a34a; font-weight: bold; }

        .footer {
            border-top: 1px solid #eee;
            padding-top: 12px;
            text-align: right;
            font-size: 11px;
            color: #4b5563;
        }
        .footer .total { font-weight: bold; color: #111827; }
    </style>
</head>
<body>

    <div class="card">
        <div class="header">
            <div class="left">
                <h2>Quittance de paiement</h2>
                <div class="subtitle">N° {{ $paiement->numero_quittance }}</div>
            </div>
            <div class="right">
                {{ $paiement->date_paiement->format('d/m/Y') }}
            </div>
        </div>

        <table class="infos">
            <tr>
                <td>
                    <span class="label">Facture</span>
                    <span class="value">{{ $facture->numFacture }}</span>
                </td>
                <td>
                    <span class="label">Client</span>
                    <span class="value">{{ $facture->nom_client }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Méthode de paiement</span>
                    <span class="value">{{ ucfirst($paiement->methode_paiement) }}</span>
                </td>
                <td>
                    <span class="label">Montant reçu (ce paiement)</span>
                    <span class="montant">{{ number_format($paiement->montant, 2) }} DT</span>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>Montant total de la facture : <span class="total">{{ number_format($facture->montant_ttc, 2) }} DT</span></p>
            <p>Total payé à ce jour : <span class="total">{{ number_format($facture->montant_paye, 2) }} DT</span></p>
            @if($facture->montant_restant > 0 && $facture->date_echeance)
                <p>Échéance du reste : {{ $facture->date_echeance->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

</body>
</html>

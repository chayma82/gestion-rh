<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Facture {{ $facture->numFacture }}</title>
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
            margin-bottom: 20px;
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
            margin-bottom: 20px;
        }
        table.infos td {
            width: 50%;
            padding: 6px 0;
            vertical-align: top;
        }
        .label { color: #6b7280; font-size: 10px; display: block; margin-bottom: 2px; }
        .value { font-weight: bold; }

        table.lignes {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        table.lignes th {
            background: #fdf3e9;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            padding: 8px 6px;
            border-bottom: 1px solid #eee;
        }
        table.lignes td {
            padding: 8px 6px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 11px;
        }
        table.lignes td.montant { text-align: right; font-weight: bold; }

        .totaux {
            width: 100%;
        }
        .totaux td {
            text-align: right;
            padding: 3px 0;
            font-size: 11px;
        }
        .totaux .ttc {
            font-size: 14px;
            font-weight: bold;
            color: #E2721B;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="header">
            <div class="left">
                <h2>Facture de vente</h2>
                <div class="subtitle">N° {{ $facture->numFacture }}</div>
            </div>
            <div class="right">
                {{ $facture->dateEmissionFacture->format('d/m/Y') }}
            </div>
        </div>

        <table class="infos">
            <tr>
                <td>
                    <span class="label">Client</span>
                    <span class="value">{{ $facture->nom_client }}</span>
                </td>
                <td>
                    <span class="label">Date d'échéance</span>
                    <span class="value">{{ $facture->date_echeance?->format('d/m/Y') ?? '—' }}</span>
                </td>
            </tr>
        </table>

        <table class="lignes">
            <colgroup>
                <col style="width:12%">
                <col style="width:26%">
                <col style="width:9%">
                <col style="width:10%">
                <col style="width:15%">
                <col style="width:10%">
                <col style="width:18%">
            </colgroup>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Désignation</th>
                    <th>Qté</th>
                    <th>Unité</th>
                    <th>Prix unit.</th>
                    <th>TVA</th>
                    <th style="text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->details as $ligne)
                    <tr>
                        <td>{{ $ligne->reference ?? '—' }}</td>
                        <td>{{ $ligne->designation }}</td>
                        <td>{{ rtrim(rtrim(number_format($ligne->quantite, 2), '0'), '.') }}</td>
                        <td>{{ $ligne->unite ?? '—' }}</td>
                        <td>{{ number_format($ligne->prix_unitaire, 2) }} DT</td>
                        <td>{{ rtrim(rtrim(number_format($ligne->taux_tva, 2), '0'), '.') }}%</td>
                        <td class="montant">{{ number_format($ligne->montant_ligne, 2) }} DT</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totaux">
            <tr><td>Montant Hors Taxe (HT) : {{ number_format($facture->montant_ht, 2) }} DT</td></tr>
            <tr><td>Montant TVA : {{ number_format($facture->montant_tva, 2) }} DT</td></tr>
            <tr><td class="ttc">Montant TTC : {{ number_format($facture->montant_ttc, 2) }} DT</td></tr>
        </table>
    </div>

</body>
</html>

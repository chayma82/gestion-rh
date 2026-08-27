<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salaires - {{ $periode }}</title>
    <style>
        /* dompdf ne supporte qu'un sous-ensemble de CSS : on reste sur du
           CSS simple, pas de flexbox/grid. */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
        }

        .en-tete {
            border-bottom: 2px solid #E2721B;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .en-tete h1 {
            font-size: 18px;
            color: #E2721B;
            margin: 0 0 4px 0;
        }

        .en-tete p {
            margin: 0;
            color: #6b7280;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #FFF3E9;
            text-align: left;
            padding: 8px 10px;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #f3f4f6;
        }

        .montant-positif { color: #059669; font-weight: bold; }
        .montant-negatif { color: #dc2626; font-weight: bold; }
        .montant-net { font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
        }
        .badge-paye   { background-color: #ecfdf5; color: #047857; }
        .badge-attente{ background-color: #fff7ed; color: #c2410c; }
        .badge-annule { background-color: #f3f4f6; color: #6b7280; }

        .recap {
            margin-top: 20px;
            width: 260px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .recap p {
            margin: 4px 0;
            font-size: 11px;
        }

        .recap .total {
            font-size: 14px;
            font-weight: bold;
            color: #E2721B;
        }

        .pied {
            margin-top: 30px;
            font-size: 9px;
            color: #9ca3af;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="en-tete">
        <h1>Fiche de salaires — {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->translatedFormat('F Y') }}</h1>
        <p>{{ $nbPayes }} / {{ $nbTotal }} salaire(s) payé(s)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employé</th>
                <th>Salaire brut</th>
                <th>Primes</th>
                <th>Avances</th>
                <th>Net à payer</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salaires as $salaire)
                <tr>
                    <td>{{ $salaire->employe->nom_complet ?? ($salaire->employe->nom . ' ' . $salaire->employe->prenom) }}</td>
                    <td>{{ number_format($salaire->salaire_brut, 2) }} DT</td>
                    <td class="montant-positif">+{{ number_format($salaire->total_primes, 2) }} DT</td>
                    <td class="montant-negatif">-{{ number_format($salaire->total_avances, 2) }} DT</td>
                    <td class="montant-net">{{ number_format($salaire->salaire_net, 2) }} DT</td>
                    <td>
                        @php $badge = $salaire->statut_badge; @endphp
                        <span class="badge badge-{{ $salaire->statut === 'paye' ? 'paye' : ($salaire->statut === 'annule' ? 'annule' : 'attente') }}">
                            {{ $badge['label'] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="recap">
        <p>Nombre de salariés : {{ $nbTotal }}</p>
        <p>Salaires payés : {{ $nbPayes }} / {{ $nbTotal }}</p>
        <p class="total">Masse salariale : {{ number_format($masseSalariale, 2) }} DT</p>
    </div>

    <div class="pied">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>
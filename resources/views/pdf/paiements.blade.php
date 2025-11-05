<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }
    </style>
    <title>Rapport des paiements</title>
</head>

<body>
    <h2>Rapport des paiements</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>TD</th>
                <th>Enseignant</th>
                <th>Banque</th>
                <th>Référence</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paiements as $p)
                <tr>
                    <td>{{ $p->date_paiement }}</td>
                    <td>{{ $p->td->titre }}</td>
                    <td>{{ $p->td->enseignant->name }}</td>
                    <td>{{ $p->banque }}</td>
                    <td>{{ $p->reference }}</td>
                    <td style="text-align:right">{{ number_format($p->montant, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
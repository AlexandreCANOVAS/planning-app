<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comptabilité - {{ $nomEmploye }} - {{ $mois }} {{ $annee }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        h2 {
            color: #333;
            margin-top: 20px;
        }
        .header {
            margin-bottom: 30px;
        }
        .employee-info {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport de Comptabilité</h1>
        <div class="employee-info">
            <p><strong>Employé :</strong> {{ $nomEmploye }}</p>
            <p><strong>Période :</strong> {{ $mois }} {{ $annee }}</p>
        </div>
    </div>

    <h2>Détail des heures supplémentaires</h2>
    <table>
        <thead>
            <tr>
                <th>Semaine</th>
                <th>Heures Travaillées</th>
                <th>Heures Sup. (25%)</th>
                <th>Heures Sup. (50%)</th>
                <th>Total Heures Sup.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detailHeuresSupp as $detail)
            <tr>
                <td>{{ $detail['semaine'] }}</td>
                <td>{{ $detail['heures_travaillees'] }}</td>
                <td>{{ $detail['heures_25'] }}</td>
                <td>{{ $detail['heures_50'] }}</td>
                <td>{{ $detail['total_heures_supp'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">Aucune heure supplémentaire pour cette période</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Récapitulatif mensuel des heures supplémentaires</h2>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Nombre d'heures</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Heures supplémentaires (25%)</td>
                <td>{{ $recapMensuel['heures_25'] }}</td>
                <td>{{ number_format($recapMensuel['montant_25'], 2) }}€</td>
            </tr>
            <tr>
                <td>Heures supplémentaires (50%)</td>
                <td>{{ $recapMensuel['heures_50'] }}</td>
                <td>{{ number_format($recapMensuel['montant_50'], 2) }}€</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Total</strong></td>
                <td><strong>{{ number_format($recapMensuel['montant_total'], 2) }}€</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>

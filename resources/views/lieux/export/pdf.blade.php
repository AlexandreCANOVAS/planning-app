<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste des lieux de travail</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        h1 {
            color: #2d3748;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .date {
            color: #718096;
            font-size: 14px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f8fafc;
            text-align: left;
            padding: 10px;
            font-weight: bold;
            border-bottom: 2px solid #e2e8f0;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .color-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #718096;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Liste des lieux de travail</h1>
        <div class="date">Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Ville</th>
                <th>Code postal</th>
                <th>Téléphone</th>
                <th>Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lieux as $lieu)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <span class="color-indicator" style="background-color: {{ $lieu->couleur }};"></span>
                            <strong>{{ $lieu->nom }}</strong>
                        </div>
                    </td>
                    <td>{{ $lieu->adresse }}</td>
                    <td>{{ $lieu->ville }}</td>
                    <td>{{ $lieu->code_postal }}</td>
                    <td>{{ $lieu->telephone }}</td>
                    <td>{{ $lieu->contact_principal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ now()->format('Y') }} {{ Auth::user()->societe->nom }} - Tous droits réservés</p>
    </div>
</body>
</html>

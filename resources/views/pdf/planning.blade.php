<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planning - {{ $employee->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #4f46e5;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4f46e5;
        }
        .header-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8fafc;
            border-radius: 5px;
        }
        .header-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4f46e5;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
        .planning-date {
            font-weight: bold;
            color: #4f46e5;
        }
        .planning-hours {
            font-weight: bold;
        }
        .planning-location {
            color: #666;
        }
        .planning-total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Planning Hebdomadaire</h1>
    
    <div class="header-info">
        <p><strong>Employé :</strong> {{ $employee->name }}</p>
        <p><strong>Semaine du :</strong> {{ $weekStart->format('d/m/Y') }} au {{ $weekEnd->format('d/m/Y') }}</p>
        <p><strong>Généré le :</strong> {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Jour</th>
                <th>Période</th>
                <th>Horaires</th>
                <th>Lieu</th>
                <th>Heures</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalHours = 0;
                $daysOfWeek = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
            @endphp
            
            @foreach($plannings as $planning)
                @php
                    $date = \Carbon\Carbon::parse($planning->date);
                    $dayOfWeek = $daysOfWeek[$date->dayOfWeek - 1];
                    $totalHours += $planning->heures_travaillees;
                @endphp
                <tr>
                    <td class="planning-date">{{ $date->format('d/m/Y') }}</td>
                    <td>{{ $dayOfWeek }}</td>
                    <td>{{ ucfirst($planning->periode) }}</td>
                    <td class="planning-hours">{{ $planning->heure_debut }} - {{ $planning->heure_fin }}</td>
                    <td class="planning-location">{{ $planning->lieu ? $planning->lieu->nom : 'Non spécifié' }}</td>
                    <td>{{ $planning->heures_travaillees }} h</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="planning-total">
        Total des heures travaillées : {{ $totalHours }} h
    </div>
    
    <div class="footer">
        <p>Ce document est généré automatiquement par {{ config('app.name') }}.</p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
    </div>
</body>
</html>

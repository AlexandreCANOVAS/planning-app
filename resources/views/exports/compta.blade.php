<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Comptable - {{ $societe->nom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-info {
            margin-bottom: 30px;
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
        .total {
            margin-top: 20px;
            text-align: right;
            font-weight: bold;
        }
        .subtitle {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport Comptable</h1>
        <h2>{{ $societe->nom }}</h2>
        <p>Période : {{ \Carbon\Carbon::createFromFormat('Y-m', $mois)->format('F Y') }}</p>
        @if($employe_id && $plannings->isNotEmpty())
            <p class="subtitle">Employé : {{ $plannings->first()->employe->nom }} {{ $plannings->first()->employe->prenom }}</p>
        @endif
    </div>

    <div class="company-info">
        <p><strong>SIRET :</strong> {{ $societe->siret }}</p>
        <p><strong>Forme juridique :</strong> {{ $societe->forme_juridique }}</p>
        <p><strong>Adresse :</strong> {{ $societe->adresse }}</p>
    </div>

    <h3>Récapitulatif des heures travaillées</h3>
    
    @if($plannings->isEmpty())
        <p>Aucune donnée disponible pour cette période.</p>
    @else
        <table>
            <thead>
                <tr>
                    @if(!$employe_id)
                        <th>Employé</th>
                    @endif
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Heures</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalHeures = 0;
                    $totalParEmploye = [];
                @endphp

                @foreach($plannings as $planning)
                    @php
                        $debut = \Carbon\Carbon::parse($planning->heure_debut);
                        $fin = \Carbon\Carbon::parse($planning->heure_fin);
                        $heures = $debut->floatDiffInHours($fin);
                        $totalHeures += $heures;
                        
                        if(!$employe_id) {
                            if(!isset($totalParEmploye[$planning->employe->id])) {
                                $totalParEmploye[$planning->employe->id] = [
                                    'nom' => $planning->employe->nom . ' ' . $planning->employe->prenom,
                                    'heures' => 0
                                ];
                            }
                            $totalParEmploye[$planning->employe->id]['heures'] += $heures;
                        }
                    @endphp
                    <tr>
                        @if(!$employe_id)
                            <td>{{ $planning->employe->nom }} {{ $planning->employe->prenom }}</td>
                        @endif
                        <td>{{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }}</td>
                        <td>{{ $planning->lieu->nom }}</td>
                        <td>{{ $debut->format('H:i') }}</td>
                        <td>{{ $fin->format('H:i') }}</td>
                        <td>{{ number_format($heures, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if(!$employe_id)
            <h3>Récapitulatif par employé</h3>
            <table>
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Total des heures</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($totalParEmploye as $data)
                        <tr>
                            <td>{{ $data['nom'] }}</td>
                            <td>{{ number_format($data['heures'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="total">
            Total des heures : {{ number_format($totalHeures, 2) }}
        </div>
    @endif
</body>
</html>

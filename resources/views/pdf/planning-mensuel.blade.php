@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planning - {{ $mois }} {{ $annee }}</title>
    <style>
        @page {
            margin: 15px;
            size: A4 portrait;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            font-size: 14px;
            margin: 0 0 15px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #ccc;
            padding-bottom: 8px;
        }
        .info-box {
            background: #f8f9fa;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .info-box p {
            margin: 2px 0;
        }
        .week-section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .week-header {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #2c3e50;
            padding: 4px 0;
            border-bottom: 2px solid #3498db;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        th {
            background: #2c3e50;
            color: white;
            text-align: left;
            padding: 6px;
            font-size: 10px;
            font-weight: normal;
        }
        td {
            padding: 5px 6px;
            border-bottom: 1px solid #eee;
            height: 16px;
            vertical-align: middle;
        }
        tr:nth-child(even) td {
            background-color: #f8f9fa;
        }
        tr.repos td {
            color: #999;
            font-style: italic;
        }
        .total {
            text-align: right;
            font-weight: bold;
            padding: 6px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
        }
        .date-column { width: 10%; }
        .jour-column { width: 10%; }
        .lieu-column { width: 25%; }
        .debut-column { width: 10%; }
        .fin-column { width: 10%; }
        .debut-aprem-column { width: 10%; }
        .fin-aprem-column { width: 10%; }
        .heures-column { 
            width: 15%;
            text-align: right;
            padding-right: 10px !important;
        }
        .heures-composees {
            color: #3498db;
            font-size: 10px;
        }
        .heures-total {
            font-weight: bold;
            color: #2c3e50;
            font-size: 10px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <h1>Planning de {{ strtoupper($employe->nom) }} {{ ucfirst(strtolower($employe->prenom)) }}</h1>

    <div class="info-box">
        <p><strong>Employé :</strong> {{ $employe->nom }} {{ $employe->prenom }}</p>
        <p><strong>Période :</strong> {{ ucfirst($mois) }} {{ $annee }}</p>
    </div>

    @php
        $planningsParSemaine = $plannings->groupBy(function($planning) {
            return Carbon::parse($planning->date)->startOfWeek()->format('Y-m-d');
        });
    @endphp

    @foreach($planningsParSemaine as $debutSemaine => $planningsSemaine)
        @php
            $dateDebut = Carbon::parse($debutSemaine);
            $numeroSemaine = $dateDebut->weekOfYear;
        @endphp

        <div class="week-section">
            <div class="week-header">
                Semaine {{ $numeroSemaine }} ({{ $dateDebut->format('d/m') }} - {{ $dateDebut->copy()->endOfWeek()->format('d/m') }})
            </div>

            <table>
                <thead>
                    <tr>
                        <th class="jour-column">JOUR</th>
                        <th class="date-column">DATE</th>
                        <th class="lieu-column">LIEU</th>
                        <th class="debut-column">DÉBUT</th>
                        <th class="fin-column">FIN</th>
                        <th class="debut-aprem-column">DÉBUT</th>
                        <th class="fin-aprem-column">FIN</th>
                        <th class="heures-column">HEURES</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $currentDate = $dateDebut->copy();
                        $totalHeuresSemaine = 0;
                    @endphp
                    
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $planningMatin = $planningsSemaine->first(function($p) use ($currentDate) {
                                return Carbon::parse($p->date)->format('Y-m-d') === $currentDate->format('Y-m-d') 
                                    && ($p->periode === 'matin' || $p->periode === 'journee');
                            });
                            
                            $planningAprem = $planningsSemaine->first(function($p) use ($currentDate) {
                                return Carbon::parse($p->date)->format('Y-m-d') === $currentDate->format('Y-m-d') 
                                    && $p->periode === 'apres-midi';
                            });

                            $heuresJour = 0;
                        @endphp
                        
                        <tr class="{{ $planningMatin || $planningAprem ? '' : 'repos' }}">
                            <td>{{ ucfirst($currentDate->locale('fr')->isoFormat('dddd')) }}</td>
                            <td>{{ $currentDate->format('d/m/Y') }}</td>
                            
                            @if($planningMatin)
                                <td>{{ $planningMatin->lieu->nom }}</td>
                                <td>{{ Carbon::parse($planningMatin->heure_debut)->format('H:i') }}</td>
                                <td>{{ Carbon::parse($planningMatin->heure_fin)->format('H:i') }}</td>
                                <td>{{ $planningAprem ? Carbon::parse($planningAprem->heure_debut)->format('H:i') : '-' }}</td>
                                <td>{{ $planningAprem ? Carbon::parse($planningAprem->heure_fin)->format('H:i') : '-' }}</td>
                                <td class="heures-column">
                                    @php
                                        $heuresJour = $planningMatin->heures_travaillees;
                                        if($planningAprem) {
                                            $heuresJour += $planningAprem->heures_travaillees;
                                        }
                                        $totalHeuresSemaine += $heuresJour;
                                    @endphp
                                    @if($planningAprem)
                                        <span>{{ number_format($planningMatin->heures_travaillees, 2) }}</span>
                                        <span class="heures-composees">+{{ number_format($planningAprem->heures_travaillees, 2) }}</span>
                                        <span class="heures-total">({{ number_format($heuresJour, 2) }})</span>
                                    @else
                                        {{ number_format($heuresJour, 2) }}
                                    @endif
                                </td>
                            @else
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td class="heures-column">-</td>
                            @endif
                        </tr>
                        @php
                            $currentDate->addDay();
                        @endphp
                    @endfor
                    <tr>
                        <td colspan="7" class="total">Total semaine {{ $numeroSemaine }}</td>
                        <td class="heures-column total">{{ number_format($totalHeuresSemaine, 2) }}h</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <div class="total">
        <p>Total du mois : {{ number_format($totalHeures, 2) }}h</p>
    </div>

    <div class="footer">
        Document généré le {{ now()->locale('fr')->isoFormat('LL [à] HH:mm') }}
    </div>
</body>
</html>

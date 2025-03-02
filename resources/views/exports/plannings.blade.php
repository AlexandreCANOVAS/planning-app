@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Planning - {{ Carbon::parse($date_debut)->locale('fr')->isoFormat('MMMM YYYY') }}</title>
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
    @foreach($employes as $employe)
        <h1>Planning de {{ strtoupper($employe->nom) }} {{ ucfirst(strtolower($employe->prenom)) }}</h1>

        <div class="info-box">
            <p><strong>Employé :</strong> {{ $employe->nom }} {{ $employe->prenom }}</p>
            <p><strong>Période :</strong> {{ Carbon::parse($date_debut)->locale('fr')->isoFormat('MMMM YYYY') }}</p>
        </div>

        @php
            $planningsParSemaine = $employe->plannings
                ->filter(function($planning) use ($date_debut) {
                    $planningDate = Carbon::parse($planning->date);
                    $debutMois = Carbon::parse($date_debut);
                    return $planningDate->month === $debutMois->month && 
                           $planningDate->year === $debutMois->year;
                })
                ->sortBy('date')
                ->groupBy(function($planning) {
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
                                    return $p->date->format('Y-m-d') === $currentDate->format('Y-m-d') 
                                        && ($p->periode === 'matin' || $p->periode === 'journee');
                                });
                                
                                $planningAprem = $planningsSemaine->first(function($p) use ($currentDate) {
                                    return $p->date->format('Y-m-d') === $currentDate->format('Y-m-d') 
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
                                            <span>{{ App\Http\Controllers\PlanningController::convertToHHMM($planningMatin->heures_travaillees) }}</span>
                                            <span class="heures-composees">+{{ App\Http\Controllers\PlanningController::convertToHHMM($planningAprem->heures_travaillees) }}</span>
                                            <span class="heures-total">({{ App\Http\Controllers\PlanningController::convertToHHMM($heuresJour) }})</span>
                                        @else
                                            {{ App\Http\Controllers\PlanningController::convertToHHMM($heuresJour) }}
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
                            <td colspan="7" class="total">Total de la semaine</td>
                            <td class="heures-column total">{{ App\Http\Controllers\PlanningController::convertToHHMM($totalHeuresSemaine) }}</td>
                        </tr>
                    </tbody>
                </table>
                
            </div>
        @endforeach

        <div class="footer">
            Document généré le {{ now()->locale('fr')->isoFormat('LL [à] HH:mm') }}
        </div>
    @endforeach
</body>
</html>
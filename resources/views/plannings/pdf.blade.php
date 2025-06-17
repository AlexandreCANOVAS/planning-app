<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Planning - {{ $employe->nom }} {{ $employe->prenom }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 10px;
            font-size: 9px;
            line-height: 1.2;
            color: #2d3748;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 1px solid #4a5568;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1a202c;
            font-size: 18px;
            margin: 0 0 3px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            color: #4a5568;
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }
        .planning-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #ffffff;
        }
        .planning-table th {
            background-color: #2d3748;
            color: #ffffff;
            font-weight: bold;
            padding: 5px 4px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .planning-table td {
            padding: 4px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .modified {
            color: #dc2626 !important; /* rouge */
            font-weight: bold;
        }
        /* Suppression du style de fond rouge pour toute la ligne */
        /* tr.modified-row td {
            background-color: #fee2e2 !important; 
        } */
        .month-title {
            font-size: 16px;
            margin-bottom: 10px;
            text-align: center;
            color: #2d3748;
        }
        .total-section {
            margin-top: 15px;
            padding: 8px;
            background-color: #f8fafc;
            border-radius: 3px;
            border: 1px solid #e2e8f0;
        }
        .total-hours {
            font-size: 12px;
            color: #2d3748;
            font-weight: bold;
            text-align: right;
        }
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 8px;
            text-align: right;
        }
        .summary-box {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #edf2f7;
            border-radius: 3px;
        }
        .summary-box p {
            margin: 3px 0;
            color: #4a5568;
        }
        h3 {
            font-size: 12px !important;
            margin: 5px 0 !important;
            padding-bottom: 3px !important;
        }
        .planning-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-bottom: 15px;
        }
        .day-cell {
            border: 1px solid #e2e8f0;
            padding: 4px;
            min-height: 40px;
        }
        .day-header {
            font-weight: bold;
            text-align: center;
            background-color: #f8fafc;
            padding: 2px;
        }
        .weekend {
            background-color: #f7fafc !important;
            color: #718096;
        }
        .repos {
            color: #718096;
            font-style: italic;
        }
        .no-planning {
            color: #a0aec0;
        }
        .info-cell {
            color: #4a5568;
            font-weight: 500;
        }
        .time-cell {
            font-family: 'DejaVu Sans Mono', monospace;
            color: #2d3748;
        }
        .hours-cell {
            font-weight: bold;
            color: #2d3748;
        }
        .date-cell {
            white-space: nowrap;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Planning de {{ $employe->nom }} {{ $employe->prenom }}</h1>
        <h2>{{ $mois }} {{ $annee }}</h2>
    </div>

    @php
        \Log::info('Données dans la vue PDF', [
            'mois' => $mois,
            'annee' => $annee,
            'nombre_plannings' => $plannings->count(),
            'dates_plannings' => $plannings->keys()->toArray()
        ]);
    @endphp

    <div class="summary-box">
        <p><strong>Employé :</strong> {{ $employe->nom }} {{ $employe->prenom }}</p>
        <p><strong>Période :</strong> {{ $mois }} {{ $annee }}</p>
    </div>

    @php
        $currentDate = $startDate->copy();
        $joursRepos = 0;
        $joursTravailles = 0;
        $totalHeures = 0;
        $weeklyData = [];
        $currentWeek = null;
    @endphp

    @while($currentDate <= $endDate)
        @php
            $weekNum = $currentDate->format('W');
            if ($weekNum !== $currentWeek) {
                $currentWeek = $weekNum;
            }
            
            if (!isset($weeklyData[$weekNum])) {
                $weeklyData[$weekNum] = [
                    'start' => $currentDate->copy()->startOfWeek(),
                    'end' => $currentDate->copy()->endOfWeek(),
                    'days' => [],
                    'total_hours' => 0
                ];
            }

            $dateStr = $currentDate->format('Y-m-d');
            $planning = $plannings->get($dateStr);
            $isWeekend = $currentDate->isWeekend();
            $isRepos = $planning && $planning->lieu && $planning->lieu->nom === 'repos';

            if ($isRepos) {
                $joursRepos++;
            } elseif ($planning) {
                $joursTravailles++;
                if ($planning->heures_travaillees) {
                    $weeklyData[$weekNum]['total_hours'] += $planning->heures_travaillees;
                    $totalHeures += $planning->heures_travaillees;
                }
            }

            $weeklyData[$weekNum]['days'][] = [
                'date' => $currentDate->copy(),
                'planning' => $planning,
                'isWeekend' => $isWeekend,
                'isRepos' => $isRepos
            ];

            $currentDate->addDay();
        @endphp
    @endwhile

    @foreach($weeklyData as $weekNum => $week)
        <div style="margin-bottom: 15px;">
            <h3 style="color: #2d3748; font-size: 12px; margin-bottom: 5px; padding-bottom: 3px; border-bottom: 1px solid #e2e8f0;">
                Semaine {{ $weekNum }} ({{ $week['start']->format('d/m') }} - {{ $week['end']->format('d/m') }})
            </h3>
            
            <table class="planning-table" style="margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="width: 15%;">Jour</th>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 30%;">Lieu</th>
                        <th style="width: 13%;">Début</th>
                        <th style="width: 13%;">Fin</th>
                        <th style="width: 14%;">Heures</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($week['days'] as $day)
                        @php
                            $dateStr = $day['date']->format('Y-m-d');
                            $isModified = isset($modifiedPlannings) && $day['planning'] && in_array($day['planning']->id, $modifiedPlannings);
                            $isTemporary = isset($temporaryPlannings) && isset($temporaryPlannings[$dateStr]);
                        @endphp
                        <tr class="{{ $day['isWeekend'] ? 'weekend' : '' }} {{ $day['isRepos'] ? 'repos' : '' }}">
                            <td class="{{ ($isModified || $isTemporary) ? 'modified' : '' }}">{{ ucfirst($day['date']->locale('fr')->isoFormat('dddd')) }}</td>
                            <td class="date-cell {{ ($isModified || $isTemporary) ? 'modified' : '' }}">{{ $day['date']->format('d/m/Y') }}</td>
                            <td class="info-cell {{ ($isModified || $isTemporary) ? 'modified' : '' }}">
                                @if($isTemporary)
                                    <span class="modified">{{ isset($temporaryPlannings[$dateStr]['lieu_nom']) ? $temporaryPlannings[$dateStr]['lieu_nom'] : 'RH' }}</span>
                                @elseif($day['planning'] && $day['planning']->lieu)
                                    {{ $day['planning']->lieu->nom }}
                                @else
                                    <span class="no-planning">-</span>
                                @endif
                            </td>
                            <td class="time-cell {{ ($isModified || $isTemporary) ? 'modified' : '' }}">
                                @if($isTemporary)
                                    <span class="modified">{{ isset($temporaryPlannings[$dateStr]['horaires']) && isset($temporaryPlannings[$dateStr]['horaires']['debut']) ? $temporaryPlannings[$dateStr]['horaires']['debut'] : '00:00' }}</span>
                                @elseif($day['planning'] && !$day['isRepos'])
                                    {{ $day['planning']->heure_debut ? \Carbon\Carbon::parse($day['planning']->heure_debut)->format('H:i') : '-' }}
                                @else
                                    <span class="no-planning">-</span>
                                @endif
                            </td>
                            <td class="time-cell {{ ($isModified || $isTemporary) ? 'modified' : '' }}">
                                @if($isTemporary)
                                    <span class="modified">{{ isset($temporaryPlannings[$dateStr]['horaires']) && isset($temporaryPlannings[$dateStr]['horaires']['fin']) ? $temporaryPlannings[$dateStr]['horaires']['fin'] : '00:00' }}</span>
                                @elseif($day['planning'] && !$day['isRepos'])
                                    {{ $day['planning']->heure_fin ? \Carbon\Carbon::parse($day['planning']->heure_fin)->format('H:i') : '-' }}
                                @else
                                    <span class="no-planning">-</span>
                                @endif
                            </td>
                            <td class="hours-cell {{ ($isModified || $isTemporary) ? 'modified' : '' }}">
                                @if($isTemporary)
                                    <span class="modified">-</span>
                                @elseif($day['planning'] && !$day['isRepos'] && $day['planning']->heures_travaillees)
                                    {{ App\Http\Controllers\PlanningController::convertToHHMM($day['planning']->heures_travaillees) }}
                                @else
                                    <span class="no-planning">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="text-align: right; padding: 5px 10px; background-color: #f8fafc; border-radius: 4px; margin-top: 5px;">
                <p style="margin: 0; color: #2d3748; font-weight: bold;">
                    Total de la semaine : {{ App\Http\Controllers\PlanningController::convertToHHMM($week['total_hours']) }}
                </p>
            </div>
        </div>
    @endforeach

    <div class="total-section">
        <div class="total-hours">
            <p style="margin: 0;">Total des heures travaillées : <strong>{{ App\Http\Controllers\PlanningController::convertToHHMM($totalHeures) }}</strong></p>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #718096;">
                Jours travaillés : {{ $joursTravailles }} | Jours de repos : {{ $joursRepos }}
            </p>
        </div>
    </div>

    <div class="footer">
        Document généré le {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('DD MMMM YYYY [à] HH:mm') }}
    </div>
</body>
</html>

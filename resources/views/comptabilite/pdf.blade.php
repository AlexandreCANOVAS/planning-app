<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comptabilité - {{ $employe->nom }} {{ $employe->prenom }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #2d3748;
            font-size: 12px;
            line-height: 1.5;
            background-color: #ffffff;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #1a365d 0%, #2b6cb0 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-title {
            text-align: left;
        }
        .header h1 {
            font-size: 28px;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            color: white;
        }
        .header h2 {
            font-size: 16px;
            font-weight: normal;
            margin: 0;
            color: rgba(255,255,255,0.9);
        }
        .header-logo {
            width: 80px;
            height: auto;
        }
        .main-content {
            margin-top: 30px;
        }
        .info-section {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 25px;
        }
        .info-section-header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .info-section-icon {
            background-color: #3182ce;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        .info-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .info-box {
            flex: 1;
            min-width: 30%;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .info-box-title {
            font-size: 12px;
            font-weight: 600;
            color: #4a5568;
            margin: 0 0 5px 0;
        }
        .info-box-value {
            font-size: 16px;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }
        .info-label {
            font-weight: 600;
            color: #4a5568;
            display: inline-block;
            width: 40%;
        }
        .info-value {
            color: #2d3748;
            font-weight: 500;
            display: inline-block;
            width: 60%;
        }
        h2 {
            color: #2c5282;
            font-size: 20px;
            margin-top: 35px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #bee3f8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            position: relative;
        }
        h2:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 80px;
            height: 2px;
            background-color: #3182ce;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        th, td {
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        th {
            background: linear-gradient(to right, #f7fafc, #ebf8ff);
            font-weight: 600;
            color: #2d3748;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 2px solid #bee3f8;
        }
        tr:hover {
            background-color: #f7fafc;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f7fafc;
            font-weight: 600;
        }
        .total-hours {
            color: #2b6cb0;
        }
        .hours-25 {
            color: #3182ce;
        }
        .hours-50 {
            color: #2c5282;
        }
        .hours-night {
            color: #6b46c1;
        }
        .hours-sunday {
            color: #d69e2e;
        }
        .hours-holiday {
            color: #c53030;
        }
        .hours-absence {
            color: #718096;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #4a5568;
            border-top: 2px solid #e2e8f0;
            padding-top: 20px;
            background: linear-gradient(to right, #f7fafc, #ebf8ff, #f7fafc);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        .footer p {
            margin: 5px 0;
        }
        .footer .company {
            font-weight: 600;
            color: #2c5282;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        .footer .date {
            font-style: italic;
        }
        .page-break {
            page-break-after: always;
        }
        table {
            page-break-inside: avoid;
        }
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .summary-box {
            flex: 1;
            min-width: 120px;
            padding: 8px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .summary-box:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #3182ce;
        }
        .summary-box h3 {
            margin-top: 3px;
            margin-bottom: 5px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #2d3748;
        }
        .summary-box .value {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .summary-box .label {
            font-size: 9px;
            color: #4a5568;
            font-weight: 500;
        }
        .box-normal {
            background: linear-gradient(135deg, #ebf8ff 0%, #d6eaff 100%);
            border-left: 0;
        }
        .box-normal:before {
            background: linear-gradient(to right, #3182ce, #63b3ed);
        }
        .box-night {
            background: linear-gradient(135deg, #f0e6ff 0%, #e9d8fd 100%);
            border-left: 0;
        }
        .box-night:before {
            background: linear-gradient(to right, #6b46c1, #9f7aea);
        }
        .box-sunday {
            background: linear-gradient(135deg, #fef6e4 0%, #feebc8 100%);
            border-left: 0;
        }
        .box-sunday:before {
            background: linear-gradient(to right, #d69e2e, #ecc94b);
        }
        .box-holiday {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-left: 0;
        }
        .box-holiday:before {
            background: linear-gradient(to right, #c53030, #f56565);
        }
        .box-absence {
            background-color: #f3f4f6;
            border-left: 4px solid #718096;
        }
        .financial-summary {
            background-color: #f0fff4;
            border-left: 4px solid #38a169;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .financial-summary h3 {
            margin-top: 0;
            color: #2f855a;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .financial-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .financial-item {
            flex: 1;
            min-width: 150px;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .financial-item .label {
            font-size: 11px;
            color: #718096;
            margin-bottom: 5px;
        }
        .financial-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #2f855a;
        }
        .total-amount {
            font-size: 18px;
            font-weight: bold;
            color: #2f855a;
            text-align: right;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #9ae6b4;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .container {
                max-width: 100%;
                padding: 15px;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div class="header-title">
                    <h1>Rapport de comptabilité</h1>
                    <h2>{{ $mois }} {{ $annee }}</h2>
                </div>
                @if(isset($societe) && isset($societe->logo) && $societe->logo)
                    <img src="{{ public_path('storage/' . $societe->logo) }}" alt="Logo" class="header-logo">
                @endif
            </div>
        </div>
        
        <div class="main-content">
            <div class="info-section">
                <div class="info-section-header">
                    <div class="info-section-icon">i</div>
                    <h3 class="info-section-title">Informations générales</h3>
                </div>
                
                <div class="info-grid">
                    <div class="info-box">
                        <p class="info-box-title">EMPLOYÉ</p>
                        <p class="info-box-value">{{ $nomEmploye }}</p>
                        <p style="margin: 5px 0 0 0; font-size: 11px;">{{ $employe->statut ?? 'Agent de sécurité' }}</p>
                    </div>
                    
                    <div class="info-box">
                        <p class="info-box-title">PÉRIODE</p>
                        <p class="info-box-value">{{ $mois }} {{ $annee }}</p>
                    </div>
                    
                    <div class="info-box">
                        <p class="info-box-title">DOCUMENT</p>
                        <p class="info-box-value">Rapport comptable</p>
                        <p style="margin: 5px 0 0 0; font-size: 11px;">Généré le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="info-section">
                <div class="info-section-header">
                    <div class="info-section-icon">!</div>
                    <h3 class="info-section-title">Information importante</h3>
                </div>
                <p style="margin: 0; font-size: 12px; color: #2d3748; line-height: 1.5;">Les heures de nuit sont comptabilisées uniquement pour les heures travaillées entre 21h et 6h du matin. Les services marqués RH ou CP sont exclus du calcul.</p>
            </div>
        
        <!-- Récapitulatif visuel des heures -->
        <div class="info-section">
            <div class="info-section-header">
                <div class="info-section-icon" style="background-color: #4299e1;">H</div>
                <h3 class="info-section-title">Récapitulatif des heures</h3>
            </div>
            
            <!-- Section unique pour toutes les boîtes récapitulatives, avec style pour éviter les coupures -->
            <div style="page-break-inside: avoid;">
                <!-- Conteneur principal avec toutes les boîtes -->
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <!-- Ligne 1: Heures totales et supplémentaires -->
                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 2; background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #2c5282; text-transform: uppercase;">Heures totales</h3>
                            <div style="font-size: 24px; font-weight: bold; color: #2b6cb0;">{{ number_format($recapMensuel['total_heures'], 2) }}h</div>
                            <div style="font-size: 12px; color: #4a5568; margin-top: 5px;">Total des heures travaillées</div>
                        </div>
                        <div style="flex: 1; background: linear-gradient(135deg, #e6fffa 0%, #b2f5ea 100%); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #234e52; text-transform: uppercase;">Heures sup. 25%</h3>
                            <div style="font-size: 24px; font-weight: bold; color: #285e61;">{{ number_format($recapMensuel['heures_25'], 2) }}h</div>
                            <div style="font-size: 12px; color: #4a5568; margin-top: 5px;">36h à 43h</div>
                        </div>
                        <div style="flex: 1; background: linear-gradient(135deg, #feebcb 0%, #fbd38d 100%); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #744210; text-transform: uppercase;">Heures sup. 50%</h3>
                            <div style="font-size: 24px; font-weight: bold; color: #975a16;">{{ number_format($recapMensuel['heures_50'], 2) }}h</div>
                            <div style="font-size: 12px; color: #4a5568; margin-top: 5px;">44h et plus</div>
                        </div>
                    </div>
                    
                    <!-- Ligne 2: Heures spéciales -->
                    <div style="display: flex; gap: 15px;">
                        <div style="flex: 1; background: linear-gradient(135deg, #e9d8fd 0%, #d6bcfa 100%); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #44337a; text-transform: uppercase;">Heures de nuit</h3>
                            <div style="font-size: 24px; font-weight: bold; color: #553c9a;">{{ number_format($recapMensuel['heures_nuit'] ?? 0, 2) }}h</div>
                            <div style="font-size: 12px; color: #4a5568; margin-top: 5px;">21h à 6h</div>
                        </div>
                        <div style="flex: 1; background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #742a2a; text-transform: uppercase;">Heures dimanche</h3>
                            <div style="font-size: 24px; font-weight: bold; color: #9b2c2c;">{{ number_format($recapMensuel['heures_dimanche'] ?? 0, 2) }}h</div>
                            <div style="font-size: 12px; color: #4a5568; margin-top: 5px;">Majoration</div>
                        </div>
                        <div style="flex: 1; background: linear-gradient(135deg, #fefcbf 0%, #faf089 100%); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #744210; text-transform: uppercase;">Heures fériés</h3>
                            <div style="font-size: 24px; font-weight: bold; color: #975a16;">{{ number_format($recapMensuel['heures_jours_feries'] ?? 0, 2) }}h</div>
                            <div style="font-size: 12px; color: #4a5568; margin-top: 5px;">Majoration</div>
                        </div>
                        <div style="flex: 1; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%); border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #2d3748; text-transform: uppercase;">Absences</h3>
                            <div style="font-size: 24px; font-weight: bold; color: #4a5568;">{{ number_format($recapMensuel['absences'] ?? 0, 0) }}</div>
                            <div style="font-size: 12px; color: #4a5568; margin-top: 5px;">Jours</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- La section Récapitulatif financier a été supprimée selon la demande -->

    <!-- Ajout d'un saut de page avant le tableau des heures supplémentaires -->
    <div class="page-break"></div>
    
    <h2>Heures supplémentaires par semaine</h2>
    <div class="card" style="page-break-inside: avoid;">
        <table>
            <thead>
                <tr>
                    <th>PÉRIODE</th>
                    <th class="text-right">TOTAL HEURES</th>
                    <th class="text-right">HEURES SUP. 25%<br><span style="font-weight: normal; font-size: 11px;">(36H À 43H)</span></th>
                    <th class="text-right">HEURES SUP. 50%<br><span style="font-weight: normal; font-size: 11px;">(>44H)</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($detailHeuresSupp as $detail)
                <tr>
                    <td>{{ $detail['semaine'] }}</td>
                    <td class="text-right total-hours">{{ number_format($detail['heures_travaillees'], 2) }}</td>
                    <td class="text-right hours-25">{{ number_format($detail['heures_25'], 2) }}</td>
                    <td class="text-right hours-50">{{ number_format($detail['heures_50'], 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Aucune heure supplémentaire pour cette période</td>
                </tr>
                @endforelse
                
                <tr class="total-row">
                    <td><strong>Total du mois</strong></td>
                    <td class="text-right"><strong>{{ number_format($recapMensuel['total_heures'], 2) }}h</strong></td>
                    <td class="text-right hours-25"><strong>{{ number_format($recapMensuel['heures_25'], 2) }}h</strong></td>
                    <td class="text-right hours-50"><strong>{{ number_format($recapMensuel['heures_50'], 2) }}h</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Ajout d'un saut de page avant le tableau des heures spéciales -->
    <div class="page-break"></div>
    
    <h2>Heures spéciales et absences</h2>
    <div class="card" style="page-break-inside: avoid;">
        <table>
            <thead>
                <tr>
                    <th>PÉRIODE</th>
                    <th class="text-right">HEURES DE NUIT<br><span style="font-weight: normal; font-size: 11px;">(21H-06H)</span></th>
                    <th class="text-right">HEURES DIMANCHE</th>
                    <th class="text-right">HEURES JOURS FÉRIÉS</th>
                    <th class="text-right">ABSENCES<br><span style="font-weight: normal; font-size: 11px;">(JOURS)</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($detailHeuresSupp as $detail)
                <tr>
                    <td>{{ $detail['semaine'] }}</td>
                    <td class="text-right hours-night">{{ number_format($detail['heures_nuit'] ?? 0, 2) }}</td>
                    <td class="text-right hours-sunday">{{ number_format($detail['heures_dimanche'] ?? 0, 2) }}</td>
                    <td class="text-right hours-holiday">{{ number_format($detail['heures_jours_feries'] ?? 0, 2) }}</td>
                    <td class="text-right hours-absence">{{ number_format($detail['absences'] ?? 0, 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Aucune donnée pour cette période</td>
                </tr>
                @endforelse
                
                <tr class="total-row">
                    <td><strong>Total du mois</strong></td>
                    <td class="text-right hours-night"><strong>{{ number_format($recapMensuel['heures_nuit'] ?? 0, 2) }}h</strong></td>
                    <td class="text-right hours-sunday"><strong>{{ number_format($recapMensuel['heures_dimanche'] ?? 0, 2) }}h</strong></td>
                    <td class="text-right hours-holiday"><strong>{{ number_format($recapMensuel['heures_jours_feries'] ?? 0, 2) }}h</strong></td>
                    <td class="text-right hours-absence"><strong>{{ number_format($recapMensuel['absences'] ?? 0, 0) }} jour(s)</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Ajout d'un saut de page avant le récapitulatif des heures travaillées -->
    <div class="page-break"></div>
    
    <h2>Récapitulatif des heures travaillées</h2>
    @if(isset($plannings) && count($plannings) > 0)
        @php
            // Regrouper les plannings par semaine
            $planningsByWeek = [];
            foreach ($plannings as $planning) {
                $date = \Carbon\Carbon::parse($planning->date);
                $weekNumber = $date->weekOfYear;
                $weekStart = $date->copy()->startOfWeek()->format('d/m/Y');
                $weekEnd = $date->copy()->endOfWeek()->format('d/m/Y');
                $weekKey = "Semaine du $weekStart au $weekEnd";
                
                if (!isset($planningsByWeek[$weekKey])) {
                    $planningsByWeek[$weekKey] = [];
                }
                
                $planningsByWeek[$weekKey][] = $planning;
            }
            
            // Trier les semaines par date
            ksort($planningsByWeek);
        @endphp
        
        <div style="margin-bottom: 15px; background-color: #f8fafc; padding: 10px; border-radius: 6px; border-left: 4px solid #3182ce;">
            <p style="margin: 0; font-size: 13px; color: #4a5568;">
                <strong>Légende:</strong> 
                <span style="margin-left: 10px; padding: 2px 6px; background-color: #e6fffa; border-radius: 4px; font-size: 11px;">Jour normal</span>
                <span style="margin-left: 10px; padding: 2px 6px; background-color: #fef6e4; border-radius: 4px; font-size: 11px; color: #d69e2e;">Dimanche</span>
                <span style="margin-left: 10px; padding: 2px 6px; background-color: #fee2e2; border-radius: 4px; font-size: 11px; color: #c53030;">Jour férié</span>
                <span style="margin-left: 10px; padding: 2px 6px; background-color: #f0e6ff; border-radius: 4px; font-size: 11px; color: #6b46c1;">Heures de nuit</span>
            </p>
        </div>
        
        @foreach($planningsByWeek as $weekTitle => $weekPlannings)
            <div class="card" style="margin-bottom: 20px; page-break-inside: avoid;">
                <h3 style="background-color: #ebf8ff; padding: 12px 15px; margin: 0; font-size: 15px; color: #2c5282; border-bottom: 1px solid #bee3f8;">{{ $weekTitle }}</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 30%;">Lieu</th>
                            <th style="width: 15%; text-align: center;">Début</th>
                            <th style="width: 15%; text-align: center;">Fin</th>
                            <th style="width: 15%; text-align: center;">Heures</th>
                            <th style="width: 10%; text-align: center;">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($weekPlannings as $planning)
                        @php
                            $date = \Carbon\Carbon::parse($planning->date);
                            $isWeekend = $date->isWeekend();
                            $isSunday = $date->isSunday();
                            $isHoliday = isset($planning->est_ferie) && $planning->est_ferie;
                            $rowClass = '';
                            $typeLabel = '';
                            $typeStyle = '';
                            
                            if ($isHoliday) {
                                $rowClass = 'background-color: #fff5f5;';
                                $typeLabel = 'Férié';
                                $typeStyle = 'background-color: #fee2e2; color: #c53030; padding: 2px 6px; border-radius: 4px; font-size: 11px;';
                            } elseif ($isSunday) {
                                $rowClass = 'background-color: #fffbeb;';
                                $typeLabel = 'Dim';
                                $typeStyle = 'background-color: #fef6e4; color: #d69e2e; padding: 2px 6px; border-radius: 4px; font-size: 11px;';
                            } else {
                                $typeLabel = 'Normal';
                                $typeStyle = 'background-color: #e6fffa; color: #047481; padding: 2px 6px; border-radius: 4px; font-size: 11px;';
                            }
                            
                            // Vérifier si les heures sont de nuit (entre 21h et 6h)
                            if (!empty($planning->heure_debut) && !empty($planning->heure_fin) && isset($planning->lieu) && !in_array($planning->lieu->nom, ['RH', 'CP'])) {
                                $heureDebut = (int)\Carbon\Carbon::parse($planning->heure_debut)->format('H');
                                $heureFin = (int)\Carbon\Carbon::parse($planning->heure_fin)->format('H');
                                
                                // Si l'heure de fin est inférieure à l'heure de début, cela signifie que le service se termine le lendemain
                                if ($heureFin < $heureDebut) {
                                    $heureFin += 24;
                                }
                                
                                // Un service est considéré de nuit si:
                                // - Il commence avant 21h et finit après 21h
                                // - OU il commence après 21h et avant 6h
                                $isNight = ($heureDebut < 21 && $heureFin > 21) || ($heureDebut >= 21 || $heureDebut < 6);
                            } else {
                                $isNight = false;
                            }
                            
                            if ($isNight) {
                                $typeLabel = 'Nuit';
                                $typeStyle = 'background-color: #f0e6ff; color: #6b46c1; padding: 2px 6px; border-radius: 4px; font-size: 11px;';
                            }
                        @endphp
                        <tr style="{{ $rowClass }}">
                            <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0;">
                                {{ $date->format('d/m/Y') }}
                                <div style="font-size: 11px; color: #718096;">{{ $date->locale('fr')->isoFormat('dddd') }}</div>
                            </td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0;">{{ $planning->lieu->nom ?? 'Non défini' }}</td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; text-align: center;">{{ \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') }}</td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; text-align: center;">{{ \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') }}</td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; text-align: center; font-weight: 600;">{{ number_format($planning->heures_travaillees, 2) }}</td>
                            <td style="padding: 10px 15px; border-bottom: 1px solid #e2e8f0; text-align: center;">
                                <span style="{{ $typeStyle }}">{{ $typeLabel }}</span>
                            </td>
                        </tr>
                        @endforeach
                        
                        <!-- Calcul du total des heures de la semaine -->
                        @php
                            $totalHeuresSemaine = collect($weekPlannings)->sum('heures_travaillees');
                        @endphp
                        
                        <tr class="total-row">
                            <td colspan="4" style="padding: 12px 15px; text-align: right; font-weight: 600; background-color: #f7fafc;"><strong>Total de la semaine</strong></td>
                            <td style="padding: 12px 15px; text-align: center; font-weight: 700; background-color: #f7fafc; color: #2b6cb0;">{{ number_format($totalHeuresSemaine, 2) }}</td>
                            <td style="padding: 12px 15px; background-color: #f7fafc;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div class="card">
            <p style="text-align: center; padding: 20px;">Aucun planning pour cette période</p>
        </div>
    @endif
    


    <div class="footer">
        <p class="company">Vision Sécurité Privée</p>
        <p class="date">Document généré le {{ date('d/m/Y à H:i') }}</p>
        <p>SIRET: 85321467800033</p>
        <p style="margin-top: 10px; font-weight: 600; color: #4a5568;">Ce document est confidentiel et destiné uniquement à l'usage interne de l'entreprise.</p>
    </div>
    </div> <!-- Fermeture du container -->
</body>
</html>

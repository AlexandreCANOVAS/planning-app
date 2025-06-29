<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'activité</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        /* En-tête avec dégradé violet */
        .header {
            background: linear-gradient(135deg, #4c1d95 0%, #7e22ce 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        /* Éléments décoratifs */
        .header-circle {
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
            top: -60px;
            right: -60px;
        }
        .header-circle-2 {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.05);
            bottom: -40px;
            left: 30px;
        }
        .header-line {
            position: absolute;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
            bottom: 15px;
            left: 0;
        }
        .header h1 {
            font-size: 28px;
            margin: 0;
            position: relative;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            font-size: 16px;
            font-weight: 300;
            margin-top: 5px;
            opacity: 0.9;
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            position: relative;
            font-size: 14px;
        }
        .header-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: rgba(255,255,255,0.15);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        /* Cartes et sections */
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05), 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(to bottom, #4c1d95, #a855f7);
        }
        .card h2 {
            color: #4c1d95;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 12px;
        }
        .card h2 svg {
            margin-right: 10px;
            width: 20px;
            height: 20px;
        }
        
        /* Grille de statistiques */
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            justify-content: space-between;
        }
        .stat-card {
            flex: 1;
            min-width: 180px;
            background: linear-gradient(145deg, #ffffff, #f9fafb);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            border: 1px solid #f3f4f6;
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4c1d95, #a855f7);
            opacity: 0.7;
        }
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #4c1d95;
            margin-bottom: 5px;
            line-height: 1.2;
        }
        .stat-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }
        
        /* Icônes pour les statistiques */
        .stat-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0.15;
            width: 24px;
            height: 24px;
        }
        /* Tableaux */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 25px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        table th {
            background: linear-gradient(135deg, #4c1d95 0%, #7e22ce 100%);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            position: relative;
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }
        table th:first-child {
            border-top-left-radius: 8px;
        }
        table th:last-child {
            border-top-right-radius: 8px;
        }
        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            color: #4b5563;
            font-size: 13.5px;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        table tr:hover td {
            background-color: #f3f4f6;
        }
        /* Cellules de total */
        .total-row td {
            background-color: #f3f4f6;
            font-weight: 600;
            border-top: 2px solid #e5e7eb;
            color: #4c1d95;
        }
        /* Cellules avec pourcentage */
        .percent-cell {
            position: relative;
        }
        .percent-bar {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            background: linear-gradient(90deg, rgba(168, 85, 247, 0.1) 0%, rgba(168, 85, 247, 0.05) 100%);
            z-index: 0;
        }
        .percent-text {
            position: relative;
            z-index: 1;
        }
        /* Pied de page */
        .footer {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            margin-top: 40px;
            padding: 20px;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4c1d95, #a855f7, #4c1d95);
        }
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer-logo {
            font-weight: bold;
            color: #4c1d95;
        }
        .footer-separator {
            display: inline-block;
            margin: 0 10px;
            color: #d1d5db;
        }
        
        /* Éléments graphiques */
        .progress-container {
            margin: 15px 0;
            background-color: #f3f4f6;
            border-radius: 20px;
            height: 10px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4c1d95, #a855f7);
            border-radius: 20px;
        }
        
        /* Badges et indicateurs */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 5px;
        }
        .badge-purple {
            background-color: #ede9fe;
            color: #6d28d9;
        }
        .badge-blue {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .badge-green {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .badge-orange {
            background-color: #ffedd5;
            color: #ea580c;
        }
        .badge-red {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        /* Cartes d'information */
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .info-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #4c1d95;
            font-weight: 600;
            font-size: 14px;
        }
        .info-card-header svg {
            margin-right: 8px;
            width: 16px;
            height: 16px;
        }
        .info-card-body {
            color: #64748b;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <!-- En-tête du rapport -->
    <div class="header">
        <div class="header-circle"></div>
        <div class="header-circle-2"></div>
        <div class="header-line"></div>
        <div class="header-badge">{{ $dateDebut->format('F Y') }}</div>
        
        <h1>Rapport d'activité</h1>
        <div class="header-subtitle">Analyse détaillée de l'activité professionnelle</div>
        <p>Période : {{ $dateDebut->format('d/m/Y') }} - {{ $dateFin->format('d/m/Y') }}</p>
        
        <div class="header-info">
            <div>
                <strong>{{ $societe->nom }}</strong><br>
                {{ $societe->adresse }}<br>
                SIRET: {{ $societe->siret ?? 'Non renseigné' }}
            </div>
            <div>
                @if(isset($employe))
                    <strong>Employé:</strong> {{ $employe->nom }} {{ $employe->prenom }}<br>
                    <strong>Poste:</strong> {{ $employe->poste ?? 'Non renseigné' }}<br>
                @else
                    <strong>Rapport global</strong><br>
                    <strong>Employés actifs:</strong> {{ $stats['resume']['nb_employes_actifs'] }}<br>
                @endif
                <strong>Généré le:</strong> {{ now()->format('d/m/Y à H:i') }}
            </div>
        </div>
    </div>
    
    <!-- Résumé de l'activité -->
    <div class="card">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-4"></path></svg>
            Résumé de l'activité
        </h2>
        
        <div class="info-card">
            <div class="info-card-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                Période d'analyse
            </div>
            <div class="info-card-body">
                Ce rapport analyse l'activité du {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}, soit {{ $dateDebut->diffInDays($dateFin) + 1 }} jours, dont {{ $stats['resume']['jours_ouvrables'] }} jours ouvrables.
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <div class="stat-value">{{ number_format($stats['resume']['total_heures'], 1) }}</div>
                <div class="stat-label">Heures totales</div>
            </div>
            <div class="stat-card">
                <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <div class="stat-value">{{ $stats['resume']['nb_employes_actifs'] }}</div>
                <div class="stat-label">Employés actifs</div>
            </div>
            <div class="stat-card">
                <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                <div class="stat-value">{{ number_format($stats['resume']['taux_occupation'], 1) }}%</div>
                <div class="stat-label">Taux d'occupation</div>
                <div class="progress-container">
                    <div class="progress-bar" style="width: {{ min($stats['resume']['taux_occupation'], 100) }}%"></div>
                </div>
            </div>
            <div class="stat-card">
                <svg class="stat-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <div class="stat-value">{{ $stats['resume']['nb_lieux_utilises'] }}</div>
                <div class="stat-label">Lieux utilisés</div>
            </div>
        </div>
    </div>
    
    <!-- Répartition des heures -->
    <div class="card">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Répartition des heures
        </h2>
        
        <div class="info-card">
            <div class="info-card-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                Légende des types d'heures
            </div>
            <div class="info-card-body">
                <span class="badge badge-purple">Standard</span> Heures normales de travail (max 7h/jour)<br>
                <span class="badge badge-orange">Supplémentaires</span> Heures au-delà des 7h/jour<br>
                <span class="badge badge-blue">Nuit</span> Heures travaillées entre 21h et 6h<br>
                <span class="badge badge-green">Dimanche</span> Heures travaillées le dimanche<br>
                <span class="badge badge-red">Fériés</span> Heures travaillées les jours fériés
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Type d'heures</th>
                    <th>Total</th>
                    <th style="width: 40%;">Répartition</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <span class="badge badge-purple">Standard</span>
                        Heures standard
                    </td>
                    <td>{{ number_format($stats['resume']['heures_standard'], 1) }}h</td>
                    <td class="percent-cell">
                        @php $percent = ($stats['resume']['heures_standard'] / max($stats['resume']['total_heures'], 0.1)) * 100; @endphp
                        <div class="percent-bar" style="width: {{ $percent }}%"></div>
                        <div class="percent-text">{{ number_format($percent, 1) }}%</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="badge badge-orange">Supplémentaires</span>
                        Heures supplémentaires
                    </td>
                    <td>{{ number_format($stats['resume']['heures_supplementaires'], 1) }}h</td>
                    <td class="percent-cell">
                        @php $percent = ($stats['resume']['heures_supplementaires'] / max($stats['resume']['total_heures'], 0.1)) * 100; @endphp
                        <div class="percent-bar" style="width: {{ $percent }}%"></div>
                        <div class="percent-text">{{ number_format($percent, 1) }}%</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="badge badge-blue">Nuit</span>
                        Heures de nuit
                    </td>
                    <td>{{ number_format($stats['resume']['heures_nuit'], 1) }}h</td>
                    <td class="percent-cell">
                        @php $percent = ($stats['resume']['heures_nuit'] / max($stats['resume']['total_heures'], 0.1)) * 100; @endphp
                        <div class="percent-bar" style="width: {{ $percent }}%"></div>
                        <div class="percent-text">{{ number_format($percent, 1) }}%</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="badge badge-green">Dimanche</span>
                        Heures dimanche
                    </td>
                    <td>{{ number_format($stats['resume']['heures_dimanche'], 1) }}h</td>
                    <td class="percent-cell">
                        @php $percent = ($stats['resume']['heures_dimanche'] / max($stats['resume']['total_heures'], 0.1)) * 100; @endphp
                        <div class="percent-bar" style="width: {{ $percent }}%"></div>
                        <div class="percent-text">{{ number_format($percent, 1) }}%</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="badge badge-red">Fériés</span>
                        Heures jours fériés
                    </td>
                    <td>{{ number_format($stats['resume']['heures_feries'], 1) }}h</td>
                    <td class="percent-cell">
                        @php $percent = ($stats['resume']['heures_feries'] / max($stats['resume']['total_heures'], 0.1)) * 100; @endphp
                        <div class="percent-bar" style="width: {{ $percent }}%"></div>
                        <div class="percent-text">{{ number_format($percent, 1) }}%</div>
                    </td>
                </tr>
                <tr class="total-row">
                    <td>Total des heures</td>
                    <td>{{ number_format($stats['resume']['total_heures'], 1) }}h</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Répartition par jour de la semaine -->
    <div class="card">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            Répartition par jour de la semaine
        </h2>
        
        <div class="info-card">
            <div class="info-card-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-4"></path></svg>
                Analyse de la charge de travail hebdomadaire
            </div>
            <div class="info-card-body">
                Cette répartition permet d'identifier les jours de la semaine avec la plus forte activité et d'optimiser la planification des ressources.
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Jour</th>
                    <th>Heures</th>
                    <th style="width: 40%;">Répartition</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $jours_fr = [
                        'Monday' => 'Lundi',
                        'Tuesday' => 'Mardi',
                        'Wednesday' => 'Mercredi',
                        'Thursday' => 'Jeudi',
                        'Friday' => 'Vendredi',
                        'Saturday' => 'Samedi',
                        'Sunday' => 'Dimanche'
                    ];
                    $couleurs = [
                        'Monday' => '#4c1d95',
                        'Tuesday' => '#5b21b6',
                        'Wednesday' => '#6d28d9',
                        'Thursday' => '#7c3aed',
                        'Friday' => '#8b5cf6',
                        'Saturday' => '#a78bfa',
                        'Sunday' => '#c4b5fd'
                    ];
                @endphp
                
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $jour_en)
                    @php 
                        $jour_fr = $jours_fr[$jour_en];
                        $heures = $stats['par_jour_semaine'][$jour_fr] ?? 0;
                        $percent = ($heures / max($stats['resume']['total_heures'], 0.1)) * 100;
                        $couleur = $couleurs[$jour_en];
                        $est_weekend = in_array($jour_en, ['Saturday', 'Sunday']);
                    @endphp
                    <tr>
                        <td>
                            @if($est_weekend)
                                <span class="badge badge-purple">{{ $jour_fr }}</span>
                            @else
                                {{ $jour_fr }}
                            @endif
                        </td>
                        <td>{{ number_format($heures, 1) }}h</td>
                        <td class="percent-cell">
                            <div class="percent-bar" style="width: {{ $percent }}%; background: linear-gradient(90deg, {{ $couleur }}20, {{ $couleur }}10);"></div>
                            <div class="percent-text">{{ number_format($percent, 1) }}%</div>
                        </td>
                    </tr>
                @endforeach
                
                <tr class="total-row">
                    <td>Total</td>
                    <td>{{ number_format($stats['resume']['total_heures'], 1) }}h</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    @if(count($stats['par_employe']) > 0)
    <!-- Répartition par employé -->
    <div class="card">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            Répartition par employé
        </h2>
        
        <div class="info-card">
            <div class="info-card-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                Analyse de la charge de travail par employé
            </div>
            <div class="info-card-body">
                Cette répartition permet d'identifier la contribution de chaque employé à l'activité globale et d'équilibrer les charges de travail.
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Heures</th>
                    <th style="width: 40%;">Répartition</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Trier les employés par nombre d'heures décroissant
                    $employes_tries = collect($stats['par_employe'])->sortByDesc('heures')->values()->all();
                @endphp
                
                @foreach($employes_tries as $index => $employeData)
                @php
                    $percent = ($employeData['heures'] / max($stats['resume']['total_heures'], 0.1)) * 100;
                    $couleurs = ['#4c1d95', '#5b21b6', '#6d28d9', '#7c3aed', '#8b5cf6', '#a78bfa', '#c4b5fd'];
                    $couleur = $couleurs[$index % count($couleurs)];
                @endphp
                <tr>
                    <td>
                        @if($index < 3)
                            <span class="badge badge-purple">{{ $employeData['nom'] }}</span>
                        @else
                            {{ $employeData['nom'] }}
                        @endif
                    </td>
                    <td>{{ number_format($employeData['heures'], 1) }}h</td>
                    <td class="percent-cell">
                        <div class="percent-bar" style="width: {{ $percent }}%; background: linear-gradient(90deg, {{ $couleur }}20, {{ $couleur }}10);"></div>
                        <div class="percent-text">{{ number_format($percent, 1) }}%</div>
                    </td>
                </tr>
                @endforeach
                
                <tr class="total-row">
                    <td>Total ({{ count($stats['par_employe']) }} employés)</td>
                    <td>{{ number_format($stats['resume']['total_heures'], 1) }}h</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
    
    @if(count($stats['par_lieu']) > 0)
    <!-- Répartition par lieu -->
    <div class="card">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            Répartition par lieu
        </h2>
        
        <div class="info-card">
            <div class="info-card-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                Analyse de l'activité par lieu de travail
            </div>
            <div class="info-card-body">
                Cette répartition permet d'identifier les lieux les plus actifs et d'optimiser la gestion des ressources par site.
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Lieu</th>
                    <th>Heures</th>
                    <th style="width: 40%;">Répartition</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Trier les lieux par nombre d'heures décroissant
                    $lieux_tries = collect($stats['par_lieu'])->sortByDesc('heures')->values()->all();
                @endphp
                
                @foreach($lieux_tries as $index => $lieuData)
                @php
                    $percent = ($lieuData['heures'] / max($stats['resume']['total_heures'], 0.1)) * 100;
                    $couleurs = ['#4c1d95', '#5b21b6', '#6d28d9', '#7c3aed', '#8b5cf6', '#a78bfa', '#c4b5fd'];
                    $couleur = $couleurs[$index % count($couleurs)];
                @endphp
                <tr>
                    <td>
                        @if($index < 3)
                            <span class="badge badge-blue">{{ $lieuData['nom'] }}</span>
                        @else
                            {{ $lieuData['nom'] }}
                        @endif
                    </td>
                    <td>{{ number_format($lieuData['heures'], 1) }}h</td>
                    <td class="percent-cell">
                        <div class="percent-bar" style="width: {{ $percent }}%; background: linear-gradient(90deg, {{ $couleur }}20, {{ $couleur }}10);"></div>
                        <div class="percent-text">{{ number_format($percent, 1) }}%</div>
                    </td>
                </tr>
                @endforeach
                
                <tr class="total-row">
                    <td>Total ({{ count($stats['par_lieu']) }} lieux)</td>
                    <td>{{ number_format($stats['resume']['total_heures'], 1) }}h</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif
    
    <!-- Pied de page -->
    <div class="footer">
        <div class="footer-content">
            <div>
                <span class="footer-logo">{{ $societe->nom }}</span>
                <span class="footer-separator">|</span>
                <span>SIRET: {{ $societe->siret ?? 'Non renseigné' }}</span>
            </div>
            <div>
                <span>Rapport d'activité</span>
                <span class="footer-separator">|</span>
                <span>{{ $dateDebut->format('F Y') }}</span>
            </div>
            <div>
                <span>Généré le {{ now()->format('d/m/Y à H:i') }}</span>
                <span class="footer-separator">|</span>
                <span>© {{ now()->format('Y') }}</span>
            </div>
        </div>
        <div style="margin-top: 15px; font-size: 10px; opacity: 0.7;">
            Ce document est confidentiel et destiné à un usage interne. Les données présentées sont calculées automatiquement à partir des plannings enregistrés dans le système.
        </div>
    </div>
    
    <!-- Signature numérique -->
    <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #9ca3af;">
        Document généré automatiquement - Ne nécessite pas de signature
    </div>
</body>
</html>

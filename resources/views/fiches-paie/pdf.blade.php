<!DOCTYPE html>
<html lang="fr">
@php
    $dateParts = explode('-', $fichePaie->mois);
    $annee = $dateParts[0];
    $mois = intval($dateParts[1]);
    $moisNom = \Carbon\Carbon::create()->month($mois)->locale('fr_FR')->monthName;
    
    // Calcul de la période (du 01/MM/YYYY au 31/MM/YYYY)
    $debutMois = "01/{$mois}/{$annee}";
    $finMois = date('t', strtotime("{$annee}-{$mois}-01")) . "/{$mois}/{$annee}";
    $periodeComplete = "Période de paie : du {$debutMois} au {$finMois}";
    
    // Calcul du net à payer
    $netAPayer = $fichePaie->salaire_net_a_payer ?? $fichePaie->salaire_net;
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de paie - {{ $fichePaie->employe->nom }} {{ $fichePaie->employe->prenom }}</title>
    <style>
        @page {
            margin: 5mm;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .page {
            width: 100%;
            padding: 1px;
            box-sizing: border-box;
        }
        
        .header {
            position: relative;
            background: linear-gradient(to right, #4c1d95, #7e22ce);
            margin-bottom: 2px;
            border-radius: 2px;
            color: white;
            padding: 2px;
        }
        
        .logo {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 50px;
            height: auto;
            background: white;
            padding: 2px;
            border-radius: 3px;
        }
        
        .company-info {
            text-align: right;
            font-size: 8px;
            color: white;
            margin-left: 55px;
        }
        
        .document-title {
            text-align: center;
            margin: 5px 0 3px;
            font-size: 12px;
            font-weight: bold;
            color: #4c1d95;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .bulletin-info {
            text-align: center;
            font-size: 9px;
            margin-bottom: 5px;
            color: #666;
            padding: 3px;
            background-color: #f3f4f6;
            border-radius: 3px;
        }
        
        .info-title {
            font-size: 9px;
            font-weight: bold;
            color: white;
            background: linear-gradient(to right, #4c1d95, #7e22ce);
            width: 100%;
            border-collapse: collapse;
            padding: 3px 5px;
            border-radius: 3px;
            margin-bottom: 3px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        
        .info-table td {
            padding: 2px;
            vertical-align: top;
        }
        
        .info-table p {
            margin: 0;
            line-height: 1.2;
        }
        
        .employee-info {
            width: 100%;
            padding: 0;
            margin-bottom: 8px;
        }
        
        .convention {
            margin: 3px 0;
            padding: 3px;
            background-color: #f3e8ff;
            border-radius: 3px;
            font-size: 8px;
            border-left: 2px solid #7e22ce;
        }
        
        .convention p {
            margin: 0;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        /* Tables de rémunération */
        .remuneration-table {
            margin: 3px 0 5px;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 3px;
        }
        
        .main-table th {
            background: linear-gradient(to right, #4c1d95, #7e22ce);
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 3px;
            font-size: 8px;
            border: 1px solid #ddd;
        }
        
        .main-table td {
            padding: 2px;
            border: 1px solid #ddd;
            text-align: right;
        }
        
        .main-table td:first-child {
            text-align: left;
            font-size: 8px;
            width: 30%;
        }
        
        .total-row {
            background-color: #f3e8ff;
            font-weight: bold;
        }
        
        .total-row td {
            border-top: 1px solid #7e22ce;
            border-bottom: 1px solid #7e22ce;
        }
        
        .category-header {
            font-weight: bold;
            color: #4c1d95;
            background-color: #f3e8ff;
        }
        
        /* Section titre */
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: white;
            margin: 5px 0 2px;
            padding: 3px 5px;
            background: linear-gradient(to right, #4c1d95, #7e22ce);
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Net à payer */
        .net-pay-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 8px 0;
        }
        
        .net-pay-box {
            width: 24%;
            padding: 5px;
            background-color: #f3e8ff;
            border-radius: 4px;
            text-align: center;
            border-left: 3px solid #7e22ce;
        }
        
        .net-pay-box.highlight {
            background-color: #7e22ce;
            color: white;
            border-left: 3px solid #4c1d95;
        }
        
        .net-pay-title {
            font-size: 9px;
            margin-bottom: 3px;
            font-weight: bold;
        }
        
        .highlight .net-pay-title {
            color: white;
        }
        
        .net-pay-value {
            font-size: 12px;
            font-weight: bold;
        }
        
        /* Cumuls */
        .cumul-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 5px 0;
            background-color: #f9f5ff;
            padding: 5px;
            border-radius: 4px;
        }
        
        .cumul-box {
            width: 24%;
            text-align: center;
            padding: 3px;
        }
        
        .cumul-label {
            font-size: 9px;
            color: #4c1d95;
            font-weight: bold;
        }
        
        .cumul-value {
            font-size: 10px;
            font-weight: bold;
        }
        
        /* Message légal */
        .legal-message {
            margin: 5px 0;
            padding: 5px;
            background-color: #f9f5ff;
            border-radius: 4px;
            font-size: 8px;
            color: #6b21a8;
            border-left: 3px solid #7e22ce;
        }
        
        .legal-message p {
            margin: 0;
        }
        
        .footer {
            margin-top: 8px;
            padding: 5px;
            background-color: #f9f5ff;
            border-radius: 4px;
            font-size: 8px;
            color: #6b21a8;
        }
        
        .signature-line {
            width: 100px;
            height: 2px;
            background-color: #4c1d95;
            margin: 0 0 3px auto;
        }
        
        .signature-label {
            font-size: 8px;
            text-align: center;
            color: #4c1d95;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            margin-left: 5px;
            color: white;
        }
        
        .status-draft {
            background-color: #7e22ce;
            color: white;
        }
        
        .status-pending {
            background-color: #4c1d95;
            color: white;
        }
        
        .status-published {
            background-color: #a855f7;
            color: white;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- En-tête -->
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 100%;">
            </div>
            <div class="company-info">
                <p><strong>{{ $fichePaie->societe->nom ?? 'SECURITY PLUS' }}</strong> | SIRET: {{ $fichePaie->societe->siret ?? '123 568 941 00056' }} | APE: {{ $fichePaie->societe->code_ape ?? '8010Z' }}</p>
                <p>{{ $fichePaie->societe->adresse ?? '12 rue de la Sécurité' }}, {{ $fichePaie->societe->code_postal ?? '31000' }} {{ $fichePaie->societe->ville ?? 'TOULOUSE' }} | Tél: {{ $fichePaie->societe->telephone ?? '05.61.22.33.44' }}</p>
            </div>
        </div>
        
        <!-- Titre du document -->
        <div class="document-title">BULLETIN DE PAIE - Période du {{ \Carbon\Carbon::parse($fichePaie->date_debut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($fichePaie->date_fin)->format('d/m/Y') }}</div>
        
        <div class="bulletin-info">
            Convention: {{ $fichePaie->convention_collective ?? 'Prévention et sécurité' }}
            @if($fichePaie->statut !== 'published')
            <span class="status-badge status-{{ $fichePaie->statut }}">
                {{ $fichePaie->statut === 'draft' ? 'BROUILLON' : 'EN ATTENTE DE VALIDATION' }}
            </span>
            @endif
        </div>
        
        <div class="clearfix">
            <div class="employee-info">
                <div class="info-title">INFORMATIONS GÉNÉRALES</div>
                <table class="info-table">
                    <tbody>
                    <tr>
                        <td width="50%">
                            <p><strong>{{ $fichePaie->employe->nom }} {{ $fichePaie->employe->prenom }}</strong> | Matricule: {{ $fichePaie->matricule ?? '000036' }}</p>
                            <p>Emploi: {{ $fichePaie->emploi ?? 'Agent de sécurité' }} | Coef: {{ $fichePaie->coefficient ?? '120' }} | H. contract.: {{ $fichePaie->heures_contractuelles ?? '151.67' }}h</p>
                        </td>
                        <td width="50%">
                            <p>{{ $fichePaie->employe->adresse }}, {{ $fichePaie->employe->code_postal ?? '31100' }} {{ $fichePaie->employe->ville ?? 'TOULOUSE' }}</p>
                            <p>Niveau: {{ $fichePaie->niveau ?? 'Agent d\'exploitation' }} | Échelon: {{ $fichePaie->echelon ?? 'Employé qualifié' }}</p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="remuneration-table">
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Base</th>
                        <th>Tx</th>
                        <th>R.Sal</th>
                        <th>Base</th>
                        <th>Tx</th>
                        <th>R.Pat</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Catégorie Salaire Brut -->
                    <tr class="category-header">
                        <td>Salaire de base</td>
                        <td>{{ number_format($fichePaie->salaire_base, 2, '.', '') }}</td>
                        <td>{{ number_format(12.6079, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->montant_heures_normales ?? $fichePaie->salaire_base, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <!-- Prime d'ancienneté -->
                    <tr>
                        <td>Prime d'ancienneté</td>
                        <td>{{ number_format($fichePaie->prime_anciennete ?? 1912.24, 2, '.', '') }}</td>
                        <td>2.0000</td>
                        <td>{{ number_format($fichePaie->prime_anciennete ?? 38.24, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <!-- Congés payés -->
                    <tr>
                        <td>Congés payés 11 jours du 05/05/2025 au 18/05/2025</td>
                        <td>{{ number_format($fichePaie->conges_payes ?? 11.00, 2, '.', '') }}</td>
                        <td>{{ number_format(75.0185, 4, '.', '') }}</td>
                        <td>{{ number_format(-825.20, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <!-- Maintien absences CP -->
                    <tr>
                        <td>Maintien absences CP</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($fichePaie->absences ?? 892.65, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <!-- Heures supplémentaires -->
                    <tr>
                        <td>Heures supplémentaires 25%</td>
                        <td>{{ number_format($fichePaie->heures_sup_25 ?? 8.50, 2, '.', '') }}</td>
                        <td>{{ number_format(15.7599, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->montant_heures_sup_25 ?? 133.96, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    

                    
                    <!-- Heures de nuit -->
                    <tr>
                        <td>Heures nuit 10%</td>
                        <td>{{ number_format($fichePaie->heures_nuit ?? 9.00, 2, '.', '') }}</td>
                        <td>{{ number_format(1.2608, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->montant_heures_nuit ?? 11.35, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <!-- Heures dimanche -->
                    <tr>
                        <td>Heures dimanche 10%</td>
                        <td>{{ number_format($fichePaie->heures_dimanche ?? 5.00, 2, '.', '') }}</td>
                        <td>{{ number_format(1.2608, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->montant_heures_dimanche ?? 6.30, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <!-- Prime d'habillage -->
                    <tr>
                        <td>Prime d'habillage</td>
                        <td>{{ number_format($fichePaie->prime_habillage ?? 90.50, 2, '.', '') }}</td>
                        <td>{{ number_format(0.1310, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->prime_habillage ?? 11.86, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <!-- Heures ferie J 100 % -->
                    <tr>
                        <td>Heures ferie J 100 %</td>
                        <td>{{ number_format($fichePaie->heures_ferie ?? 12.00, 2, '.', '') }}</td>
                        <td>{{ number_format(12.6079, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->montant_heures_ferie ?? 151.29, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    
                    <!-- Salaire Brut -->
                    <tr class="total-row">
                        <td>Salaire Brut</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section-title">SANTÉ</div>
        <div class="remuneration-table">
            <table class="main-table">
                <thead>
                    <tr>
                        <th>Libellé</th>
                        <th>Base</th>
                        <th>Tx</th>
                        <th>R.Sal</th>
                        <th>Base</th>
                        <th>Tx</th>
                        <th>R.Pat</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Sécurité sociale-maladie -->
                    <tr>
                        <td>Sécurité sociale-maladie, maternité, invalidité-décès</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(7.0000, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->securite_sociale_maladie ?? 163.29, 2, '.', '') }}</td>
                    </tr>
                    
                    <!-- Complémentaire santé -->
                    <tr>
                        <td>Complémentaire incapacité-invalidité-décès</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(10.73, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(26.13, 2, '.', '') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section-title">ACCIDENTS DU TRAVAIL ET RETRAITE</div>
        <div class="remuneration-table">
            <table class="main-table">
                <tbody>
                    <tr>
                        <td>Accidents du travail - maladies professionnelles</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(2.5700, 4, '.', '') }}</td>
                        <td>{{ number_format(59.95, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td>Sécurité sociale plafonnement</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(6.0000, 4, '.', '') }}</td>
                        <td>{{ number_format(139.96, 2, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(8.5500, 4, '.', '') }}</td>
                        <td>{{ number_format(199.44, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td>Retraite complémentaire</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(3.9300, 4, '.', '') }}</td>
                        <td>{{ number_format(91.68, 2, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(5.8900, 4, '.', '') }}</td>
                        <td>{{ number_format(137.40, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td>CEG</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(0.8600, 4, '.', '') }}</td>
                        <td>{{ number_format(20.06, 2, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(1.2900, 4, '.', '') }}</td>
                        <td>{{ number_format(30.09, 2, '.', '') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section-title">ASSURANCE CHÔMAGE ET PRÉVOYANCE</div>
        <div class="remuneration-table">
            <table class="main-table">
                <tbody>
                    <tr>
                        <td>Assurance chômage</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(4.0500, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->assurance_chomage ?? 94.44, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td>AGS (FNGS)</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(0.1500, 4, '.', '') }}</td>
                        <td>{{ number_format(3.50, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td>Prévoyance non cadre TA</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(0.7800, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->prevoyance ?? 18.19, 2, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(0.4200, 4, '.', '') }}</td>
                        <td>{{ number_format(9.80, 2, '.', '') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section-title">AUTRES CONTRIBUTIONS ET TOTAL DES COTISATIONS</div>
        <div class="remuneration-table">
            <table class="main-table">
                <tbody>
                    <tr>
                        <td>Contribution solidarité autonomie</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td>{{ number_format(0.3000, 4, '.', '') }}</td>
                        <td>{{ number_format($fichePaie->contribution_solidarite ?? 7.00, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td>Forfait social + Versement mobilité + FNAL</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }}</td>
                        <td></td>
                        <td>{{ number_format(60.40, 2, '.', '') }}</td>
                    </tr>
                    <tr class="category-header">
                        <td>TOTAL DES COTISATIONS</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(301.27, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(784.34, 2, '.', '') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section-title">CSG / CRDS</div>
        <div class="remuneration-table">
            <table class="main-table">
                <tbody>
                    <tr>
                        <td>CSG déductible (6.8000%)</td>
                        <td>{{ number_format($fichePaie->salaire_brut * 0.9825 ?? 2291.87, 2, '.', '') }}</td>
                        <td></td>
                        <td>{{ number_format($fichePaie->csg_deductible ?? 155.85, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>CSG non déductible (2.4000%) + CRDS (0.5000%)</td>
                        <td>{{ number_format($fichePaie->salaire_brut * 0.9825 ?? 2291.87, 2, '.', '') }}</td>
                        <td></td>
                        <td>{{ number_format(($fichePaie->csg_non_deductible ?? 55.00) + ($fichePaie->crds ?? 11.46), 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Récapitulatif des totaux et net à payer -->
        <div class="section-title">TOTAL COTISATIONS ET NET À PAYER</div>
        <div class="remuneration-table">
            <table class="main-table">
                <tbody>
                    <tr class="total-row">
                        <td>Total des cotisations et contributions</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(523.58, 2, '.', '') }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(784.34, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td>Allègement de cotisations</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(-466.54, 2, '.', '') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>TOTAL VERSÉ PAR L'EMPLOYEUR</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format(2650.49, 2, '.', '') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="net-pay-section">
            <div class="net-pay-box">
                <div class="net-pay-title">SALAIRE BRUT</div>
                <div class="net-pay-value">{{ number_format($fichePaie->salaire_brut ?? 2332.69, 2, '.', '') }} €</div>
            </div>
            
            <div class="net-pay-box">
                <div class="net-pay-title">NET À PAYER AVANT IMPÔT</div>
                <div class="net-pay-value">{{ number_format(1809.11, 2, '.', '') }} €</div>
            </div>
            
            <div class="net-pay-box">
                <div class="net-pay-title">IMPÔT PRÉLEVÉ À LA SOURCE</div>
                <div class="net-pay-value">{{ number_format($fichePaie->impot_preleve_source ?? 0, 2, '.', '') }} €</div>
            </div>
            
            <div class="net-pay-box highlight">
                <div class="net-pay-title">NET À PAYER</div>
                <div class="net-pay-value">{{ number_format(1809.11, 2, '.', '') }} €</div>
            </div>
        </div>
        
        <!-- Cumuls et Message légal combinés -->
        <div class="section-title">CUMULS ET INFORMATIONS LÉGALES</div>
        <div class="cumul-section">
            <div class="cumul-box">
                <div class="cumul-label">Brut</div>
                <div class="cumul-value">{{ number_format(13996.14, 2, '.', '') }} €</div>
            </div>
            <div class="cumul-box">
                <div class="cumul-label">Net imposable</div>
                <div class="cumul-value">{{ number_format(10854.66, 2, '.', '') }} €</div>
            </div>
            <div class="cumul-box">
                <div class="cumul-label">Net payé</div>
                <div class="cumul-value">{{ number_format(10854.66, 2, '.', '') }} €</div>
            </div>
            <div class="cumul-box">
                <div class="cumul-label">Cot. patronales</div>
                <div class="cumul-value">{{ number_format(1906.44, 2, '.', '') }} €</div>
            </div>
        </div>
        
        <div class="footer">
            <div style="float: left; width: 75%; font-size: 8px; line-height: 1.2;">Conservez ce bulletin sans limitation de durée. Document généré le {{ \Carbon\Carbon::now()->format('d/m/Y') }}. @if($fichePaie->commentaires)<span style="color: #6b21a8; font-weight: bold;">{{ $fichePaie->commentaires }}</span>@endif</div>
            <div style="float: right; width: 20%; text-align: right;">
                <div class="signature-line"></div>
                <div class="signature-label">Signature</div>
            </div>
        </div>
        <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>

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
            background: linear-gradient(135deg, #4c1d95 0%, #7e22ce 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            position: relative;
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.15);
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
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header h2 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            color: rgba(255,255,255,1);
            letter-spacing: 0.5px;
            text-transform: capitalize;
        }
        .header-logo {
            width: 80px;
            height: auto;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.2));
        }
        .main-content {
            margin-top: 30px;
        }
        .info-section {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.08);
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(124, 58, 237, 0.1);
        }
        .info-section-header {
            border-bottom: 2px solid #e9d5ff;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .info-section-icon {
            background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%);
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-weight: bold;
            box-shadow: 0 3px 6px rgba(124, 58, 237, 0.2);
        }
        .info-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #4c1d95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
        }
        .info-box {
            flex: 1;
            min-width: 30%;
            background: linear-gradient(to bottom, #faf5ff 0%, #f5f3ff 100%);
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.08);
            border: 1px solid rgba(167, 139, 250, 0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .info-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(124, 58, 237, 0.12);
        }
        .info-box-title {
            font-size: 12px;
            font-weight: 600;
            color: #6b21a8;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-box-value {
            font-size: 18px;
            font-weight: 700;
            color: #4c1d95;
            margin: 0;
        }
        .info-label {
            font-weight: 600;
            color: #6b21a8;
            display: inline-block;
            width: 40%;
        }
        .info-value {
            color: #4c1d95;
            font-weight: 500;
            display: inline-block;
            width: 60%;
        }
        h2 {
            color: #6b21a8;
            font-size: 22px;
            margin-top: 40px;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e9d5ff;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            font-weight: 700;
        }
        h2:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, #7e22ce, #a855f7);
            border-radius: 3px;
        }
        .card {
            background-color: white;
            border-radius: 12px;
            margin-bottom: 35px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.1);
            border: 1px solid rgba(167, 139, 250, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(124, 58, 237, 0.15);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        th, td {
            padding: 16px 20px;
            border-bottom: 1px solid #e9d5ff;
            text-align: left;
        }
        th {
            background: linear-gradient(to right, #f5f3ff, #ede9fe);
            font-weight: 600;
            color: #5b21b6;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 2px solid #c4b5fd;
        }
        tr:hover {
            background-color: #f8f4ff;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background: linear-gradient(to right, #f5f3ff, #ede9fe);
            font-weight: 600;
        }
        .total-hours {
            color: #6b21a8;
            font-weight: 700;
        }
        .hours-25 {
            color: #7e22ce;
        }
        .hours-50 {
            color: #6b21a8;
        }
        .hours-night {
            color: #8b5cf6;
        }
        .hours-sunday {
            color: #d97706;
        }
        .hours-holiday {
            color: #dc2626;
        }
        .hours-absence {
            color: #64748b;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 2px solid #e9d5ff;
            padding-top: 25px;
            background: linear-gradient(to right, #f5f3ff, #ede9fe, #f5f3ff);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 -5px 15px rgba(124, 58, 237, 0.08);
        }
        .footer p {
            margin: 6px 0;
        }
        .footer .company {
            font-weight: 600;
            color: #6b21a8;
            font-size: 15px;
            letter-spacing: 0.5px;
        }
        .footer .date {
            font-style: italic;
            color: #7e22ce;
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
            gap: 15px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .summary-box {
            flex: 1;
            min-width: 130px;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(124, 58, 237, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(167, 139, 250, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .summary-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.15);
        }
        .summary-box:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }
        .summary-box h3 {
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .summary-box .value {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-box .label {
            font-size: 10px;
            font-weight: 500;
            opacity: 0.8;
        }
        .box-normal {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        }
        .box-normal:before {
            background: linear-gradient(to right, #7e22ce, #a855f7);
        }
        .box-normal h3, .box-normal .value {
            color: #5b21b6;
        }
        .box-normal .label {
            color: #6b21a8;
        }
        .box-night {
            background: linear-gradient(135deg, #f3e8ff 0%, #ede9fe 100%);
        }
        .box-night:before {
            background: linear-gradient(to right, #6b21a8, #8b5cf6);
        }
        .box-night h3, .box-night .value {
            color: #5b21b6;
        }
        .box-night .label {
            color: #6b21a8;
        }
        .box-sunday {
            background: linear-gradient(135deg, #fef3c7 0%, #fef9c3 100%);
        }
        .box-sunday:before {
            background: linear-gradient(to right, #b45309, #d97706);
        }
        .box-sunday h3, .box-sunday .value {
            color: #92400e;
        }
        .box-sunday .label {
            color: #b45309;
        }
        .box-holiday {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        }
        .box-holiday:before {
            background: linear-gradient(to right, #b91c1c, #ef4444);
        }
        .box-holiday h3, .box-holiday .value {
            color: #991b1b;
        }
        .box-holiday .label {
            color: #b91c1c;
        }
        .box-absence {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        }
        .box-absence:before {
            background: linear-gradient(to right, #475569, #64748b);
        }
        .box-absence h3, .box-absence .value {
            color: #334155;
        }
        .box-absence .label {
            color: #475569;
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
        .summary-box {
            flex: 1;
            min-width: 180px;
            padding: 20px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1);
            border: 1px solid rgba(167, 139, 250, 0.2);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 5px;
            position: relative;
            overflow: hidden;
        }
        .summary-box h3 {
            font-size: 15px;
            margin: 0 0 15px 0;
            color: #6b21a8;
            font-weight: 600;
            position: relative;
            padding-bottom: 8px;
        }
        .summary-box h3:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background: linear-gradient(to right, #7e22ce, #a855f7);
            border-radius: 2px;
        }
        .summary-box .value {
            font-size: 26px;
            font-weight: 700;
            color: #4c1d95;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            justify-content: space-between;
        }
        .box-normal {
            border-left: 4px solid #7e22ce;
        }
        .box-normal:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #7e22ce, #a855f7);
        }
        
        .box-night {
            border-left: 4px solid #1e40af;
        }
        .box-night:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #1e40af, #3b82f6);
        }
        
        .box-sunday {
            border-left: 4px solid #9333ea;
        }
        .box-sunday:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #9333ea, #c084fc);
        }
        
        .box-holiday {
            border-left: 4px solid #be123c;
        }
        .box-holiday:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #be123c, #fb7185);
        }
        
        .box-absence {
            border-left: 4px solid #ca8a04;
        }
        .box-absence:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #ca8a04, #facc15);
        }
        
        .summary-box .label {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header" style="background: linear-gradient(135deg, #4c1d95 0%, #7e22ce 100%); position: relative; overflow: hidden; padding: 30px; border-radius: 15px; box-shadow: 0 10px 25px rgba(124, 58, 237, 0.2);">
            <!-- Élément décoratif en arrière-plan -->
            <div style="position: absolute; top: 0; right: 0; width: 150px; height: 150px; background: radial-gradient(circle at center, rgba(168, 85, 247, 0.4) 0%, rgba(168, 85, 247, 0) 70%); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -20px; left: 30px; width: 100px; height: 100px; background: radial-gradient(circle at center, rgba(168, 85, 247, 0.3) 0%, rgba(168, 85, 247, 0) 70%); border-radius: 50%;"></div>
            
            <div class="header-content" style="position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center;">
                <div class="header-title">
                    <h1 style="font-size: 28px; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Rapport de comptabilité</h1>
                    <div style="display: inline-block; background-color: rgba(255,255,255,0.2); padding: 6px 15px; border-radius: 30px; margin-top: 5px;">
                        <h2 style="font-size: 18px; font-weight: 600; margin: 0; color: white; letter-spacing: 0.5px; text-transform: capitalize;">{{ $mois }} {{ $annee }}</h2>
                    </div>
                </div>
                @if(isset($societe) && isset($societe->logo) && $societe->logo)
                    <div style="background: rgba(255,255,255,0.9); padding: 10px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        <img src="{{ public_path('storage/' . $societe->logo) }}" alt="Logo" style="width: 80px; height: auto; display: block;">
                    </div>
                @endif
            </div>
        </div>
        
        <div class="main-content">
            <div class="info-section" style="margin-top: 30px; margin-bottom: 30px;">
                <div class="info-section-header" style="display: flex; align-items: center; margin-bottom: 20px;">
                    <div style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.2);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <h3 style="color: #6b21a8; font-size: 18px; margin: 0; font-weight: 600; letter-spacing: 0.5px;">Informations générales</h3>
                </div>
                
                <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 15px;">
                    <div style="flex: 1; min-width: 200px; background: linear-gradient(to right, #f5f3ff, #ede9fe); border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.1); border-left: 4px solid #7e22ce;">
                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">EMPLOYÉ</p>
                        <p style="margin: 0; font-size: 18px; color: #4c1d95; font-weight: 600;">{{ $nomEmploye }}</p>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #6b21a8;">{{ $employe->statut ?? 'Agent de sécurité' }}</p>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px; background: linear-gradient(to right, #f5f3ff, #ede9fe); border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.1); border-left: 4px solid #7e22ce;">
                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">PÉRIODE</p>
                        <p style="margin: 0; font-size: 18px; color: #4c1d95; font-weight: 600; text-transform: capitalize;">{{ $mois }} {{ $annee }}</p>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px; background: linear-gradient(to right, #f5f3ff, #ede9fe); border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.1); border-left: 4px solid #7e22ce;">
                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">DOCUMENT</p>
                        <p style="margin: 0; font-size: 18px; color: #4c1d95; font-weight: 600;">Rapport comptable</p>
                        <p style="margin: 8px 0 0 0; font-size: 13px; color: #6b21a8;">Généré le {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="info-section" style="margin-bottom: 30px;">
                <div class="info-section-header" style="display: flex; align-items: center; margin-bottom: 15px;">
                    <div style="background: linear-gradient(135deg, #be123c 0%, #fb7185 100%); width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; box-shadow: 0 4px 10px rgba(190, 18, 60, 0.2);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <h3 style="color: #be123c; font-size: 18px; margin: 0; font-weight: 600; letter-spacing: 0.5px;">Information importante</h3>
                </div>
                <div style="background: linear-gradient(to right, #fee2e2, #fecaca); border-radius: 12px; padding: 15px 20px; box-shadow: 0 4px 15px rgba(190, 18, 60, 0.1); border-left: 4px solid #be123c;">
                    <p style="margin: 0; font-size: 14px; color: #9f1239; line-height: 1.6;">Les heures de nuit sont comptabilisées uniquement pour les heures travaillées entre <strong>21h et 6h</strong> du matin. Les services marqués <strong>RH</strong> ou <strong>CP</strong> sont exclus du calcul.</p>
                </div>
            </div>
        
        <!-- Pas de saut de page forcé pour permettre un meilleur flux du contenu -->
        
        <!-- Récapitulatif visuel des heures -->
        <div class="info-section" style="margin-bottom: 0; margin-top: 10px;">
            <div class="info-section-header" style="display: flex; align-items: center; margin-bottom: 2px;">
                <div style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; box-shadow: 0 3px 8px rgba(124, 58, 237, 0.2);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 6v6l4 2"></path>
                    </svg>
                </div>
                <h3 style="color: #6b21a8; font-size: 16px; margin: 0; font-weight: 600; letter-spacing: 0.5px;">Récapitulatif des heures</h3>
            </div>
            
            <!-- Section unique pour toutes les boîtes récapitulatives, avec style pour éviter les coupures -->
            <div style="page-break-inside: avoid;">
                <!-- Titre de la section récapitulative avec style amélioré et espacement réduit -->
                <div style="margin-bottom: 8px; background: linear-gradient(to right, #f5f3ff, #ede9fe); padding: 8px 12px; border-radius: 8px; border-left: 3px solid #7e22ce;">
                    <h3 style="font-size: 15px; color: #6b21a8; margin: 0 0 3px 0; font-weight: 600; letter-spacing: 0.5px;">Vue d'ensemble du mois</h3>
                    <p style="color: #64748b; font-size: 12px; margin: 0; line-height: 1.3;">Récapitulatif des heures travaillées pour <strong style="color: #6b21a8;">{{ $mois }} {{ $annee }}</strong></p>
                </div>
                
                <!-- Conteneur principal avec toutes les boîtes, style amélioré pour éviter les coupures et espacement réduit -->
                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; page-break-inside: avoid;">
                    <!-- Heures totales -->
                    <div style="flex: 1; min-width: 110px; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(124, 58, 237, 0.1); text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(167, 139, 250, 0.2); background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, #4c1d95, #7e22ce);"></div>
                        <h3 style="margin-top: 2px; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; color: #4c1d95;">Heures totales</h3>
                        <div style="font-size: 20px; font-weight: bold; margin-bottom: 2px; color: #4c1d95;">{{ number_format($recapMensuel['total_heures'], 2) }}h</div>
                        <div style="font-size: 10px; font-weight: 500; color: #6b21a8; opacity: 0.8;">Total de toutes les heures</div>
                    </div>
                    
                    <!-- Heures sup -->
                    <div style="flex: 1; min-width: 110px; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(124, 58, 237, 0.1); text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(167, 139, 250, 0.2); background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, #4c1d95, #7e22ce);"></div>
                        <h3 style="margin-top: 2px; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; color: #4c1d95;">Heures sup. 25%</h3>
                        <div style="font-size: 20px; font-weight: bold; margin-bottom: 2px; color: #4c1d95;">{{ number_format($recapMensuel['heures_25'], 2) }}h</div>
                        <div style="font-size: 10px; font-weight: 500; color: #6b21a8; opacity: 0.8;">Entre 35h et 43h</div>
                    </div>
                    
                    <!-- Heures sup 50% -->
                    <div style="flex: 1; min-width: 110px; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(124, 58, 237, 0.1); text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(167, 139, 250, 0.2); background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, #4c1d95, #7e22ce);"></div>
                        <h3 style="margin-top: 2px; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; color: #4c1d95;">Heures sup. 50%</h3>
                        <div style="font-size: 20px; font-weight: bold; margin-bottom: 2px; color: #4c1d95;">{{ number_format($recapMensuel['heures_50'], 2) }}h</div>
                        <div style="font-size: 10px; font-weight: 500; color: #6b21a8; opacity: 0.8;">Au-delà de 43h</div>
                    </div>
                </div>
                
                <!-- Titre de la section des heures spéciales avec style amélioré et espacement réduit -->
                <div style="margin: 5px 0 5px 0; background: linear-gradient(to right, #f5f3ff, #ede9fe); padding: 8px 12px; border-radius: 6px; border-left: 3px solid #7e22ce;">
                    <h3 style="font-size: 14px; color: #6b21a8; margin: 0 0 2px 0; font-weight: 600; letter-spacing: 0.3px;">Heures spéciales</h3>
                    <p style="color: #64748b; font-size: 11px; margin: 0; line-height: 1.3;">Ces heures bénéficient de <strong style="color: #6b21a8;">majorations spécifiques</strong></p>
                </div>
                
                <!-- Grille des heures spéciales avec style amélioré et espacement réduit -->
                <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; page-break-inside: avoid;">
                    <!-- Heures de nuit -->
                    <div style="flex: 1; min-width: 110px; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(124, 58, 237, 0.1); text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(167, 139, 250, 0.2); background: linear-gradient(135deg, #f3e8ff 0%, #ede9fe 100%);">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, #6b21a8, #8b5cf6);"></div>
                        <h3 style="margin-top: 2px; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; color: #5b21b6;">Heures de nuit</h3>
                        <div style="font-size: 18px; font-weight: bold; margin-bottom: 2px; color: #5b21b6;">{{ number_format($recapMensuel['heures_nuit'] ?? 0, 2) }}h</div>
                        <div style="font-size: 10px; font-weight: 500; color: #6b21a8; opacity: 0.8;">Entre 21h et 6h (+10%)</div>
                    </div>
                    
                    <!-- Heures dimanche -->
                    <div style="flex: 1; min-width: 110px; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(214, 158, 46, 0.1); text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(217, 119, 6, 0.2); background: linear-gradient(135deg, #fef3c7 0%, #fef9c3 100%);">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, #b45309, #d97706);"></div>
                        <h3 style="margin-top: 2px; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; color: #92400e;">Heures dimanche</h3>
                        <div style="font-size: 18px; font-weight: bold; margin-bottom: 2px; color: #92400e;">{{ number_format($recapMensuel['heures_dimanche'] ?? 0, 2) }}h</div>
                        <div style="font-size: 10px; font-weight: 500; color: #b45309; opacity: 0.8;">Travail le dimanche (+25%)</div>
                    </div>
                    
                    <!-- Heures jours fériés -->
                    <div style="flex: 1; min-width: 110px; padding: 8px; border-radius: 6px; box-shadow: 0 2px 8px rgba(220, 38, 38, 0.1); text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(220, 38, 38, 0.2); background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, #b91c1c, #dc2626);"></div>
                        <h3 style="margin-top: 2px; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; color: #b91c1c;">Jours fériés</h3>
                        <div style="font-size: 18px; font-weight: bold; margin-bottom: 2px; color: #b91c1c;">{{ number_format($recapMensuel['heures_feries'] ?? 0, 2) }}h</div>
                        <div style="font-size: 10px; font-weight: 500; color: #b91c1c; opacity: 0.8;">Travail les jours fériés (+50%)</div>
                    </div>
                </div>
                
                <!-- Section des absences avec style amélioré et espacement réduit - Regroupée dans un seul conteneur pour éviter les coupures -->
                <div style="page-break-inside: avoid; margin-top: 5px;">
                    <div style="margin: 0 0 5px 0; background: linear-gradient(to right, #f5f3ff, #ede9fe); padding: 8px 15px; border-radius: 8px; border-left: 4px solid #7e22ce;">
                        <h3 style="font-size: 15px; color: #6b21a8; margin: 0 0 3px 0; font-weight: 600; letter-spacing: 0.3px;">Absences</h3>
                        <p style="color: #64748b; font-size: 12px; margin: 0; line-height: 1.3;">Jours d'absence enregistrés pour <strong style="color: #6b21a8;">{{ $mois }} {{ $annee }}</strong></p>
                    </div>
                    
                    <!-- Boîte d'absences avec style amélioré et espacement réduit -->
                    <div style="display: flex; margin-bottom: 8px;">
                        <div style="flex: 1; padding: 10px; border-radius: 8px; box-shadow: 0 3px 10px rgba(100, 116, 139, 0.1); text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(100, 116, 139, 0.2); background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);">
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(to right, #475569, #64748b);"></div>
                            <h3 style="margin-top: 3px; margin-bottom: 5px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.3px; font-weight: 600; color: #475569;">Jours d'absence</h3>
                            <div style="font-size: 22px; font-weight: bold; margin-bottom: 2px; color: #475569;">{{ number_format($recapMensuel['absences'] ?? 0, 0) }}</div>
                            <div style="font-size: 11px; font-weight: 500; color: #64748b; opacity: 0.8;">Jours non travaillés ce mois</div>
                        </div>
                    </div>
                    
                    <!-- Légende des majorations avec style amélioré et espacement réduit -->
                    <div style="margin-top: 8px; padding: 10px 15px; background: linear-gradient(to right, #f5f3ff, #ede9fe); border-radius: 8px; border-left: 4px solid #7e22ce; box-shadow: 0 3px 8px rgba(124, 58, 237, 0.08); page-break-inside: avoid;">
                    <h4 style="font-size: 14px; color: #6b21a8; margin: 0 0 8px 0; font-weight: 600; display: flex; align-items: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b21a8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        Informations sur les majorations
                    </h4>
                    <ul style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 12px; line-height: 1.5;">
                        <li>Heures de nuit <strong>(21h-6h)</strong> : majoration de <strong style="color: #6b21a8;">10%</strong> du taux horaire</li>
                        <li>Heures du dimanche : majoration de <strong style="color: #b45309;">25%</strong> du taux horaire</li>
                        <li>Heures des jours fériés : majoration de <strong style="color: #b91c1c;">50%</strong> du taux horaire</li>
                    </ul>
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
                    <th style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">PÉRIODE</div>
                    </th>
                    <th class="text-right" style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">TOTAL HEURES</div>
                    </th>
                    <th class="text-right" style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">HEURES SUP. 25%<br><span style="font-weight: normal; font-size: 11px; opacity: 0.9;">(36H À 43H)</span></div>
                    </th>
                    <th class="text-right" style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">HEURES SUP. 50%<br><span style="font-weight: normal; font-size: 11px; opacity: 0.9;">(>44H)</span></div>
                    </th>
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
                
                <tr class="total-row" style="background: linear-gradient(to right, #f5f3ff, #ede9fe); border-top: 2px solid #c4b5fd;">
                    <td style="font-weight: 700; color: #6b21a8; font-size: 14px;">Total du mois</td>
                    <td class="text-right" style="font-weight: 700; color: #4c1d95; font-size: 14px;">{{ number_format($recapMensuel['total_heures'], 2) }}h</td>
                    <td class="text-right hours-25" style="font-weight: 700; color: #4c1d95; font-size: 14px;">{{ number_format($recapMensuel['heures_25'], 2) }}h</td>
                    <td class="text-right hours-50" style="font-weight: 700; color: #4c1d95; font-size: 14px;">{{ number_format($recapMensuel['heures_50'], 2) }}h</td>
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
                    <th style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">PÉRIODE</div>
                    </th>
                    <th class="text-right" style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">HEURES DE NUIT<br><span style="font-weight: normal; font-size: 11px; opacity: 0.9;">(21H-06H)</span></div>
                    </th>
                    <th class="text-right" style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">HEURES DIMANCHE</div>
                    </th>
                    <th class="text-right" style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">HEURES JOURS FÉRIÉS</div>
                    </th>
                    <th class="text-right" style="background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none;">
                        <div style="padding: 5px 0;">ABSENCES<br><span style="font-weight: normal; font-size: 11px; opacity: 0.9;">(JOURS)</span></div>
                    </th>
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
                
                <tr class="total-row" style="background: linear-gradient(to right, #f5f3ff, #ede9fe); border-top: 2px solid #c4b5fd;">
                    <td style="font-weight: 700; color: #6b21a8; font-size: 14px;">Total du mois</td>
                    <td class="text-right hours-night" style="font-weight: 700; color: #4c1d95; font-size: 14px;">{{ number_format($recapMensuel['heures_nuit'] ?? 0, 2) }}h</td>
                    <td class="text-right hours-sunday" style="font-weight: 700; color: #4c1d95; font-size: 14px;">{{ number_format($recapMensuel['heures_dimanche'] ?? 0, 2) }}h</td>
                    <td class="text-right hours-holiday" style="font-weight: 700; color: #4c1d95; font-size: 14px;">{{ number_format($recapMensuel['heures_jours_feries'] ?? 0, 2) }}h</td>
                    <td class="text-right hours-absence" style="font-weight: 700; color: #4c1d95; font-size: 14px;">{{ number_format($recapMensuel['absences'] ?? 0, 0) }} jour(s)</td>
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
        
        <div style="margin-bottom: 20px; background-color: #f5f3ff; padding: 15px; border-radius: 10px; border: 1px solid #e9d5ff; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.08);">
            <p style="margin: 0; font-size: 13px; color: #64748b;">
                <strong style="color: #6b21a8;">Légende:</strong> 
                <span style="margin-left: 10px; padding: 3px 8px; background-color: #f5f3ff; border-radius: 6px; font-size: 12px; color: #7e22ce; border: 1px solid #e9d5ff;">Jour normal</span>
                <span style="margin-left: 10px; padding: 3px 8px; background-color: #fef3c7; border-radius: 6px; font-size: 12px; color: #b45309; border: 1px solid #fde68a;">Dimanche</span>
                <span style="margin-left: 10px; padding: 3px 8px; background-color: #fee2e2; border-radius: 6px; font-size: 12px; color: #b91c1c; border: 1px solid #fecaca;">Jour férié</span>
                <span style="margin-left: 10px; padding: 3px 8px; background-color: #f3e8ff; border-radius: 6px; font-size: 12px; color: #6b21a8; border: 1px solid #e9d5ff;">Heures de nuit</span>
            </p>
        </div>
        
        @foreach($planningsByWeek as $weekTitle => $weekPlannings)
            <div class="card" style="margin-bottom: 30px; page-break-inside: avoid;">
                <h3 style="background: linear-gradient(to right, #f5f3ff, #ede9fe); padding: 15px 20px; margin: 0; font-size: 16px; color: #6b21a8; border-bottom: 1px solid #e9d5ff; font-weight: 600;">{{ $weekTitle }}</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 15%; background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none; padding: 10px;">
                                <div style="padding: 5px 0;">Date</div>
                            </th>
                            <th style="width: 30%; background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none; padding: 10px;">
                                <div style="padding: 5px 0;">Lieu</div>
                            </th>
                            <th style="width: 15%; text-align: center; background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none; padding: 10px;">
                                <div style="padding: 5px 0;">Début</div>
                            </th>
                            <th style="width: 15%; text-align: center; background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none; padding: 10px;">
                                <div style="padding: 5px 0;">Fin</div>
                            </th>
                            <th style="width: 15%; text-align: center; background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none; padding: 10px;">
                                <div style="padding: 5px 0;">Heures</div>
                            </th>
                            <th style="width: 10%; text-align: center; background: linear-gradient(135deg, #7e22ce 0%, #a855f7 100%); color: white; border-bottom: none; padding: 10px;">
                                <div style="padding: 5px 0;">Type</div>
                            </th>
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
                                $rowClass = 'background-color: #fef2f2;';
                                $typeLabel = 'Férié';
                                $typeStyle = 'background-color: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 6px; font-size: 12px; border: 1px solid #fecaca; font-weight: 500;';
                            } elseif ($isSunday) {
                                $rowClass = 'background-color: #fffbeb;';
                                $typeLabel = 'Dim';
                                $typeStyle = 'background-color: #fef3c7; color: #b45309; padding: 3px 8px; border-radius: 6px; font-size: 12px; border: 1px solid #fde68a; font-weight: 500;';
                            } else {
                                $typeLabel = 'Normal';
                                $typeStyle = 'background-color: #f5f3ff; color: #7e22ce; padding: 3px 8px; border-radius: 6px; font-size: 12px; border: 1px solid #e9d5ff; font-weight: 500;';
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
                                $typeStyle = 'background-color: #f3e8ff; color: #6b21a8; padding: 3px 8px; border-radius: 6px; font-size: 12px; border: 1px solid #e9d5ff; font-weight: 500;';
                            }
                        @endphp
                        <tr style="{{ $rowClass }}">
                            <td style="padding: 12px 18px; border-bottom: 1px solid #e9d5ff; border-left: 3px solid #c4b5fd;">
                                <div style="font-weight: 600; color: #4c1d95;">{{ $date->format('d/m/Y') }}</div>
                                <div style="font-size: 12px; color: #64748b; margin-top: 3px; text-transform: capitalize;">{{ $date->locale('fr')->isoFormat('dddd') }}</div>
                            </td>
                            <td style="padding: 12px 18px; border-bottom: 1px solid #e9d5ff; color: #4b5563;">{{ $planning->lieu->nom ?? 'Non défini' }}</td>
                            <td style="padding: 12px 18px; border-bottom: 1px solid #e9d5ff; text-align: center; color: #4b5563;">{{ \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') }}</td>
                            <td style="padding: 12px 18px; border-bottom: 1px solid #e9d5ff; text-align: center; color: #4b5563;">{{ \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') }}</td>
                            <td style="padding: 12px 18px; border-bottom: 1px solid #e9d5ff; text-align: center; font-weight: 600; color: #6b21a8;">{{ number_format($planning->heures_travaillees, 2) }}</td>
                            <td style="padding: 12px 18px; border-bottom: 1px solid #e9d5ff; text-align: center;">
                                <span style="{{ $typeStyle }}">{{ $typeLabel }}</span>
                            </td>
                        </tr>
                        @endforeach
                        
                        <!-- Calcul du total des heures de la semaine -->
                        @php
                            $totalHeuresSemaine = collect($weekPlannings)->sum('heures_travaillees');
                        @endphp
                        
                        <tr class="total-row">
                            <td colspan="4" style="padding: 15px 20px; text-align: right; font-weight: 600; background: linear-gradient(to right, #f5f3ff, #ede9fe); border-top: 2px solid #c4b5fd;"><strong style="color: #6b21a8; font-size: 14px;">Total de la semaine</strong></td>
                            <td style="padding: 15px 20px; text-align: center; font-weight: 700; background: linear-gradient(to right, #f5f3ff, #ede9fe); color: #4c1d95; font-size: 14px; border-top: 2px solid #c4b5fd;">{{ number_format($totalHeuresSemaine, 2) }}h</td>
                            <td style="padding: 15px 20px; background: linear-gradient(to right, #f5f3ff, #ede9fe); border-top: 2px solid #c4b5fd;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @else
        <div class="card">
            <div style="text-align: center; padding: 30px; background: linear-gradient(to right, #f5f3ff, #ede9fe); border-radius: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#7e22ce" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 15px auto; display: block;">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <h3 style="color: #6b21a8; font-size: 18px; margin-bottom: 10px;">Aucun planning disponible</h3>
                <p style="color: #64748b; font-size: 14px;">Il n'y a pas de planning enregistré pour <strong>{{ $mois }} {{ $annee }}</strong>.</p>
            </div>
        </div>
    @endif
    


    <div class="footer" style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e9d5ff; background: linear-gradient(to right, #f5f3ff, #ede9fe); border-radius: 12px; padding: 25px; text-align: center;">
        <div style="margin-bottom: 15px;">
            <h3 style="color: #6b21a8; font-size: 18px; margin: 0 0 5px 0; font-weight: 600;">Vision Sécurité Privée</h3>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Expertise en sécurité privée</p>
        </div>
        
        <div style="display: flex; justify-content: center; margin: 15px 0;">
            <div style="border-right: 1px solid #e9d5ff; padding: 0 20px;">
                <p style="margin: 0; font-size: 12px; color: #64748b;">SIRET</p>
                <p style="margin: 0; font-weight: 600; color: #4c1d95; font-size: 14px;">85321467800033</p>
            </div>
            <div style="padding: 0 20px;">
                <p style="margin: 0; font-size: 12px; color: #64748b;">Document généré le</p>
                <p style="margin: 0; font-weight: 600; color: #4c1d95; font-size: 14px;">{{ date('d/m/Y à H:i') }}</p>
            </div>
        </div>
        
        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9d5ff;">
            <p style="margin: 0; font-weight: 600; color: #4c1d95; font-size: 12px;">Ce document est confidentiel et destiné uniquement à l'usage interne de l'entreprise.</p>
        </div>
    </div>
    </div> <!-- Fermeture du container -->
</body>
</html>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiches de paie - {{ $mois }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .fiche-paie {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #6b46c1;
            padding-bottom: 10px;
        }
        .header-left {
            width: 50%;
        }
        .header-right {
            width: 50%;
            text-align: right;
        }
        .logo {
            max-width: 150px;
            max-height: 60px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #6b46c1;
            margin: 20px 0;
            text-align: center;
        }
        .subtitle {
            font-size: 16px;
            color: #6b46c1;
            margin-bottom: 15px;
            text-align: center;
        }
        .info-block {
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8fafc;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 40%;
            font-weight: bold;
        }
        .info-value {
            width: 60%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3e8ff;
            color: #6b46c1;
            padding: 8px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }
        td {
            padding: 8px;
            border: 1px solid #e2e8f0;
        }
        .total-row {
            font-weight: bold;
            background-color: #f3e8ff;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f3e8ff;
            border-radius: 5px;
        }
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #6b46c1;
            margin-bottom: 10px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
        }
        .summary-value {
            text-align: right;
        }
        .net-pay {
            font-size: 16px;
            font-weight: bold;
            color: #6b46c1;
            margin-top: 10px;
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .gradient-header {
            background: linear-gradient(135deg, #8b5cf6, #6b46c1);
            color: white;
            padding: 10px;
            border-radius: 5px 5px 0 0;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    @foreach($fichesPaie as $index => $fiche)
        <div class="fiche-paie">
            <div class="header">
                <div class="header-left">
                    @if($societe->logo)
                        <img src="{{ public_path('storage/' . $societe->logo) }}" alt="Logo" class="logo">
                    @else
                        <h3>{{ $societe->nom }}</h3>
                    @endif
                    <div>
                        {{ $societe->adresse }}<br>
                        {{ $societe->code_postal }} {{ $societe->ville }}<br>
                        SIRET: {{ $societe->siret }}
                    </div>
                </div>
                <div class="header-right">
                    <h4>Bulletin de paie</h4>
                    <div>Période: {{ $fiche['mois'] }}</div>
                    <div>Date d'émission: {{ now()->format('d/m/Y') }}</div>
                </div>
            </div>

            <div class="gradient-header">
                <h2 style="margin: 0; text-align: center;">BULLETIN DE PAIE</h2>
            </div>

            <div class="info-block">
                <div class="info-row">
                    <div class="info-label">Employé:</div>
                    <div class="info-value">{{ $fiche['employe']->prenom }} {{ $fiche['employe']->nom }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Poste:</div>
                    <div class="info-value">{{ $fiche['employe']->poste ?? 'Non spécifié' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">N° Sécurité Sociale:</div>
                    <div class="info-value">{{ $fiche['employe']->num_secu ?? 'Non spécifié' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date d'embauche:</div>
                    <div class="info-value">{{ $fiche['employe']->date_embauche ? \Carbon\Carbon::parse($fiche['employe']->date_embauche)->format('d/m/Y') : 'Non spécifiée' }}</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Désignation</th>
                        <th>Quantité</th>
                        <th>Taux</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Heures normales</td>
                        <td>{{ number_format($fiche['heures_normales'], 2) }} h</td>
                        <td>{{ number_format($fiche['taux_horaire'], 2) }} €</td>
                        <td>{{ number_format($fiche['salaire_base'], 2) }} €</td>
                    </tr>
                    @if($fiche['heures_sup_25'] > 0)
                    <tr>
                        <td>Heures supplémentaires (25%)</td>
                        <td>{{ number_format($fiche['heures_sup_25'], 2) }} h</td>
                        <td>{{ number_format($fiche['taux_horaire'] * 1.25, 2) }} €</td>
                        <td>{{ number_format($fiche['montant_heures_sup_25'], 2) }} €</td>
                    </tr>
                    @endif
                    @if($fiche['heures_sup_50'] > 0)
                    <tr>
                        <td>Heures supplémentaires (50%)</td>
                        <td>{{ number_format($fiche['heures_sup_50'], 2) }} h</td>
                        <td>{{ number_format($fiche['taux_horaire'] * 1.5, 2) }} €</td>
                        <td>{{ number_format($fiche['montant_heures_sup_50'], 2) }} €</td>
                    </tr>
                    @endif
                    @if($fiche['heures_nuit'] > 0)
                    <tr>
                        <td>Heures de nuit (10%)</td>
                        <td>{{ number_format($fiche['heures_nuit'], 2) }} h</td>
                        <td>{{ number_format($fiche['taux_horaire'] * 1.1, 2) }} €</td>
                        <td>{{ number_format($fiche['montant_heures_nuit'], 2) }} €</td>
                    </tr>
                    @endif
                    @if($fiche['heures_dimanche'] > 0)
                    <tr>
                        <td>Heures dimanche (50%)</td>
                        <td>{{ number_format($fiche['heures_dimanche'], 2) }} h</td>
                        <td>{{ number_format($fiche['taux_horaire'] * 1.5, 2) }} €</td>
                        <td>{{ number_format($fiche['montant_heures_dimanche'], 2) }} €</td>
                    </tr>
                    @endif
                    @if($fiche['heures_jours_feries'] > 0)
                    <tr>
                        <td>Heures jours fériés (100%)</td>
                        <td>{{ number_format($fiche['heures_jours_feries'], 2) }} h</td>
                        <td>{{ number_format($fiche['taux_horaire'] * 2, 2) }} €</td>
                        <td>{{ number_format($fiche['montant_heures_jours_feries'], 2) }} €</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="3">Total brut</td>
                        <td>{{ number_format($fiche['salaire_brut'], 2) }} €</td>
                    </tr>
                </tbody>
            </table>

            <table>
                <thead>
                    <tr>
                        <th>Cotisations</th>
                        <th>Base</th>
                        <th>Taux</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Cotisations sociales</td>
                        <td>{{ number_format($fiche['salaire_brut'], 2) }} €</td>
                        <td>22%</td>
                        <td>{{ number_format($fiche['cotisations_salariales'], 2) }} €</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total des cotisations</td>
                        <td>{{ number_format($fiche['cotisations_salariales'], 2) }} €</td>
                    </tr>
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-title">Récapitulatif</div>
                <div class="summary-row">
                    <div class="summary-label">Total brut:</div>
                    <div class="summary-value">{{ number_format($fiche['salaire_brut'], 2) }} €</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Total cotisations:</div>
                    <div class="summary-value">{{ number_format($fiche['cotisations_salariales'], 2) }} €</div>
                </div>
                <div class="net-pay">
                    Salaire net à payer: {{ number_format($fiche['salaire_net'], 2) }} €
                </div>
            </div>

            <div class="footer">
                <p>Ce bulletin de paie est généré automatiquement. Document non contractuel à titre informatif.</p>
                <p>{{ $societe->nom }} - SIRET: {{ $societe->siret }}</p>
            </div>
        </div>

        @if($index < count($fichesPaie) - 1)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>

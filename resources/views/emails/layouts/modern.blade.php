<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <style>
        /* Styles de base */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f5f8fa;
        }
        
        /* Container principal */
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        /* En-tête */
        .header {
            background-color: #4f46e5;
            padding: 25px 0;
            text-align: center;
        }
        
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-weight: 600;
            font-size: 24px;
        }
        
        /* Logo */
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        
        /* Contenu */
        .content {
            padding: 30px;
        }
        
        /* Boîte d'information */
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #4f46e5;
            border-radius: 4px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .info-box h3 {
            margin-top: 0;
            color: #4f46e5;
            font-size: 18px;
        }
        
        /* Bouton */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.2s;
        }
        
        .btn:hover {
            background-color: #4338ca;
        }
        
        /* Pied de page */
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 0.85em;
            color: #6b7280;
            background-color: #f8fafc;
            border-top: 1px solid #e5e7eb;
        }
        
        /* Utilitaires */
        .text-center {
            text-align: center;
        }
        
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        
        .calendar-icon {
            text-align: center;
            margin: 20px 0;
        }
        
        .calendar-icon svg {
            width: 60px;
            height: 60px;
            fill: #4f46e5;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #4b5563;
        }
        
        .detail-value {
            color: #111827;
        }
        
        .attachment-info {
            background-color: #f3f4f6;
            border-radius: 4px;
            padding: 12px;
            margin-top: 25px;
            display: flex;
            align-items: center;
        }
        
        .attachment-icon {
            margin-right: 12px;
            color: #4f46e5;
        }
        
        .attachment-text {
            font-size: 0.9em;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        
        <div class="content">
            @yield('content')
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
            <p>
                <a href="{{ url('/') }}" style="color: #4f46e5; margin: 0 10px;">Accueil</a>
                <a href="{{ url('/contact') }}" style="color: #4f46e5; margin: 0 10px;">Contact</a>
                <a href="{{ url('/mentions-legales') }}" style="color: #4f46e5; margin: 0 10px;">Mentions légales</a>
            </p>
        </div>
    </div>
</body>
</html>

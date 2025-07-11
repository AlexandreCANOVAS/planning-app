<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <meta name="color-scheme" content="{{ request()->cookie('theme', 'light') === 'dark' ? 'dark' : 'light' }}">
        <meta name="theme-color" content="{{ request()->cookie('theme', 'light') === 'dark' ? '#111827' : '#ffffff' }}">
        <meta name="apple-mobile-web-app-status-bar-style" content="{{ request()->cookie('theme', 'light') === 'dark' ? 'black-translucent' : 'default' }}">
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
        
        <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'], 'defer')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/luxon/2.3.1/luxon.min.js"></script>
        
        <!-- Livewire Styles -->
        @livewireStyles
        
        <!-- Variables globales pour JavaScript -->
        <script>
            window.userId = {{ auth()->id() ?? 'null' }};
        </script>
        
        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        
        <!-- Styles -->
        @stack('styles')
        
        @if(request()->cookie('theme', 'light') === 'dark')
        <style>
            /* Thème sombre élégant et sophistiqué */
            :root.dark {
                /* Palette de couleurs ultra-moderne mais plus douce */
                --bg-primary: #050709;  /* Fond principal très sombre */
                --bg-secondary: rgba(9, 12, 20, 0.7);  /* Fond secondaire */
                --bg-tertiary: rgba(12, 17, 28, 0.8);  /* Fond tertiaire */
                --bg-card: rgba(9, 12, 20, 0.6);  /* Fond des cartes */
                
                /* Couleurs d'accent plus vives mais élégantes */
                --accent-primary: #3b82f6;  /* Bleu plus vif */
                --accent-secondary: #8b5cf6;  /* Violet plus vif */
                --accent-tertiary: #10b981;  /* Vert plus vif */
                --accent-quaternary: #ec4899;  /* Rose plus vif */
                --accent-quinary: #f59e0b;  /* Ambre plus vif */
                
                /* Texte */
                --text-primary: #ffffff;  /* Blanc pur plus lumineux */
                --text-secondary: #f3f4f6;  /* Gris très clair plus lumineux */
                --text-muted: #d1d5db;  /* Gris moyen plus lumineux */
                --text-accent: #93c5fd;  /* Bleu clair */
                --border-color: rgba(255, 255, 255, 0.05);  /* Bordures blanches plus visibles */
                
                /* Effets */
                --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);  /* Ombre douce */
                --hover-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);  /* Ombre au survol */
                --primary-glow: 0 0 20px rgba(59, 130, 246, 0.25);  /* Lueur bleue plus intense */
                --secondary-glow: 0 0 20px rgba(139, 92, 246, 0.25);  /* Lueur violette plus intense */
                --tertiary-glow: 0 0 20px rgba(16, 185, 129, 0.25);  /* Lueur verte plus intense */
            }
            
            /* Base du thème */
            html.dark {
                background-color: var(--bg-primary);
                color: var(--text-primary);
                color-scheme: dark;
                background-image: linear-gradient(135deg, #050709 0%, #0c111c 100%);
            }
            
            html.dark body {
                background-color: transparent;
                color: var(--text-primary);
                letter-spacing: 0.015em;
                line-height: 1.6;
            }
            
            /* Suppression des fonds noirs sur les éléments textuels */
            html.dark p, 
            html.dark span, 
            html.dark h1, 
            html.dark h2, 
            html.dark h3, 
            html.dark h4, 
            html.dark h5, 
            html.dark h6, 
            html.dark label, 
            html.dark small {
                background-color: transparent !important;
            }
            
            /* Typographie élégante */
            html.dark h1 {
                color: var(--accent-primary) !important;
                font-weight: 700 !important;
                letter-spacing: -0.025em !important;
                margin-bottom: 1.5rem !important;
                font-size: 1.875rem !important;
                line-height: 1.2 !important;
            }
            
            html.dark h2 {
                color: var(--accent-secondary) !important;
                font-weight: 600 !important;
                letter-spacing: -0.015em !important;
                margin-bottom: 1.25rem !important;
                font-size: 1.5rem !important;
                line-height: 1.3 !important;
            }
            
            html.dark h3 {
                color: var(--accent-tertiary) !important;
                font-weight: 600 !important;
                margin-bottom: 1rem !important;
                font-size: 1.25rem !important;
                line-height: 1.4 !important;
            }
            
            /* Fond principal pour les conteneurs */
            html.dark .bg-white, 
            html.dark .bg-gray-50, 
            html.dark .bg-gray-100, 
            html.dark [class*="bg-white"], 
            html.dark [class*="bg-gray"] {
                background-color: var(--bg-secondary) !important;
                color: var(--text-primary) !important;
                backdrop-filter: blur(12px) !important;
                -webkit-backdrop-filter: blur(12px) !important;
            }
            
            /* Cartes et conteneurs - style de base sans forcer les couleurs */
            html.dark .card, 
            html.dark .shadow-sm, 
            html.dark .shadow, 
            html.dark .shadow-md, 
            html.dark .shadow-lg, 
            html.dark .shadow-xl,
            html.dark div[role="region"] {
                border-radius: 0.75rem !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                padding: 1.25rem !important;
                margin-bottom: 1.25rem !important;
            }
            
            /* Effet hover sur les cartes */
            html.dark .card:hover, 
            html.dark div[role="region"]:hover {
                transform: translateY(-2px) !important;
            }
            
            /* Tableaux - style de base sans forcer les couleurs */
            html.dark table {
                border-collapse: separate !important;
                border-spacing: 0 !important;
                border-radius: 0.5rem !important;
                overflow: hidden !important;
                margin-bottom: 1.5rem !important;
                width: 100% !important;
            }
            
            html.dark th {
                font-weight: 500 !important;
                text-transform: uppercase !important;
                font-size: 0.75rem !important;
                letter-spacing: 0.05em !important;
                padding: 0.75rem 1rem !important;
                text-align: left !important;
            }
            
            html.dark tr {
                transition: all 0.2s ease-in-out !important;
            }
            
            html.dark td {
                padding: 0.75rem 1rem !important;
                vertical-align: middle !important;
            }
            
            /* Formulaires - style de base sans forcer les couleurs */
            html.dark input, 
            html.dark select, 
            html.dark textarea {
                border-radius: 0.375rem !important;
                padding: 0.5rem 0.75rem !important;
                transition: all 0.15s ease-in-out !important;
            }
            
            html.dark input:focus, 
            html.dark select:focus, 
            html.dark textarea:focus {
                outline: none !important;
            }
            
            /* Boutons - style de base sans forcer les couleurs */
            html.dark .btn,
            html.dark button {
                border-radius: 0.375rem !important;
                padding: 0.5rem 1rem !important;
                font-weight: 500 !important;
                transition: all 0.15s ease-in-out !important;
                letter-spacing: 0.01em !important;
            }
            
            html.dark .btn:hover,
            html.dark button:hover {
                transform: translateY(-1px) !important;
            }
            
            /* Styles de base des boutons sans forcer les couleurs */
            html.dark .btn {
                transition: all 0.2s ease-in-out !important;
            }
            
            /* Liens - style de base sans forcer les couleurs */
            html.dark a {
                text-decoration: none !important;
                transition: all 0.15s ease-in-out !important;
                background-color: transparent !important;
            }
            
            /* Badges - style de base sans forcer les couleurs */
            html.dark .badge {
                display: inline-flex !important;
                align-items: center !important;
                padding: 0.25rem 0.5rem !important;
                border-radius: 0.375rem !important;
                font-size: 0.75rem !important;
                font-weight: 500 !important;
                line-height: 1 !important;
                letter-spacing: 0.025em !important;
            }
            
            /* Icônes - style de base sans forcer les couleurs */
            html.dark i.fa, 
            html.dark i.fas, 
            html.dark i.far, 
            html.dark i.fal, 
            html.dark i.fab,
            html.dark svg {
                transition: all 0.15s ease-in-out !important;
            }
            
            /* Navigation - style de base sans forcer les couleurs */
            html.dark nav {
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
            }
            
            html.dark nav a {
                transition: all 0.15s ease-in-out !important;
            }
            
            /* Séparateurs */
            html.dark hr {
                border-color: rgba(75, 85, 99, 0.03) !important;
                margin: 1.5rem 0 !important;
            }
            
            /* Corrections spécifiques */
            html.dark .text-gray-600,
            html.dark .text-gray-700,
            html.dark .text-gray-800,
            html.dark .text-gray-900 {
                color: var(--text-secondary) !important;
            }
            
            html.dark .text-gray-500,
            html.dark .text-gray-400 {
                color: var(--text-muted) !important;
            }
            
            /* Les styles de couleurs de fond ont été supprimés pour permettre aux couleurs d'origine d'être affichées */
            
            /* Noms d'entreprise et titres - style de base sans forcer les couleurs */
            html.dark .company-name,
            html.dark .page-title,
            html.dark h1.text-xl,
            html.dark h1.text-2xl,
            html.dark h1.text-3xl {
                font-weight: 600 !important;
                letter-spacing: -0.01em !important;
            }
            
            /* Préserver les couleurs d'origine sur tout le site */
            html.dark [class*="text-"] {
                color: inherit !important;
            }
            
            html.dark [class*="bg-"] {
                background-color: inherit !important;
            }
            
            html.dark [class*="border-"] {
                border-color: inherit !important;
            }
            
            /* Bordures blanches subtiles globales */
            html.dark * {
                border-color: rgba(255, 255, 255, 0.03) !important;
            }
            
            /* Bordures subtiles aux cartes et conteneurs */
            html.dark .card,
            html.dark div[role="region"],
            html.dark .shadow-sm,
            html.dark .shadow,
            html.dark .shadow-md,
            html.dark table,
            html.dark th,
            html.dark td,
            html.dark tr,
            html.dark hr,
            html.dark nav,
            html.dark input,
            html.dark select,
            html.dark textarea {
                border: 1px solid rgba(255, 255, 255, 0.03) !important;
            }
        </style>
        @endif
    </head>
    <body class="font-sans antialiased {{ request()->cookie('theme', 'light') === 'dark' ? 'dark' : '' }} flex flex-col min-h-screen">
        <div class="flex-grow {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-950' : 'bg-gray-50' }}">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="{{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-900' : 'bg-white shadow' }}">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @elseif (session('error'))
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot ?? '' }}
                @hasSection('content')
                    @yield('content')
                @endif
            </main>
        </div>
            
        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-300">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Section Logo et Description -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <img src="{{ asset('images/logo.svg') }}" alt="Planify Logo" class="h-12 w-auto">
                        </div>
                        <p class="text-sm text-gray-400">Simplifiez la gestion de votre entreprise avec notre solution tout-en-un.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>

                    <!-- Section Fonctionnalités -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Fonctionnalités</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="text-base text-gray-300 hover:text-white">Planning</a></li>
                            <li><a href="#" class="text-base text-gray-300 hover:text-white">Congés</a></li>
                            <li><a href="#" class="text-base text-gray-300 hover:text-white">Suivi du temps</a></li>
                        </ul>
                    </div>

                    <!-- Section Entreprise -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Entreprise</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="#" class="text-base text-gray-300 hover:text-white">À propos</a></li>
                            <li><a href="#" class="text-base text-gray-300 hover:text-white">Tarifs</a></li>
                            <li><a href="{{ route('contact') }}" class="text-base text-gray-300 hover:text-white">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Section Support -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Support</h3>
                        <ul class="mt-4 space-y-2">
                            <li><a href="{{ route('pages.help') }}" class="text-base text-gray-300 hover:text-white">Centre d'aide</a></li>
                            <li><a href="{{ route('pages.documentation') }}" class="text-base text-gray-300 hover:text-white">Documentation</a></li>
                            <li><a href="{{ route('pages.system-status') }}" class="text-base text-gray-300 hover:text-white">Statut du système</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 border-t border-gray-800 pt-8 flex flex-col sm:flex-row justify-between items-center">
                    <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Planify. Tous droits réservés.</p>
                    <div class="flex space-x-6 mt-4 sm:mt-0">
                        <a href="{{ route('politique-confidentialite') }}" class="text-sm text-gray-400 hover:text-white">Politique de confidentialité</a>
                        <a href="#" class="text-sm text-gray-400 hover:text-white">Conditions d'utilisation</a>
                        <a href="{{ route('mentions-legales') }}" class="text-sm text-gray-400 hover:text-white">Mentions légales</a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Modal d'avertissement de session expirée -->
        @auth
        <div id="session-timeout-modal" class="fixed inset-0 z-[9999] flex items-center justify-center" style="display: none;">
            <div class="relative w-full max-w-md mx-auto p-2">
                <div class="bg-white border-2 border-gray-300 rounded-lg shadow-2xl overflow-hidden">
                    <!-- En-tête de la modal -->
                    <div class="p-4 border-b border-gray-200 flex items-center bg-gray-50">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <h5 class="text-lg font-bold text-gray-800" id="session-timeout-modal-title">
                            Avertissement d'inactivité
                        </h5>
                    </div>
                    
                    <!-- Corps de la modal -->
                    <div class="p-5 bg-white text-gray-700">
                        <p class="mb-3 text-base">Votre session est inactive depuis un moment.</p>
                        <p class="mb-3 text-base">Pour des raisons de sécurité, vous serez automatiquement déconnecté dans <span id="session-countdown" class="font-bold text-red-600 text-lg">30</span> secondes.</p>
                        <p class="text-base font-medium">Souhaitez-vous rester connecté ?</p>
                    </div>
                    
                    <!-- Pied de la modal -->
                    <div class="p-4 border-t border-gray-200 flex justify-between bg-gray-50">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button id="session-logout" type="button" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-md transition-colors duration-200 flex items-center shadow-sm">
                                <i class="fas fa-sign-out-alt mr-2"></i> Se déconnecter
                            </button>
                        </form>
                        <button id="session-continue" type="button" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-md transition-colors duration-200 flex items-center shadow-sm">
                            <i class="fas fa-redo-alt mr-2"></i> Continuer la session
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script de gestion du timeout de session -->
        <script src="{{ asset('js/session-timeout.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialiser le gestionnaire de timeout de session
                new SessionTimeoutHandler({
                    warningTime: 10 * 1000, // 10 secondes d'inactivité avant l'avertissement (pour test)
                    redirectTime: 10 * 1000,    // 10 secondes avant déconnexion après l'avertissement (pour test)
                    onWarning: function() {
                        console.log('Session inactive - Avertissement affiché');
                    },
                    onTimeout: function() {
                        console.log('Session expirée - Redirection vers la déconnexion');
                    }
                });
                
                // Gestion spécifique du bouton de déconnexion
                document.getElementById('session-logout').addEventListener('click', function() {
                    document.getElementById('logout-form').submit();
                });
            });
        </script>
        @endauth

        <script>
            window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'user' => auth()->check() ? ['id' => auth()->id()] : null,
            ]) !!};
            
            // Routes disponibles pour le JavaScript
            window.appRoutes = {
                contact: "{{ route('contact') }}"
            };
        </script>
        
        {{-- Inclusion des scripts de notifications --}}
        @include('partials.notification-scripts')
        
        @stack('scripts')

        <!-- Script de consentement aux cookies chargé manuellement -->

        @hasSection('scripts')
            @yield('scripts')
        @endif
        <x-cookie-consent />
        
        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Composant de notification pour les téléchargements -->
        @include('components.download-toast')
        
        @stack('scripts')
    </body>
</html>

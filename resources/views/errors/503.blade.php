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

    <title>{{ config('app.name', 'Laravel') }} - Maintenance</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }
        
        .animate-bounce-slow {
            animation: bounce 2s ease-in-out infinite;
        }
        
        .gear-spin-left {
            animation: spin 10s linear infinite;
        }
        
        .gear-spin-right {
            animation: spin 10s linear infinite reverse;
        }
        
        .progress-animation {
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #8b5cf6, #3b82f6, #10b981);
            background-size: 200% 100%;
            animation: gradient 2s linear infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 100% 0; }
            100% { background-position: -100% 0; }
        }
        
        .blob {
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
        }
        
        .blob-animation {
            animation: blob 8s ease-in-out infinite;
        }
        
        @keyframes blob {
            0%, 100% { border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%; }
            25% { border-radius: 58% 42% 75% 25% / 76% 46% 54% 24%; }
            50% { border-radius: 50% 50% 33% 67% / 55% 27% 73% 45%; }
            75% { border-radius: 33% 67% 58% 42% / 63% 68% 32% 37%; }
        }
        
        /* Mode sombre */
        .dark {
            color-scheme: dark;
        }
        
        .dark body {
            background-color: #0f172a;
            color: #f3f4f6;
        }
    </style>
</head>
<body class="{{ request()->cookie('theme', 'light') === 'dark' ? 'dark bg-gray-900' : 'bg-gray-50' }} min-h-screen flex flex-col">
    <div class="flex-grow flex items-center justify-center p-4 md:p-8">
        <div class="max-w-4xl w-full">
            <!-- Bannière supérieure -->
            <div class="relative overflow-hidden rounded-t-2xl {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-purple-900/30 border-purple-800' : 'bg-purple-600' }} text-white p-6 md:p-8 border-b">
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-tools text-white"></i>
                            </div>
                            <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'Planify') }}</span>
                        </div>
                        <div class="text-sm bg-yellow-500/20 px-3 py-1 rounded-full border border-yellow-400/30 flex items-center">
                            <span class="animate-pulse mr-2 h-2 w-2 bg-yellow-400 rounded-full"></span>
                            <span>Maintenance en cours</span>
                        </div>
                    </div>
                </div>
                
                <!-- Formes décoratives -->
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-blue-500/10' : 'bg-blue-500/20' }} blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 h-24 w-24 rounded-full {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-purple-500/10' : 'bg-purple-500/20' }} blur-2xl"></div>
            </div>
            
            <!-- Contenu principal -->
            <div class="{{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200' }} border border-t-0 rounded-b-2xl shadow-lg overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="grid md:grid-cols-2 gap-8 items-center">
                        <!-- Colonne de gauche: Texte -->
                        <div class="space-y-6">
                            <div>
                                <h1 class="text-3xl font-bold {{ request()->cookie('theme', 'light') === 'dark' ? 'text-white' : 'text-gray-900' }} tracking-tight mb-2">
                                    Site en maintenance
                                </h1>
                                <div class="h-1 w-20 bg-gradient-to-r from-purple-600 to-blue-500 rounded-full"></div>
                            </div>
                            
                            <p class="text-lg {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-300' : 'text-gray-600' }}">
                                Nous effectuons actuellement des améliorations pour vous offrir une meilleure expérience. Merci de votre patience.
                            </p>
                            
                            <!-- Barre de progression -->
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-400' : 'text-gray-500' }}">
                                    <span>Progression</span>
                                    <span>Nous travaillons activement</span>
                                </div>
                                <div class="{{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-700' : 'bg-gray-200' }} h-1 rounded-full overflow-hidden">
                                    <div class="progress-animation"></div>
                                </div>
                            </div>
                            
                            <!-- Estimation -->
                            <div class="{{ request()->cookie('theme', 'light') === 'dark' ? 'bg-yellow-900/20 border-yellow-800/30' : 'bg-yellow-50 border-yellow-100' }} border rounded-xl p-4">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-yellow-500/20' : 'bg-yellow-100' }} p-2 rounded-lg">
                                        <i class="fas fa-clock {{ request()->cookie('theme', 'light') === 'dark' ? 'text-yellow-300' : 'text-yellow-600' }}"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="font-medium {{ request()->cookie('theme', 'light') === 'dark' ? 'text-yellow-300' : 'text-yellow-800' }}">Durée estimée</h3>
                                        <p class="mt-1 {{ request()->cookie('theme', 'light') === 'dark' ? 'text-yellow-200/70' : 'text-yellow-700' }} text-sm">
                                            @php
                                                $message = isset($exception) && $exception->getMessage() ? $exception->getMessage() : '';
                                                // Si le message est "Service Unavailable", on affiche un message plus informatif
                                                if (empty($message) || $message === 'Service Unavailable') {
                                                    $message = 'Notre équipe technique travaille activement pour rétablir le service le plus rapidement possible.';
                                                }
                                            @endphp
                                            {{ $message }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contact -->
                            <div class="pt-4 {{ request()->cookie('theme', 'light') === 'dark' ? 'border-t border-gray-700' : 'border-t border-gray-200' }}">
                                <h3 class="font-medium {{ request()->cookie('theme', 'light') === 'dark' ? 'text-white' : 'text-gray-900' }} mb-3">Besoin d'assistance ?</h3>
                                <div class="flex flex-wrap gap-3">
                                    <a href="mailto:support@planify.fr" class="{{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-700 hover:bg-gray-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }} px-4 py-2 rounded-lg flex items-center transition-colors">
                                        <i class="fas fa-envelope mr-2 text-purple-500"></i> support@planify.fr
                                    </a>
                                    <a href="tel:+33123456789" class="{{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-700 hover:bg-gray-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-800' }} px-4 py-2 rounded-lg flex items-center transition-colors">
                                        <i class="fas fa-phone mr-2 text-purple-500"></i> 01 23 45 67 89
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Colonne de droite: Illustration -->
                        <div class="flex justify-center">
                            <div class="relative w-full max-w-sm">
                                <!-- Cercle décoratif -->
                                <div class="absolute inset-0 blob blob-animation {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-purple-900/20' : 'bg-purple-100' }}"></div>
                                
                                <!-- Illustration -->
                                <div class="relative flex justify-center items-center h-64 md:h-80">
                                    <!-- Engrenage principal -->
                                    <div class="absolute gear-spin-right text-8xl {{ request()->cookie('theme', 'light') === 'dark' ? 'text-purple-600/50' : 'text-purple-500/80' }}">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    
                                    <!-- Engrenages secondaires -->
                                    <div class="absolute top-10 right-16 gear-spin-left text-4xl {{ request()->cookie('theme', 'light') === 'dark' ? 'text-blue-500/50' : 'text-blue-500/80' }}">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="absolute bottom-10 left-16 gear-spin-left text-5xl {{ request()->cookie('theme', 'light') === 'dark' ? 'text-indigo-500/50' : 'text-indigo-500/80' }}">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    
                                    <!-- Icône centrale -->
                                    <div class="relative animate-float">
                                        <div class="h-24 w-24 rounded-full flex items-center justify-center {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-800 shadow-lg shadow-purple-900/30' : 'bg-white shadow-lg shadow-purple-300/50' }} border {{ request()->cookie('theme', 'light') === 'dark' ? 'border-gray-700' : 'border-gray-200' }}">
                                            <i class="fas fa-tools text-4xl {{ request()->cookie('theme', 'light') === 'dark' ? 'text-purple-400' : 'text-purple-600' }}"></i>
                                        </div>
                                        
                                        <!-- Petites bulles décoratives -->
                                        <div class="absolute -top-2 -right-2 animate-bounce-slow">
                                            <div class="h-5 w-5 rounded-full {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-blue-600' : 'bg-blue-500' }}"></div>
                                        </div>
                                        <div class="absolute -bottom-1 -left-1 animate-pulse">
                                            <div class="h-3 w-3 rounded-full {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-purple-600' : 'bg-purple-500' }}"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pied de page -->
                <div class="{{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-900/50 border-t border-gray-800' : 'bg-gray-50 border-t border-gray-100' }} px-6 py-4 text-center">
                    <p class="text-sm {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-400' : 'text-gray-500' }}">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Planify') }}. Tous droits réservés.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

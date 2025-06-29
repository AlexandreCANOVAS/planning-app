<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Planify') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'], 'defer')
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- Styles -->
        @stack('styles')
    </head>
    <body class="font-sans antialiased flex flex-col min-h-screen">
        @include('layouts.guest-navigation')

        <div class="flex-grow">
            {{ $slot }}
        </div>

        <footer class="bg-gray-900 py-12 w-full mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <a href="{{ route('welcome') }}">
                                <img src="{{ asset('images/logo.svg') }}" alt="Planify Logo" class="h-12 w-auto">
                            </a>
                        </div>
                        <p class="text-gray-400 mb-4">
                            Simplifiez la gestion de votre entreprise avec notre solution tout-en-un.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-white font-semibold text-lg mb-4">Fonctionnalités</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('features.planning') }}" class="text-gray-400 hover:text-white transition-colors duration-300">Planning</a></li>
                            <li><a href="{{ route('features.conges') }}" class="text-gray-400 hover:text-white transition-colors duration-300">Congés</a></li>
                            <li><a href="{{ route('features.temps') }}" class="text-gray-400 hover:text-white transition-colors duration-300">Suivi du temps</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-white font-semibold text-lg mb-4">Entreprise</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition-colors duration-300">À propos</a></li>
                            <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors duration-300">Contact</a></li>
                            <li><a href="{{ route('pricing') }}" class="text-gray-400 hover:text-white transition-colors duration-300">Tarifs</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h3 class="text-white font-semibold text-lg mb-4">Légal</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Conditions d'utilisation</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Politique de confidentialité</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Mentions légales</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        &copy; {{ date('Y') }} Planify. Tous droits réservés.
                    </p>
                    <div class="mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300 mx-2 text-sm">
                            Français
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300 mx-2 text-sm">
                            English
                        </a>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Script de consentement aux cookies chargé manuellement -->

        <x-cookie-consent />

        @stack('scripts')

    </body>
</html>

<nav x-data="{ open: false, featuresOpen: false }" class="bg-white border-b shadow-sm sticky w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('welcome') }}" class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center mr-2 shadow-md">
                            <span class="text-white font-bold">P</span>
                        </div>
                        <span class="font-semibold text-xl text-gray-800">Planify</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:ml-10 sm:flex items-center">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 ease-in-out text-gray-800 hover:text-purple-600 {{ request()->routeIs('welcome') ? 'border-b-2 border-purple-600' : '' }}">
                        {{ __('Accueil') }}
                    </a>
                    
                    <!-- Dropdown pour Fonctionnalités -->
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 ease-in-out text-gray-800 hover:text-purple-600 {{ request()->routeIs('features.*') ? 'border-b-2 border-purple-600' : '' }}">
                            {{ __('Fonctionnalités') }}
                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                             style="display: none;">
                            <div class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white">
                                <a href="{{ route('features.planning') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-100">
                                    <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                                    {{ __('Planning') }}
                                </a>
                                <a href="{{ route('features.conges') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-100">
                                    <i class="fas fa-umbrella-beach mr-2 text-purple-600"></i>
                                    {{ __('Congés') }}
                                </a>
                                <a href="{{ route('features.temps') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-100">
                                    <i class="fas fa-clock mr-2 text-purple-600"></i>
                                    {{ __('Suivi du temps') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('about') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 ease-in-out text-gray-800 hover:text-purple-600 {{ request()->routeIs('about') ? 'border-b-2 border-purple-600' : '' }}">
                        {{ __('À propos') }}
                    </a>
                    
                    <a href="{{ route('pricing') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 ease-in-out text-gray-800 hover:text-purple-600 {{ request()->routeIs('pricing') ? 'border-b-2 border-purple-600' : '' }}">
                        {{ __('Tarifs') }}
                    </a>
                    
                    <a href="{{ route('contact') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium transition duration-150 ease-in-out text-gray-800 hover:text-purple-600 {{ request()->routeIs('contact') ? 'border-b-2 border-purple-600' : '' }}">
                        {{ __('Contact') }}
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 shadow-md transition-all duration-300">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                {{ __('Tableau de bord') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-purple-600 transition-colors duration-300">
                                {{ __('Connexion') }}
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 shadow-md transition-all duration-300">
                                    {{ __('Inscription') }}
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('welcome') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('welcome') ? 'border-purple-600 text-purple-700 bg-purple-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                {{ __('Accueil') }}
            </a>
            
            <!-- Mobile Dropdown pour Fonctionnalités -->
            <div x-data="{ featuresOpen: false }">
                <button @click="featuresOpen = !featuresOpen" class="w-full text-left block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('features.*') ? 'border-purple-600 text-purple-700 bg-purple-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    <div class="flex justify-between items-center">
                        {{ __('Fonctionnalités') }}
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
                <div x-show="featuresOpen" class="pl-6 pr-4 py-2 space-y-1" style="display: none;">
                    <a href="{{ route('features.planning') }}" class="block py-2 text-sm text-gray-600 hover:text-purple-600">
                        <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                        {{ __('Planning') }}
                    </a>
                    <a href="{{ route('features.conges') }}" class="block py-2 text-sm text-gray-600 hover:text-purple-600">
                        <i class="fas fa-umbrella-beach mr-2 text-purple-600"></i>
                        {{ __('Congés') }}
                    </a>
                    <a href="{{ route('features.temps') }}" class="block py-2 text-sm text-gray-600 hover:text-purple-600">
                        <i class="fas fa-clock mr-2 text-purple-600"></i>
                        {{ __('Suivi du temps') }}
                    </a>
                </div>
            </div>
            
            <a href="{{ route('pricing') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('pricing') ? 'border-purple-600 text-purple-700 bg-purple-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                {{ __('Tarifs') }}
            </a>
            
            <a href="{{ route('about') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('about') ? 'border-purple-600 text-purple-700 bg-purple-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                {{ __('À propos') }}
            </a>
            
            <a href="{{ route('contact') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('contact') ? 'border-purple-600 text-purple-700 bg-purple-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                {{ __('Contact') }}
            </a>
        </div>
        
        @if (Route::has('login'))
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4 space-y-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block text-base font-medium text-purple-600 hover:text-purple-800">
                            {{ __('Tableau de bord') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block text-base font-medium text-gray-600 hover:text-purple-600">
                            {{ __('Connexion') }}
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:border-purple-700 focus:ring focus:ring-purple-200 active:bg-purple-600 disabled:opacity-25 transition">
                                {{ __('S\'inscrire') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        @endif
    </div>
</nav>

<!-- Navbar est maintenant en sticky, pas besoin de padding supplémentaire -->

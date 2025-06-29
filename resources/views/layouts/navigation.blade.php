<nav x-data="{ open: false, activeGroup: null }" class="bg-gradient-to-r from-[rgb(131,44,207)] to-[rgb(141,54,217)] border-b border-purple-700 shadow-md fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition-transform duration-300 hover:scale-105">
                        <img class="h-12 w-auto drop-shadow-md" src="{{ asset('images/logo.svg') }}" alt="Planify">
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:-my-px sm:ml-10 sm:flex items-center">
                    @if(Auth::check() && Auth::user() && Auth::user()->role === 'employe')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                            class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md">
                            <i class="fas fa-chart-line mr-2"></i>
                            {{ __('Tableau de bord') }}
                        </x-nav-link>

                        <x-nav-link :href="route('employe.plannings.index')" :active="request()->routeIs('employe.plannings.*')"
                            class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ __('Plannings') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('employe.echanges.index')" :active="request()->routeIs('employe.echanges.*') || request()->routeIs('employe.plannings.liste-echanges') || request()->routeIs('employe.plannings.comparer')"
                            class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            {{ __('Échanges') }}
                        </x-nav-link>

                        <x-nav-link :href="route('employe.conges.index')" :active="request()->routeIs('employe.conges.*')"
                            class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md">
                            <i class="fas fa-umbrella-beach mr-2"></i>
                            {{ __('Congés') }}
                        </x-nav-link>
                    @else
                        <!-- Groupe Tableau de bord -->
                        <div class="group">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                                class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md">
                                <i class="fas fa-chart-line mr-2"></i>
                                {{ __('Tableau de bord') }}
                            </x-nav-link>
                        </div>

                        <!-- Séparateur vertical -->
                        <div class="h-8 border-l border-purple-400/30 mx-1"></div>

                        <!-- Groupe Gestion RH -->
                        <div class="group relative" x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false">
                            <div class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md cursor-pointer">
                                <i class="fas fa-users mr-2"></i>
                                {{ __('Gestion RH') }}
                                <i class="fas fa-chevron-down ml-1 text-xs transition-transform" :class="{'rotate-180': open}"></i>
                            </div>
                            
                            <!-- Sous-menu -->
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute left-0 mt-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('employes.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 hover:text-purple-800 {{ request()->routeIs('employes.*') ? 'bg-purple-100 text-purple-800' : '' }}">
                                        <i class="fas fa-users mr-2"></i>
                                        {{ __('Employés') }}
                                    </a>
                                    <a href="{{ route('formations.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 hover:text-purple-800 {{ request()->routeIs('formations.*') ? 'bg-purple-100 text-purple-800' : '' }}">
                                        <i class="fas fa-graduation-cap mr-2"></i>
                                        {{ __('Formations') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Groupe Planification -->
                        <div class="group relative" x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false">
                            <div class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md cursor-pointer">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                {{ __('Planification') }}
                                <i class="fas fa-chevron-down ml-1 text-xs transition-transform" :class="{'rotate-180': open}"></i>
                            </div>
                            
                            <!-- Sous-menu -->
                            <div x-show="open" 
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                                class="absolute left-0 mt-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                                style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('plannings.calendar') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 hover:text-purple-800 {{ request()->routeIs('plannings.*') ? 'bg-purple-100 text-purple-800' : '' }}">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        {{ __('Plannings') }}
                                    </a>
                                    <a href="{{ route('conges.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 hover:text-purple-800 {{ request()->routeIs('conges.*') ? 'bg-purple-100 text-purple-800' : '' }}">
                                        <i class="fas fa-umbrella-beach mr-2"></i>
                                        {{ __('Congés') }}
                                    </a>
                                    <a href="{{ route('lieux.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-purple-100 hover:text-purple-800 {{ request()->routeIs('lieux.*') ? 'bg-purple-100 text-purple-800' : '' }}">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        {{ __('Lieux de travail') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Séparateur vertical -->
                        <div class="h-8 border-l border-purple-400/30 mx-1"></div>

                        <!-- Autres liens -->
                        <x-nav-link :href="route('comptabilite.index')" :active="request()->routeIs('comptabilite.*')"
                            class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md">
                            <i class="fas fa-calculator mr-2"></i>
                            {{ __('Comptabilité') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')"
                            class="inline-flex items-center px-3 py-2 mx-1 text-sm font-medium leading-5 transition-all duration-200 ease-in-out text-white hover:text-white hover:bg-purple-600/40 rounded-md">
                            <i class="fas fa-file-alt mr-2"></i>
                            {{ __('Documents') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Notifications Dropdown -->
                <div class="relative mr-3">
                    @include('partials.notifications-dropdown')
                </div>
                
                <!-- Settings Dropdown -->
                <div class="relative">
                    <x-dropdown align="right" width="64">
                        <x-slot name="trigger">
                            <button class="group flex items-center px-3 py-2 border border-purple-400/30 text-sm font-medium rounded-md text-white hover:bg-purple-600/40 focus:outline-none transition-all duration-200 ease-in-out">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-white flex items-center justify-center mr-2 shadow-sm group-hover:shadow transition-all duration-200 ease-in-out">
                                        <i class="fas fa-user text-[rgb(131,44,207)]"></i>
                                    </div>
                                    <div>
                                        @if(Auth::check() && Auth::user())
                                            <div class="font-medium text-white group-hover:text-white">{{ Auth::user()->name }}</div>
                                            <div class="text-xs text-purple-200 group-hover:text-purple-100">{{ Auth::user()->role === 'employe' ? 'Employé' : 'Employeur' }}</div>
                                        @else
                                            <div class="font-medium text-white">Invité</div>
                                            <div class="text-xs text-purple-200">Non connecté</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-1">
                                    <svg class="fill-white h-4 w-4 transition-transform duration-200 group-hover:rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- En-tête du menu -->
                            <div class="px-4 py-3 border-b border-gray-200">
                                @if(Auth::check() && Auth::user())
                                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                @endif
                            </div>
                            
                            <div class="py-1">
                                <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                    <i class="fas fa-user-cog mr-2 text-purple-600"></i>
                                    {{ __('Profil') }}
                                </x-dropdown-link>

                                @if(Auth::check() && Auth::user()->role !== 'employe')
                                <x-dropdown-link :href="route('dashboard')" class="flex items-center">
                                    <i class="fas fa-tachometer-alt mr-2 text-purple-600"></i>
                                    {{ __('Tableau de bord') }}
                                </x-dropdown-link>
                                @endif
                            </div>

                            <div class="border-t border-gray-100 py-1">
                                <!-- Bouton de basculement de thème -->
                                <form method="POST" action="{{ route('theme.toggle') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out flex items-center">
                                        @if(request()->cookie('theme', 'light') === 'dark')
                                            <i class="fas fa-sun mr-2 text-amber-500"></i>
                                            {{ __('Thème clair') }}
                                        @else
                                            <i class="fas fa-moon mr-2 text-indigo-600"></i>
                                            {{ __('Thème sombre') }}
                                        @endif
                                    </button>
                                </form>
                            </div>

                            <div class="border-t border-gray-100 py-1">
                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                    @csrf
                                    <x-dropdown-link href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                                        class="flex items-center text-red-600 hover:text-red-700 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>
                                        {{ __('Déconnexion') }}
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-white hover:bg-purple-600/40 focus:outline-none transition-all duration-200 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-gradient-to-b from-[rgb(131,44,207)] to-[rgb(141,54,217)] shadow-lg">
        <div class="pt-2 pb-3 space-y-0.5">
            @if(Auth::check() && Auth::user() && Auth::user()->role === 'employe')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-chart-line w-6 text-center mr-2"></i>
                    {{ __('Tableau de bord') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('employe.plannings.index')" :active="request()->routeIs('employe.plannings.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-calendar-alt w-6 text-center mr-2"></i>
                    {{ __('Plannings') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('employe.echanges.index')" :active="request()->routeIs('employe.echanges.*') || request()->routeIs('employe.plannings.liste-echanges') || request()->routeIs('employe.plannings.comparer')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-exchange-alt w-6 text-center mr-2"></i>
                    {{ __('Échanges') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('employe.conges.index')" :active="request()->routeIs('employe.conges.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-umbrella-beach w-6 text-center mr-2"></i>
                    {{ __('Congés') }}
                </x-responsive-nav-link>
            @else
                <!-- Tableau de bord -->
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-chart-line w-6 text-center mr-2"></i>
                    {{ __('Tableau de bord') }}
                </x-responsive-nav-link>

                <!-- Séparateur avec titre de section -->
                <div class="px-4 py-2 text-xs font-semibold text-purple-200 bg-purple-700/30 uppercase tracking-wider">
                    Gestion RH
                </div>

                <x-responsive-nav-link :href="route('employes.index')" :active="request()->routeIs('employes.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-users w-6 text-center mr-2"></i>
                    {{ __('Employés') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('formations.index')" :active="request()->routeIs('formations.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-graduation-cap w-6 text-center mr-2"></i>
                    {{ __('Formations') }}
                </x-responsive-nav-link>

                <!-- Séparateur avec titre de section -->
                <div class="px-4 py-2 text-xs font-semibold text-purple-200 bg-purple-700/30 uppercase tracking-wider">
                    Planification
                </div>

                <x-responsive-nav-link :href="route('plannings.calendar')" :active="request()->routeIs('plannings.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-calendar-alt w-6 text-center mr-2"></i>
                    {{ __('Plannings') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('conges.index')" :active="request()->routeIs('conges.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-umbrella-beach w-6 text-center mr-2"></i>
                    {{ __('Congés') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('lieux.index')" :active="request()->routeIs('lieux.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-map-marker-alt w-6 text-center mr-2"></i>
                    {{ __('Lieux de travail') }}
                </x-responsive-nav-link>
                
                <!-- Séparateur avec titre de section -->
                <div class="px-4 py-2 text-xs font-semibold text-purple-200 bg-purple-700/30 uppercase tracking-wider">
                    Administration
                </div>

                <x-responsive-nav-link :href="route('comptabilite.index')" :active="request()->routeIs('comptabilite.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-calculator w-6 text-center mr-2"></i>
                    {{ __('Comptabilité') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-file-alt w-6 text-center mr-2"></i>
                    {{ __('Documents') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-purple-400/30">
            <div class="px-4 py-2 bg-white/10 backdrop-blur-sm">
                @if(Auth::check() && Auth::user())
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-white flex items-center justify-center mr-3 shadow-sm">
                            <i class="fas fa-user text-[rgb(131,44,207)]"></i>
                        </div>
                        <div>
                            <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-purple-200">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                @else
                    <div class="font-medium text-base text-white">Invité</div>
                    <div class="font-medium text-sm text-purple-200">Non connecté</div>
                @endif
            </div>

            <div class="mt-3 space-y-0.5">
                <x-responsive-nav-link :href="route('profile.edit')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-user-cog w-6 text-center mr-2"></i>
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                @if(Auth::check() && Auth::user()->role !== 'employe')
                <x-responsive-nav-link :href="route('dashboard')" 
                    class="flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                    <i class="fas fa-tachometer-alt w-6 text-center mr-2"></i>
                    {{ __('Tableau de bord') }}
                </x-responsive-nav-link>
                @endif

                <!-- Bouton de basculement de thème pour mobile -->
                <form method="POST" action="{{ route('theme.toggle') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-white hover:bg-purple-600/40 transition-all duration-200">
                        @if(request()->cookie('theme', 'light') === 'dark')
                            <i class="fas fa-sun w-6 text-center mr-2 text-amber-300"></i>
                            {{ __('Thème clair') }}
                        @else
                            <i class="fas fa-moon w-6 text-center mr-2 text-indigo-200"></i>
                            {{ __('Thème sombre') }}
                        @endif
                    </button>
                </form>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-red-200 hover:bg-red-600/30 hover:text-white transition-all duration-200">
                        <i class="fas fa-sign-out-alt w-6 text-center mr-2"></i>
                        {{ __('Déconnexion') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Add padding to main content to account for fixed navbar -->
<div class="pt-16"></div>

<style>
/* Active link styles */
.nav-link-active {
    @apply border-b-2 border-white text-white bg-purple-600/40;
}

/* Hover effects for nav links */
.nav-link {
    position: relative;
    @apply text-white hover:text-white transition-all duration-200;
}

.nav-link::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 2px;
    bottom: -2px;
    left: 0;
    background-color: white;
    transform: scaleX(0);
    transform-origin: bottom right;
    transition: transform 0.3s ease-out;
    opacity: 0.7;
}

.nav-link:hover::after {
    transform: scaleX(1);
    transform-origin: bottom left;
}

/* Dropdown menu animations */
.dropdown-menu {
    @apply transform opacity-0 scale-95;
    transition: transform 0.2s ease-out, opacity 0.2s ease-out;
}

.dropdown-menu.show {
    @apply transform opacity-100 scale-100;
}

/* User avatar hover effect */
.user-avatar {
    @apply transition-all duration-200;
}

.user-avatar:hover {
    @apply transform scale-105 shadow-md;
}

/* Active submenu item */
.submenu-active {
    @apply bg-purple-100 text-purple-800;
}

/* Submenu transition */
.submenu-enter {
    @apply transition ease-out duration-200;
}

.submenu-enter-start {
    @apply opacity-0 transform scale-95;
}

.submenu-enter-end {
    @apply opacity-100 transform scale-100;
}

/* Mobile menu styles */
.mobile-nav-active {
    @apply bg-purple-600/40 text-white;
}
</style>

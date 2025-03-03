<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <span class="font-semibold text-xl text-gray-800">Planning App</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @if(Auth::user()->role === 'employe')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-chart-line mr-2"></i>
                            {{ __('Tableau de bord') }}
                        </x-nav-link>

                        <x-nav-link :href="route('employe.plannings.index')" :active="request()->routeIs('employe.plannings.*')"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ __('Plannings') }}
                        </x-nav-link>

                        <x-nav-link :href="route('employe.conges.index')" :active="request()->routeIs('employe.conges.*')"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-umbrella-beach mr-2"></i>
                            {{ __('Congés') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" 
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-chart-line mr-2"></i>
                            {{ __('Tableau de bord') }}
                        </x-nav-link>

                        <x-nav-link :href="route('employes.index')" :active="request()->routeIs('employes.*')"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-users mr-2"></i>
                            {{ __('Employés') }}
                        </x-nav-link>

                        <x-nav-link :href="route('formations.index')" :active="request()->routeIs('formations.*')"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            {{ __('Formations') }}
                        </x-nav-link>

                        <x-nav-link :href="route('plannings.calendar')" :active="request()->routeIs('plannings.*')"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ __('Plannings') }}
                        </x-nav-link>

                        <x-nav-link :href="route('comptabilite.index')" :active="request()->routeIs('comptabilite.*')"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-calculator mr-2"></i>
                            {{ __('Comptabilité') }}
                        </x-nav-link>

                        <x-nav-link :href="route('conges.index')" :active="request()->routeIs('conges.*')"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-umbrella-beach mr-2"></i>
                            {{ __('Congés') }}
                        </x-nav-link>

                        <x-nav-link :href="route('lieux.index')" :active="request()->routeIs('lieux.*')"
                            class="inline-flex items-center px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            {{ __('Lieux de travail') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Settings Dropdown -->
                <div class="ml-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                                    </div>
                                </div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                <i class="fas fa-user-cog mr-2"></i>
                                {{ __('Profil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                    this.closest('form').submit();" class="flex items-center text-red-600 hover:text-red-700 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(Auth::user()->role === 'employe')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    {{ __('Tableau de bord') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('employe.plannings.index')" :active="request()->routeIs('employe.plannings.*')" class="flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ __('Plannings') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('employe.conges.index')" :active="request()->routeIs('employe.conges.*')" class="flex items-center">
                    <i class="fas fa-umbrella-beach mr-2"></i>
                    {{ __('Congés') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    {{ __('Tableau de bord') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('employes.index')" :active="request()->routeIs('employes.*')" class="flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    {{ __('Employés') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('formations.index')" :active="request()->routeIs('formations.*')" class="flex items-center">
                    <i class="fas fa-graduation-cap mr-2"></i>
                    {{ __('Formations') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('plannings.calendar')" :active="request()->routeIs('plannings.*')" class="flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ __('Plannings') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('comptabilite.index')" :active="request()->routeIs('comptabilite.*')" class="flex items-center">
                    <i class="fas fa-calculator mr-2"></i>
                    {{ __('Comptabilité') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('conges.index')" :active="request()->routeIs('conges.*')" class="flex items-center">
                    <i class="fas fa-umbrella-beach mr-2"></i>
                    {{ __('Congés') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('lieux.index')" :active="request()->routeIs('lieux.*')" class="flex items-center">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    {{ __('Lieux de travail') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="flex items-center">
                    <i class="fas fa-user-cog mr-2"></i>
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                            this.closest('form').submit();" class="flex items-center text-red-600">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
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
    @apply border-b-2 border-blue-500 text-blue-600;
}

/* Hover effects for nav links */
.nav-link {
    position: relative;
    @apply text-gray-500 hover:text-blue-600 transition-colors duration-200;
}

.nav-link::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: theme('colors.blue.500');
    transform: scaleX(0);
    transform-origin: bottom right;
    transition: transform 0.3s ease-out;
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
    @apply transition-transform duration-200;
}

.user-avatar:hover {
    @apply transform scale-105;
}
</style>

<x-app-layout>
@push('scripts')
<script>
    window.societeId = {{ auth()->user()->societe_id }};
</script>
<script src="{{ asset('js/toast.js') }}"></script>
<script src="{{ asset('js/soldes-realtime-update.js') }}"></script>
{{-- Références aux scripts JavaScript supprimées pour éviter les erreurs 404 --}}
@endpush
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] rounded-xl shadow-lg p-6">
            <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                <div class="w-48 h-48 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="absolute bottom-0 left-0 transform -translate-x-1/3 translate-y-1/3">
                <div class="w-32 h-32 rounded-full bg-white opacity-10"></div>
            </div>
            
            <div class="relative flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-1">
                        {{ __('Tableau de bord') }}
                    </h2>
                    <p class="text-purple-100 text-sm">
                        Bienvenue sur votre espace de gestion
                    </p>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="bg-white bg-opacity-10 rounded-lg px-4 py-2 backdrop-blur-sm">
                        <div class="text-sm text-purple-100">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span id="currentDate">{{ now()->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-10 rounded-lg px-4 py-2 backdrop-blur-sm">
                        <div class="text-sm text-purple-100">
                            <i class="fas fa-clock mr-2"></i>
                            <span id="currentTime"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->societe)
                <!-- En-tête société -->
                <div class="bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] rounded-xl shadow-lg mb-6 overflow-hidden">
                    <div class="relative px-8 py-10">
                        <!-- Cercles décoratifs -->
                        <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                            <div class="w-72 h-72 rounded-full bg-white opacity-5"></div>
                        </div>
                        <div class="absolute bottom-0 left-0 transform -translate-x-1/3 translate-y-1/3">
                            <div class="w-56 h-56 rounded-full bg-white opacity-5"></div>
                        </div>

                        <!-- Contenu -->
                        <div class="relative flex justify-between items-center">
                            <div class="flex items-center space-x-6">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-white bg-opacity-10 rounded-xl flex items-center justify-center shadow-inner overflow-hidden relative group">
                                        @if(file_exists(public_path('images/company/logo.svg')))
                                            <img src="{{ asset('images/company/logo.svg') }}" 
                                                 alt="{{ auth()->user()->societe->nom }}" 
                                                 class="w-12 h-12 object-contain">
                                        @else
                                            <i class="fas fa-building text-white text-2xl"></i>
                                        @endif
                                        
                                        <!-- Bouton pour changer le logo -->
                                        <form action="{{ route('societe.upload-logo') }}" method="POST" enctype="multipart/form-data" class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                            @csrf
                                            <label class="cursor-pointer text-white text-xs text-center p-2">
                                                <i class="fas fa-camera mb-1"></i><br>
                                                Changer
                                                <input type="file" name="logo" class="hidden" onchange="this.form.submit()" accept="image/*">
                                            </label>
                                        </form>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-white mb-2">{{ auth()->user()->societe->nom }}</h1>
                                    <div class="space-y-2">
                                        <div class="flex items-center text-purple-100 text-sm bg-black bg-opacity-20 rounded-full px-4 py-1.5">
                                            <i class="fas fa-envelope mr-2"></i>
                                            <span>{{ auth()->user()->email }}</span>
                                        </div>
                                        
                                        <div class="flex space-x-4">
                                            <div class="flex items-center text-purple-100 text-sm bg-black bg-opacity-20 rounded-full px-4 py-1.5">
                                                <i class="fas fa-building mr-2"></i>
                                                <span>{{ auth()->user()->societe->forme_juridique }}</span>
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-id-card mr-2"></i>
                                                <span>{{ auth()->user()->societe->siret }}</span>
                                            </div>
                                        </div>

                                        <div class="flex space-x-4">
                                            <div class="flex items-center text-purple-100 text-sm bg-black bg-opacity-20 rounded-full px-4 py-1.5">
                                                <i class="fas fa-map-marker-alt mr-2"></i>
                                                <span>{{ auth()->user()->societe->adresse }}</span>
                                            </div>
                                        </div>

                                        @if(auth()->user()->societe->telephone)
                                        <div class="flex space-x-4">
                                            <div class="flex items-center text-purple-100 text-sm bg-black bg-opacity-20 rounded-full px-4 py-1.5">
                                                <i class="fas fa-phone mr-2"></i>
                                                <span>{{ auth()->user()->societe->telephone }}</span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <a href="{{ route('societes.edit', auth()->user()->societe) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-white bg-opacity-10 hover:bg-opacity-20 rounded-lg text-sm text-white transition-all duration-200 border border-white border-opacity-20 hover:border-opacity-30 shadow-lg">
                                    <i class="fas fa-cog mr-2"></i>
                                    Paramètres de l'entreprise
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques principales -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 mr-4">
                                <i class="fas fa-users text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Employés</p>
                                <p class="text-2xl font-bold">{{ auth()->user()->societe->employes->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 mr-4">
                                <i class="fas fa-map-marker-alt text-green-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Lieux de travail</p>
                                <p class="text-2xl font-bold">{{ auth()->user()->societe->lieux->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 mr-4">
                                <i class="fas fa-calendar-alt text-purple-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Plannings actifs</p>
                                <p class="text-2xl font-bold">{{ auth()->user()->societe->plannings()->where('date', '<=', now())->where('date', '>=', now())->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500 relative">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 mr-4 relative">
                                <i class="fas fa-calendar-check text-yellow-500"></i>
                                <span id="notification-bubble" class="absolute -top-1 -right-1 bg-red-500 w-3 h-3 rounded-full transform transition-all duration-300 animate-pulse {{ ($stats['conges_en_attente'] ?? 0) > 0 ? 'scale-100 opacity-100' : 'scale-0 opacity-0' }}"></span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Demandes de congés</p>
                                <p class="text-2xl font-bold" id="conges-counter">{{ $stats['conges_en_attente'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides et Activité récente -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Actions rapides -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Actions rapides</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('employes.create') }}" class="group relative bg-white hover:bg-gray-50 rounded-xl border border-gray-200 p-5 flex items-start space-x-4 transition-all duration-200 hover:shadow-md">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-lg bg-blue-50 group-hover:bg-blue-100 transition-colors duration-200">
                                        <i class="fas fa-user-plus text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-medium text-gray-900 mb-1">Ajouter un employé</h4>
                                    <p class="text-sm text-gray-500">Créer un nouveau compte employé</p>
                                </div>
                                <div class="absolute top-1/2 right-4 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="fas fa-arrow-right text-blue-600"></i>
                                </div>
                            </a>

                            <a href="{{ route('lieux.create') }}" class="group relative bg-white hover:bg-gray-50 rounded-xl border border-gray-200 p-5 flex items-start space-x-4 transition-all duration-200 hover:shadow-md">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-lg bg-green-50 group-hover:bg-green-100 transition-colors duration-200">
                                        <i class="fas fa-map-marker-alt text-green-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-medium text-gray-900 mb-1">Nouveau lieu</h4>
                                    <p class="text-sm text-gray-500">Ajouter un lieu de travail</p>
                                </div>
                                <div class="absolute top-1/2 right-4 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="fas fa-arrow-right text-green-600"></i>
                                </div>
                            </a>

                            <a href="{{ route('fiches-paie.index') }}" class="group relative bg-white hover:bg-gray-50 rounded-xl border border-gray-200 p-5 flex items-start space-x-4 transition-all duration-200 hover:shadow-md">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-lg bg-indigo-50 group-hover:bg-indigo-100 transition-colors duration-200">
                                        <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-medium text-gray-900 mb-1">Fiches de paie</h4>
                                    <p class="text-sm text-gray-500">Gérer les fiches de paie</p>
                                </div>
                                <div class="absolute top-1/2 right-4 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="fas fa-arrow-right text-indigo-600"></i>
                                </div>
                            </a>

                            <a href="{{ route('plannings.calendar') }}" class="group relative bg-white hover:bg-gray-50 rounded-xl border border-gray-200 p-5 flex items-start space-x-4 transition-all duration-200 hover:shadow-md">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-lg bg-purple-50 group-hover:bg-purple-100 transition-colors duration-200">
                                        <i class="fas fa-calendar-plus text-purple-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-medium text-gray-900 mb-1">Créer un planning</h4>
                                    <p class="text-sm text-gray-500">Gérer les plannings des employés</p>
                                </div>
                                <div class="absolute top-1/2 right-4 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="fas fa-arrow-right text-purple-600"></i>
                                </div>
                            </a>

                            <a href="{{ route('conges.index') }}" class="group relative bg-white hover:bg-gray-50 rounded-xl border border-gray-200 p-5 flex items-start space-x-4 transition-all duration-200 hover:shadow-md">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-lg bg-yellow-50 group-hover:bg-yellow-100 transition-colors duration-200">
                                        <i class="fas fa-calendar-check text-yellow-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-medium text-gray-900 mb-1">Gérer les congés</h4>
                                    <p class="text-sm text-gray-500">Traiter les demandes de congés</p>
                                </div>
                                <div class="absolute top-1/2 right-4 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="fas fa-arrow-right text-yellow-600"></i>
                                </div>
                            </a>

                            <a href="{{ route('comptabilite.index') }}" class="group relative bg-white hover:bg-gray-50 rounded-xl border border-gray-200 p-5 flex items-start space-x-4 transition-all duration-200 hover:shadow-md">
                                <div class="flex-shrink-0">
                                    <div class="p-3 rounded-lg bg-red-50 group-hover:bg-red-100 transition-colors duration-200">
                                        <i class="fas fa-chart-line text-red-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base font-medium text-gray-900 mb-1">Comptabilité</h4>
                                    <p class="text-sm text-gray-500">Suivi des heures et statistiques</p>
                                </div>
                                <div class="absolute top-1/2 right-4 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <i class="fas fa-arrow-right text-red-600"></i>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Vision Sécurité Privée - Statut des employés -->
                    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-users text-blue-500 mr-2"></i>
                            Statut de mes employés
                        </h3>
                        
                        <!-- Navigation par onglets -->
                        <div class="border-b border-gray-200 mb-4">
                            <ul class="flex flex-wrap -mb-px" id="statusTabs" role="tablist">
                                <li class="mr-2" role="presentation">
                                    <button class="inline-block p-3 border-b-2 border-blue-500 rounded-t-lg text-blue-600 active" 
                                            id="en-service-tab" 
                                            data-tabs-target="#en-service" 
                                            type="button" 
                                            role="tab" 
                                            aria-controls="en-service" 
                                            aria-selected="true">
                                        <i class="fas fa-user-clock mr-2"></i>
                                        En service <span class="ml-1 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">{{ $employesEnService->count() }}</span>
                                    </button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="inline-block p-3 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                                            id="en-repos-tab" 
                                            data-tabs-target="#en-repos" 
                                            type="button" 
                                            role="tab" 
                                            aria-controls="en-repos" 
                                            aria-selected="false">
                                        <i class="fas fa-coffee mr-2"></i>
                                        En repos <span class="ml-1 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">{{ $employesEnRepos->count() }}</span>
                                    </button>
                                </li>
                                <li class="mr-2" role="presentation">
                                    <button class="inline-block p-3 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                                            id="en-conge-tab" 
                                            data-tabs-target="#en-conge" 
                                            type="button" 
                                            role="tab" 
                                            aria-controls="en-conge" 
                                            aria-selected="false">
                                        <i class="fas fa-umbrella-beach mr-2"></i>
                                        En congé <span class="ml-1 text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full">{{ $employesEnConge->count() }}</span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- Contenu des onglets -->
                        <div id="statusTabContent">
                            <!-- Employés en service -->
                            <div class="block" id="en-service" role="tabpanel" aria-labelledby="en-service-tab">
                                @if($employesEnService->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($employesEnService as $data)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-100">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <i class="fas fa-user text-blue-500"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $data['employe']->nom }} {{ $data['employe']->prenom }}</div>
                                                        @if($data['lieu'])
                                                            <div class="text-xs text-gray-500">
                                                                <i class="fas fa-map-marker-alt mr-1"></i>
                                                                {{ $data['lieu']->nom }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-xs text-gray-600">
                                                    @foreach($data['heures'] as $horaire)
                                                        <div>{{ \Carbon\Carbon::parse($horaire['debut'])->format('H:i') }} - {{ \Carbon\Carbon::parse($horaire['fin'])->format('H:i') }}</div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-gray-500 bg-gray-50 rounded-lg border border-gray-100">
                                        <i class="fas fa-coffee text-gray-400 text-xl mb-2"></i>
                                        <p class="text-sm">Aucun employé en service aujourd'hui</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Employés en repos -->
                            <div class="hidden" id="en-repos" role="tabpanel" aria-labelledby="en-repos-tab">
                                @if($employesEnRepos->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($employesEnRepos as $employe)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-100">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                                        <i class="fas fa-user text-green-500"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $employe->nom }} {{ $employe->prenom }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            <i class="fas fa-phone mr-1"></i>
                                                            {{ $employe->telephone ?? 'Non renseigné' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">En repos</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-gray-500 bg-gray-50 rounded-lg border border-gray-100">
                                        <i class="fas fa-user-clock text-gray-400 text-xl mb-2"></i>
                                        <p class="text-sm">Tous les employés sont en service ou en congé aujourd'hui</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Employés en congé -->
                            <div class="hidden" id="en-conge" role="tabpanel" aria-labelledby="en-conge-tab">
                                @if($employesEnConge->count() > 0)
                                    <div class="space-y-2">
                                        @foreach($employesEnConge as $employe)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-100">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                                        <i class="fas fa-user text-yellow-500"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $employe->nom }} {{ $employe->prenom }}</div>
                                                        <div class="text-xs text-gray-500">
                                                            <i class="fas fa-envelope mr-1"></i>
                                                            {{ $employe->email ?? 'Non renseigné' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">En congé</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4 text-gray-500 bg-gray-50 rounded-lg border border-gray-100">
                                        <i class="fas fa-umbrella-beach text-gray-400 text-xl mb-2"></i>
                                        <p class="text-sm">Aucun employé en congé aujourd'hui</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @include('dashboard.partials.documents-exports')



            @else
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="max-w-md mx-auto">
                        <i class="fas fa-building text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Bienvenue sur votre espace employeur</h3>
                        <p class="text-gray-500 mb-6">Pour commencer, créez votre société et commencez à gérer vos employés et plannings.</p>
                        <a href="{{ route('societes.create') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Créer ma société
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const societeId = {{ auth()->user()->societe_id }};
            const initialCount = {{ $stats['conges_en_attente'] ?? 0 }};
            
            function updateNotificationBubble(count) {
                const notificationBubble = document.querySelector('#notification-bubble');
                const congesCounter = document.querySelector('#conges-counter');
                
                if (notificationBubble && congesCounter) {
                    congesCounter.textContent = count;
                    
                    if (count > 0) {
                        notificationBubble.classList.remove('scale-0', 'opacity-0');
                        notificationBubble.classList.add('scale-100', 'opacity-100');
                    } else {
                        notificationBubble.classList.add('scale-0', 'opacity-0');
                        notificationBubble.classList.remove('scale-100', 'opacity-100');
                    }
                }
            }
            
            // Fonction pour mettre à jour les soldes de congés d'un employé
            function updateEmployeSoldes(employeId, soldeConges, soldeRtt, soldeExceptionnels) {
                console.log(`Mise à jour des soldes pour l'employé ${employeId}:`, {
                    soldeConges, soldeRtt, soldeExceptionnels
                });
                
                // Trouver tous les éléments qui affichent les soldes de cet employé
                document.querySelectorAll(`[data-employe-id="${employeId}"]`).forEach(el => {
                    const type = el.getAttribute('data-solde-type');
                    if (type === 'conges') {
                        el.textContent = parseFloat(soldeConges).toFixed(1);
                    } else if (type === 'rtt') {
                        el.textContent = parseFloat(soldeRtt).toFixed(1);
                    } else if (type === 'exceptionnels') {
                        el.textContent = parseFloat(soldeExceptionnels).toFixed(1);
                    }
                });
                
                // Afficher une notification toast si disponible
                if (window.showToast) {
                    window.showToast(`Les soldes de congés ont été mis à jour`, 'purple');
                }
            }
            
            // Initialize bubble on page load
            updateNotificationBubble(initialCount);
            
            window.Echo.private(`societe.${societeId}`)
                .listen('.CongeRequested', (e) => {
                    updateNotificationBubble(e.congesEnAttente);
                })
                .listen('.CongeStatusUpdated', (e) => {
                    updateNotificationBubble(e.congesEnAttente);
                })
                .listen('.soldes.updated', (e) => {
                    console.log('Evénement soldes.updated reçu:', e);
                    updateEmployeSoldes(
                        e.employe_id,
                        e.solde_conges,
                        e.solde_rtt,
                        e.solde_conges_exceptionnels
                    );
                })
                .error((error) => {
                    console.error('Echo error:', error);
                });
                
            // Gestion des onglets pour la section "Vision Sécurité Privée"
            const tabsElement = document.getElementById('statusTabs');
            if (tabsElement) {
                const tabs = tabsElement.querySelectorAll('button[role="tab"]');
                const tabPanels = document.querySelectorAll('#statusTabContent > div[role="tabpanel"]');
                
                tabs.forEach(tab => {
                    tab.addEventListener('click', () => {
                        // Désactiver tous les onglets
                        tabs.forEach(t => {
                            t.classList.remove('border-blue-500', 'text-blue-600', 'active');
                            t.classList.add('border-transparent');
                            t.setAttribute('aria-selected', 'false');
                        });
                        
                        // Cacher tous les panneaux
                        tabPanels.forEach(panel => {
                            panel.classList.add('hidden');
                            panel.classList.remove('block');
                        });
                        
                        // Activer l'onglet cliqué
                        tab.classList.remove('border-transparent');
                        tab.classList.add('border-blue-500', 'text-blue-600', 'active');
                        tab.setAttribute('aria-selected', 'true');
                        
                        // Afficher le panneau correspondant
                        const target = document.querySelector(tab.getAttribute('data-tabs-target'));
                        if (target) {
                            target.classList.remove('hidden');
                            target.classList.add('block');
                        }
                    });
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateTime() {
                const now = luxon.DateTime.local().setLocale('fr');
                document.getElementById('currentTime').textContent = now.toFormat('HH:mm:ss');
                document.getElementById('currentDate').textContent = now.toFormat('dd/MM/yyyy');
            }
            
            // Update immediately and then every second
            updateTime();
            setInterval(updateTime, 1000);
        });
    </script>
    @endpush
</x-app-layout>
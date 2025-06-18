<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section d'accueil -->
            <div class="mb-8">
                <div class="relative overflow-hidden bg-gradient-to-r from-[rgb(75,20,140)] to-[rgb(55,10,110)] rounded-xl shadow-lg p-6">
                    <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                        <div class="w-48 h-48 rounded-full bg-white opacity-10"></div>
                    </div>
                    <div class="absolute bottom-0 left-0 transform -translate-x-1/3 translate-y-1/3">
                        <div class="w-32 h-32 rounded-full bg-white opacity-10"></div>
                    </div>
                    
                    <div class="relative flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if($employe->photo)
                                <img src="{{ asset('storage/' . $employe->photo) }}" alt="{{ $employe->nom }}" class="w-16 h-16 rounded-full object-cover border-2 border-white border-opacity-30">
                            @else
                                <div class="w-16 h-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                                    <span class="text-white text-xl font-semibold">{{ substr($employe->prenom, 0, 1) }}{{ substr($employe->nom, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-1">
                                Bienvenue sur votre tableau de bord, {{ $employe->prenom }}
                            </h1>
                            <p class="text-purple-100 text-sm">
                                {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }} • {{ $employe->poste ?? 'Employé' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Statistiques -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Heures travaillées -->
                <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Heures cette semaine</div>
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['heures_semaine'], 2) }}h</div>
                    </div>
                </div>

                <!-- Congés restants -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-medium text-gray-700">Soldes de congés</div>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="p-2 bg-blue-50/70 rounded-lg border border-blue-100">
                            <div class="text-xs text-blue-600 mb-1">Congés payés</div>
                            <div class="text-lg font-bold text-blue-800 solde-conges-value">{{ number_format($employe->solde_conges, 1) }}</div>
                        </div>
                        <div class="p-2 bg-green-50/70 rounded-lg border border-green-100">
                            <div class="text-xs text-green-600 mb-1">RTT</div>
                            <div class="text-lg font-bold text-green-800 solde-rtt-value">{{ number_format($employe->solde_rtt, 1) }}</div>
                        </div>
                        <div class="p-2 bg-amber-50/70 rounded-lg border border-amber-100">
                            <div class="text-xs text-amber-600 mb-1">Exceptionnels</div>
                            <div class="text-lg font-bold text-amber-800 solde-exceptionnels-value">{{ number_format($employe->solde_conges_exceptionnels, 1) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Plannings actifs -->
                <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Prochain service</div>
                        <div class="text-xl font-bold text-gray-900">
                            @if($stats['prochain_planning'])
                                {{ \Carbon\Carbon::parse($stats['prochain_planning']->date)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Lieu de travail -->
                <div class="bg-white rounded-xl shadow-sm p-6 flex items-center space-x-4">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Lieu actuel</div>
                        <div class="text-xl font-bold text-gray-900">{{ $stats['prochain_planning']->lieu->nom ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides et Activité récente -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Actions rapides -->
                <div>
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('employe.plannings.index') }}" class="bg-white rounded-xl shadow-sm p-4 flex items-start space-x-4 hover:bg-gray-50 transition">
                            <div class="p-3 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Voir mon planning</div>
                                <div class="text-sm text-gray-500">Consultez vos horaires</div>
                            </div>
                        </a>

                        <a href="{{ route('employe.conges.index') }}" class="bg-white rounded-xl shadow-sm p-4 flex items-start space-x-4 hover:bg-gray-50 transition">
                            <div class="p-3 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Demander un congé</div>
                                <div class="text-sm text-gray-500">Nouvelle demande</div>
                            </div>
                        </a>

                        <div class="bg-white rounded-xl shadow-sm p-4 flex items-start space-x-4">
                            <div class="p-3 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Mes documents</div>
                                <div class="text-sm text-gray-500">Bientôt disponible</div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm p-4 flex items-start space-x-4">
                            <div class="p-3 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Mon profil</div>
                                <div class="text-sm text-gray-500">Bientôt disponible</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Centre de notifications -->
                <div x-data="notificationCenterInline()" x-init="init()">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center">
                            <h2 class="text-lg font-medium text-gray-900">Centre de notifications</h2>
                            <template x-if="unreadCount > 0">
                                <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                    <span x-text="unreadCount"></span>
                                </span>
                            </template>
                        </div>
                        <template x-if="unreadCount > 0">
                            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST" class="flex">
                                @csrf
                                <button type="submit" class="text-xs text-[rgb(131,44,207)] hover:text-[rgb(151,64,227)] flex items-center border border-[rgb(131,44,207)] rounded-md px-2 py-1">
                                    <i class="fas fa-check-double mr-1"></i> Tout marquer comme lu
                                </button>
                            </form>
                        </template>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="max-h-[400px] overflow-y-auto">
                            <template x-if="notifications.length > 0">
                                <div class="divide-y divide-gray-200">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div class="p-4 hover:bg-gray-50/80 border-l-4" :class="{
                                            'border-l-purple-500 bg-purple-50/10': !notification.read_at && notification.data.title && notification.data.title.includes('Modification de votre solde de congés'),
                                            'border-l-blue-500 bg-blue-50/10': !notification.read_at && notification.data.type === 'conge_status_changed',
                                            'border-l-amber-500 bg-amber-50/10': !notification.read_at && (notification.data.type === 'planning_created' || notification.data.type === 'planning_updated'),
                                            'border-l-green-500 bg-green-50/10': !notification.read_at && !notification.data.title?.includes('Modification de votre solde de congés') && notification.data.type !== 'conge_status_changed' && notification.data.type !== 'planning_created' && notification.data.type !== 'planning_updated',
                                            'border-l-transparent': notification.read_at
                                        }">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="h-10 w-10 rounded-full flex items-center justify-center border" :class="`bg-${notification.data.color || 'purple'}-50 border-${notification.data.color || 'purple'}-200`">
                                                        <i class="fas" :class="`${notification.data.icon || 'fa-bell'} text-${notification.data.color || 'purple'}-600`"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex justify-between items-start">
                                                        <p class="text-sm font-medium text-gray-900" x-text="notification.data.title || 'Notification'"></p>
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-xs text-gray-500" x-text="formatDate(notification.created_at)"></span>
                                                            <template x-if="!notification.read_at">
                                                                <span class="inline-flex h-2 w-2 rounded-full bg-red-600"></span>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-600" x-text="notification.data.message"></p>
                                                    
                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                        <form :action="`/notifications/${notification.id}/mark-as-read`" method="POST" x-show="!notification.read_at">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                                <i class="fas fa-check mr-1"></i>Marquer comme lu
                                                            </button>
                                                        </form>
                                                        
                                                        <template x-if="notification.data.type === 'conge_created'">
                                                            <a :href="`/employeur/conges/${notification.data.id}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                                <i class="fas fa-eye mr-1"></i>Voir la demande
                                                            </a>
                                                        </template>
                                                        
                                                        <template x-if="notification.data.type === 'conge_status_changed'">
                                                            <a :href="`/employe/conges/${notification.data.id}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                                <i class="fas fa-eye mr-1"></i>Voir le congé
                                                            </a>
                                                        </template>
                                                        
                                                        <template x-if="notification.data.type === 'planning_created' || notification.data.type === 'planning_updated'">
                                                            <a :href="`/employe/plannings/calendar?mois=${notification.data.mois || new Date().getMonth() + 1}&annee=${notification.data.annee || new Date().getFullYear()}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                                <i class="fas fa-calendar-alt mr-1"></i>Voir le planning
                                                            </a>
                                                        </template>
                                                        
                                                        <!-- Notification de modification de solde de congés -->
                                                        <template x-if="notification.data.title && notification.data.title.includes('Modification de votre solde de congés')">
                                                            <div class="mt-2">
                                                                <div class="text-xs text-gray-600 mb-1">
                                                                    <span class="font-medium">Modifié par:</span> <span x-text="notification.data.user_name || 'Système'"></span>
                                                                </div>
                                                                <template x-if="notification.data.commentaire">
                                                                    <div class="text-xs italic text-gray-500 mb-2" x-text="notification.data.commentaire"></div>
                                                                </template>
                                                                <a href="/dashboard" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                                    <i class="fas fa-info-circle mr-1"></i>Voir les détails
                                                                </a>
                                                            </div>
                                                        </template>
                                                        
                                                        <template x-if="notification.data.type === 'exchange_request' || notification.data.type === 'exchange_accepted' || notification.data.type === 'exchange_status_changed'">
                                                            <a :href="`/employe/echanges/${notification.data.id}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                                <i class="fas fa-exchange-alt mr-1"></i>Voir l'échange
                                                            </a>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            
                            <template x-if="notifications.length === 0">
                                <div class="p-8 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-purple-100">
                                        <i class="fas fa-bell text-purple-600 text-xl"></i>
                                    </div>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Aucune notification</h3>
                                    <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore reçu de notifications.</p>
                                </div>
                            </template>
                        </div>
                        
                        <div class="border-t border-gray-200 p-3 text-center">
                            <a href="{{ route('notifications.index') }}" class="text-sm text-[rgb(131,44,207)] hover:text-[rgb(151,64,227)] font-medium">
                                Voir toutes les notifications
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendrier d'équipe -->
            @include('dashboard.partials.team-calendar')

            <!-- Documents et Exports -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Documents et Exports</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Planning mensuel -->
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="mb-4">
                            <div class="p-3 bg-blue-100 rounded-lg w-fit">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Planning mensuel</h3>
                        <p class="text-sm text-gray-500 mb-4">Exportez votre planning au format PDF</p>
                        <form action="{{ route('employe.plannings.download-pdf') }}" method="GET" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mois :</label>
                                <select name="mois" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $m, 1)->locale('fr')->monthName }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Année :</label>
                                <select name="annee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Télécharger
                            </button>
                        </form>
                    </div>

                    <!-- Alertes importantes -->
                    <div class="bg-white rounded-xl shadow-sm p-6" x-data="{ activeTab: 'plannings' }">
                        <div class="mb-4">
                            <div class="p-3 bg-red-100 rounded-lg w-fit">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Alertes importantes</h3>
                        <p class="text-sm text-gray-500 mb-4">Modifications récentes et éléments à suivre</p>
                        
                        <!-- Onglets -->
                        <div class="border-b border-gray-200 mb-4">
                            <nav class="-mb-px flex space-x-4">
                                <button @click="activeTab = 'plannings'" :class="{'border-b-2 border-[rgb(131,44,207)] text-[rgb(131,44,207)]': activeTab === 'plannings', 'text-gray-500 hover:text-gray-700': activeTab !== 'plannings'}" class="py-2 px-1 text-sm font-medium">
                                    <i class="fas fa-calendar-alt mr-1"></i>Plannings modifiés
                                </button>
                                <button @click="activeTab = 'conges'" :class="{'border-b-2 border-[rgb(131,44,207)] text-[rgb(131,44,207)]': activeTab === 'conges', 'text-gray-500 hover:text-gray-700': activeTab !== 'conges'}" class="py-2 px-1 text-sm font-medium">
                                    <i class="fas fa-clock mr-1"></i>Congés à suivre
                                </button>
                            </nav>
                        </div>
                        
                        <!-- Contenu des onglets -->
                        <div>
                            <!-- Plannings modifiés -->
                            <div x-show="activeTab === 'plannings'" class="space-y-3">
                                @forelse($plannings->take(3) as $planning)
                                    <div class="flex items-center justify-between border-l-4 border-amber-500 bg-amber-50 pl-3 pr-4 py-2 rounded-r-md">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }} à {{ $planning->lieu->nom }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') }}
                                            </p>
                                        </div>
                                        <a href="{{ route('employe.plannings.show', $planning) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-[rgb(131,44,207)] hover:bg-purple-50">
                                            <i class="fas fa-eye mr-1"></i>Voir
                                        </a>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <p class="text-sm text-gray-500">Aucune modification récente de planning</p>
                                    </div>
                                @endforelse
                                
                                <div class="text-center pt-2">
                                    <a href="{{ route('employe.plannings.calendar') }}" class="text-sm text-[rgb(131,44,207)] hover:text-[rgb(151,64,227)] font-medium">
                                        Voir tous mes plannings
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Congés à suivre -->
                            <div x-show="activeTab === 'conges'" class="space-y-3">
                                <!-- Modifications récentes des soldes de congés -->
                                @if($employe->historiqueConges->count() > 0)
                                    <div class="border-l-4 border-purple-500 bg-purple-50/70 pl-3 pr-4 py-2 rounded-r-md">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    Modification de vos soldes de congés
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $employe->historiqueConges->first()->created_at->format('d/m/Y à H:i') }} par {{ $employe->historiqueConges->first()->user->name ?? 'Système' }}
                                                </p>
                                            </div>
                                            <a href="{{ route('solde.historique', $employe->id) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-[rgb(131,44,207)] hover:bg-purple-50">
                                                <i class="fas fa-history mr-1"></i>Historique
                                            </a>
                                        </div>
                                        <div class="mt-2 grid grid-cols-3 gap-2">
                                            @php
                                                $lastHistorique = $employe->historiqueConges->first();
                                            @endphp
                                            <div class="p-1 bg-blue-50/50 rounded border border-blue-100 text-xs">
                                                <span class="text-blue-600">Congés payés:</span> 
                                                <span class="font-medium">{{ $lastHistorique->ancien_solde_conges }} → {{ $lastHistorique->nouveau_solde_conges }}</span>
                                            </div>
                                            <div class="p-1 bg-green-50/50 rounded border border-green-100 text-xs">
                                                <span class="text-green-600">RTT:</span> 
                                                <span class="font-medium">{{ $lastHistorique->ancien_solde_rtt }} → {{ $lastHistorique->nouveau_solde_rtt }}</span>
                                            </div>
                                            <div class="p-1 bg-amber-50/50 rounded border border-amber-100 text-xs">
                                                <span class="text-amber-600">Exceptionnels:</span> 
                                                <span class="font-medium">{{ $lastHistorique->ancien_solde_conges_exceptionnels }} → {{ $lastHistorique->nouveau_solde_conges_exceptionnels }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Demandes de congés -->
                                @forelse($conges->take(3) as $conge)
                                    <div class="flex items-center justify-between border-l-4 {{ $conge->statut === 'en_attente' ? 'border-blue-500 bg-blue-50' : ($conge->statut === 'approuve' ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50') }} pl-3 pr-4 py-2 rounded-r-md">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $conge->type }} • 
                                                <span class="{{ $conge->statut === 'en_attente' ? 'text-blue-600' : ($conge->statut === 'approuve' ? 'text-green-600' : 'text-red-600') }}">
                                                    {{ $conge->statut === 'en_attente' ? 'En attente' : ($conge->statut === 'approuve' ? 'Approuvé' : 'Refusé') }}
                                                </span>
                                            </p>
                                        </div>
                                        <a href="{{ route('employe.conges.show', $conge) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded text-[rgb(131,44,207)] hover:bg-purple-50">
                                            <i class="fas fa-eye mr-1"></i>Voir
                                        </a>
                                    </div>
                                @empty
                                    @if($employe->historiqueConges->count() == 0)
                                        <div class="text-center py-4">
                                            <p class="text-sm text-gray-500">Aucune demande de congé en cours</p>
                                        </div>
                                    @endif
                                @endforelse
                                
                                <div class="text-center pt-2">
                                    <a href="{{ route('employe.conges.index') }}" class="text-sm text-[rgb(131,44,207)] hover:text-[rgb(151,64,227)] font-medium">
                                        Voir tous mes congés
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Écouter les événements de notification pour les soldes de congés
        window.Echo.private(`App.Models.User.${window.userId}`)
            .notification((notification) => {
                console.log('Notification reçue:', notification);
                
                // Si c'est une notification de modification de solde de congés
                if (notification.title && notification.title.includes('Modification de votre solde de congés')) {
                    console.log('Mise à jour des soldes de congés détectée');
                    
                    // Rafraîchir les données sans recharger la page
                    fetch('/api/employe/soldes-conges', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Soldes de congés mis à jour:', data);
                        
                        // Mettre à jour les valeurs affichées
                        if (data.solde_conges !== undefined) {
                            document.querySelectorAll('.solde-conges-value').forEach(el => {
                                el.textContent = parseFloat(data.solde_conges).toFixed(1);
                            });
                        }
                        
                        if (data.solde_rtt !== undefined) {
                            document.querySelectorAll('.solde-rtt-value').forEach(el => {
                                el.textContent = parseFloat(data.solde_rtt).toFixed(1);
                            });
                        }
                        
                        if (data.solde_conges_exceptionnels !== undefined) {
                            document.querySelectorAll('.solde-exceptionnels-value').forEach(el => {
                                el.textContent = parseFloat(data.solde_conges_exceptionnels).toFixed(1);
                            });
                        }
                        
                        // Afficher une notification toast
                        if (window.showToast) {
                            window.showToast('Vos soldes de congés ont été mis à jour', 'purple');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la mise à jour des soldes de congés:', error);
                    });
                }
            });
            
        // Écouter les événements de modification des soldes de congés en temps réel
        const employeId = {{ $employe->id }};
        window.Echo.private(`employe.${employeId}`)
            .listen('.solde.updated', (event) => {
                console.log('Evénement SoldeCongeModified reçu:', event);
                
                console.log('Données reçues:', event);
                
                // Mettre à jour les valeurs affichées
                try {
                    if (event.solde_conges !== undefined) {
                        document.querySelectorAll('.solde-conges-value').forEach(el => {
                            el.textContent = parseFloat(event.solde_conges).toFixed(1);
                            console.log('Solde congés mis à jour:', parseFloat(event.solde_conges).toFixed(1));
                        });
                    }
                    
                    if (event.solde_rtt !== undefined) {
                        document.querySelectorAll('.solde-rtt-value').forEach(el => {
                            el.textContent = parseFloat(event.solde_rtt).toFixed(1);
                            console.log('Solde RTT mis à jour:', parseFloat(event.solde_rtt).toFixed(1));
                        });
                    }
                    
                    if (event.solde_conges_exceptionnels !== undefined) {
                        document.querySelectorAll('.solde-exceptionnels-value').forEach(el => {
                            el.textContent = parseFloat(event.solde_conges_exceptionnels).toFixed(1);
                            console.log('Solde exceptionnels mis à jour:', parseFloat(event.solde_conges_exceptionnels).toFixed(1));
                        });
                    }
                } catch (error) {
                    console.error('Erreur lors de la mise à jour des soldes:', error);
                }
                
                // Afficher une notification toast
                if (window.showToast) {
                    window.showToast('Vos soldes de congés ont été mis à jour', 'purple');
                }
            });
    });
</script>
@endpush

</x-app-layout>
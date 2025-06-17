<div x-data="notificationsDropdown()" x-init="init()" class="relative ml-3">
    <div>
        <button @click="toggle()" type="button" class="relative p-1 my-2 rounded-full text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[rgb(131,44,207)]">
            <span class="sr-only">Voir les notifications</span>
            <i class="fas fa-bell text-xl"></i>
            <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-[rgb(131,44,207)] transform translate-x-1/2 -translate-y-1/2 bg-white rounded-full"></span>
        </button>
    </div>

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-96 rounded-md shadow-lg {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-800/90 border-gray-700' : 'bg-white/90 border-gray-200' }} border backdrop-blur-sm ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" style="z-index: 100;">
        <div class="py-2 px-4 border-b {{ request()->cookie('theme', 'light') === 'dark' ? 'border-gray-700' : 'border-gray-200' }}">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-medium {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-200' : 'text-gray-800' }}">Notifications</h3>
                <template x-if="unreadCount > 0">
                    <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs text-[rgb(131,44,207)] hover:text-[rgb(151,64,227)]">Tout marquer comme lu</button>
                    </form>
                </template>
            </div>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            <template x-if="notifications.length > 0">
                <div class="py-1">
                    <template x-for="notification in notifications" :key="notification.id">
                        <div class="px-4 py-3 hover:{{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-700/50' : 'bg-gray-50/80' }} border-b {{ request()->cookie('theme', 'light') === 'dark' ? 'border-gray-700' : 'border-gray-200' }} last:border-0">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full flex items-center justify-center" :class="`bg-${notification.data.color || 'indigo'}-100 {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-opacity-20' : '' }}`">
                                        <i class="fas" :class="`${notification.data.icon || 'fa-bell'} text-${notification.data.color || 'indigo'}-600`"></i>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex justify-between">
                                        <p class="text-sm font-medium {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-200' : 'text-gray-900' }}" x-text="notification.data.title || 'Notification'"></p>
                                        <p class="text-xs text-gray-500" x-text="notification.created_at"></p>
                                    </div>
                                    <p class="mt-1 text-xs {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-300' : 'text-gray-600' }}" x-text="notification.data.message"></p>
                                    
                                    <div class="mt-2 flex space-x-2">
                                        <form :action="`/notifications/${notification.id}/mark-as-read`" method="POST" x-show="!notification.read_at">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                <i class="fas fa-check mr-1"></i>Lu
                                            </button>
                                        </form>
                                        
                                        <template x-if="notification.data.type === 'conge_created'">
                                            <a :href="`/employeur/conges/${notification.data.id}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                <i class="fas fa-eye mr-1"></i>Voir
                                            </a>
                                        </template>
                                        <template x-if="notification.data.type === 'conge_status_changed'">
                                            <a :href="`/employe/conges/${notification.data.id}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                <i class="fas fa-eye mr-1"></i>Voir
                                            </a>
                                        </template>
                                        <template x-if="notification.data.type === 'planning_created' || notification.data.type === 'planning_updated'">
                                            <a :href="`/employe/plannings/calendar?mois=${notification.data.mois || new Date().getMonth() + 1}&annee=${notification.data.annee || new Date().getFullYear()}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                <i class="fas fa-calendar-alt mr-1"></i>Planning
                                            </a>
                                        </template>
                                        <template x-if="notification.data.type === 'exchange_request'">
                                            <a :href="`/employe/echanges/${notification.data.id}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                <i class="fas fa-exchange-alt mr-1"></i>Voir
                                            </a>
                                        </template>
                                        <template x-if="notification.data.type === 'exchange_accepted' || notification.data.type === 'exchange_status_changed'">
                                            <a :href="`/employe/echanges/${notification.data.id}`" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                <i class="fas fa-exchange-alt mr-1"></i>Voir
                                            </a>
                                        </template>
                                        <template x-if="notification.data.type === 'exchange_requested' || notification.data.type === 'exchange_status_changed'">
                                            <a href="/employe/echanges" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-[rgb(131,44,207)] hover:bg-[rgb(141,54,217)]">
                                                <i class="fas fa-exchange-alt mr-1"></i>Échanges
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
                <div class="px-4 py-6 text-center">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-[rgb(131,44,207)] bg-opacity-20 mb-3">
                        <i class="fas fa-bell-slash text-xl text-[rgb(131,44,207)]"></i>
                    </div>
                    <p class="text-sm font-medium {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-300' : 'text-gray-600' }}">Aucune notification</p>
                    <p class="text-xs {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-400' : 'text-gray-500' }}">Vous n'avez pas encore reçu de notifications.</p>
                </div>
            </template>
        </div>
        
        <div class="py-1 border-t {{ request()->cookie('theme', 'light') === 'dark' ? 'border-gray-700' : 'border-gray-200' }}">
            <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-[rgb(131,44,207)] hover:bg-[rgb(131,44,207)]/10" role="menuitem">
                Voir toutes les notifications
            </a>
        </div>
    </div>
</div>

<script>
    function notificationsDropdown() {
        return {
            open: false,
            unreadCount: 0,
            notifications: [],
            init() {
                this.fetchNotifications();
                
                // Rafraîchir les notifications toutes les 30 secondes
                setInterval(() => {
                    this.fetchNotifications();
                }, 30000);
            },
            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.fetchNotifications();
                }
            },
            fetchNotifications() {
                fetch('{{ route("notifications.unread") }}')
                    .then(response => response.json())
                    .then(data => {
                        this.unreadCount = data.count;
                        this.notifications = data.notifications;
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération des notifications:', error);
                    });
            }
        };
    }
</script>

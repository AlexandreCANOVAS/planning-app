@auth
    @php
    // Récupération des notifications non lues
    $allNotifications = auth()->user()->unreadNotifications;
    
    // Filtrage des notifications pour ne garder que la dernière modification de solde par type
    $soldeModifications = [];
    $filteredNotifications = [];
    
    foreach ($allNotifications as $notification) {
        // Si c'est une notification de modification de solde
        if (isset($notification->data['title']) && 
            strpos($notification->data['title'], 'Modification de votre solde de congés') !== false) {
            
            // Déterminer le type de solde (congés, RTT, exceptionnels)
            $soldeType = 'congés';
            if (isset($notification->data['message'])) {
                if (strpos($notification->data['message'], 'RTT') !== false) {
                    $soldeType = 'RTT';
                } else if (strpos($notification->data['message'], 'exceptionnels') !== false) {
                    $soldeType = 'exceptionnels';
                }
            }
            
            // Ne garder que la notification la plus récente pour chaque type de solde
            if (!isset($soldeModifications[$soldeType]) || 
                $notification->created_at > $soldeModifications[$soldeType]->created_at) {
                $soldeModifications[$soldeType] = $notification;
            }
        } else {
            // Pour les autres types de notifications, les conserver toutes
            $filteredNotifications[] = $notification;
        }
    }
    
    // Ajouter les dernières modifications de solde au début des notifications filtrées
    foreach ($soldeModifications as $notification) {
        array_unshift($filteredNotifications, $notification);
    }
    
    // Utiliser les notifications filtrées
    $notifications = collect($filteredNotifications);
    @endphp

    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if($notifications->count() > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                        {{ $notifications->count() }}
                    </span>
                @endif
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="p-2">
                @if($notifications->count() > 0)
                    <form action="{{ route('notifications.mark-all-as-read') }}" method="POST" class="px-4 py-2 border-b">
                        @csrf
                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-900 w-full text-left">
                            Tout marquer comme lu
                        </button>
                    </form>
                @endif

                @forelse($notifications as $notification)
                    <div class="py-2 px-4 hover:bg-gray-100 border-b last:border-b-0">
                        <p class="text-sm text-gray-600">
                            {{ $notification->data['message'] }}
                        </p>
                        <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="mt-1">
                            @csrf
                            <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-900">
                                Marquer comme lu
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="py-2 px-4">
                        <p class="text-sm text-gray-600">Aucune notification</p>
                    </div>
                @endforelse
            </div>
        </x-slot>
    </x-dropdown>
@endauth
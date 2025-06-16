<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-200' : 'text-gray-800' }}">
            <i class="fas fa-bell mr-2"></i>{{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm rounded-lg {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-800/50 border-gray-700' : 'bg-white/80 border-gray-200' }} border backdrop-blur-sm">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-4 rounded-md bg-green-100 border border-green-200 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-200' : 'text-gray-800' }}">
                            {{ __('Toutes vos notifications') }}
                        </h3>
                        
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                                    <i class="fas fa-check-double mr-2"></i>{{ __('Marquer tout comme lu') }}
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($notifications->count() > 0)
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="p-4 rounded-lg border {{ $notification->read_at ? 'opacity-70' : 'border-indigo-300' }} {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-gray-700/50 border-gray-600' : 'bg-white/90 border-gray-200' }}">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 pt-0.5">
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center {{ $notification->data['color'] ?? 'bg-indigo-100' }} {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-opacity-20' : '' }}">
                                                <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} text-{{ $notification->data['color'] ?? 'indigo' }}-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="flex justify-between">
                                                <p class="text-sm font-medium {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-200' : 'text-gray-900' }}">
                                                    {{ $notification->data['title'] ?? 'Notification' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <p class="mt-1 text-sm {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-300' : 'text-gray-600' }}">
                                                {{ $notification->data['message'] ?? 'Aucun message' }}
                                            </p>
                                            
                                            <div class="mt-2 flex space-x-2">
                                                @if(!$notification->read_at)
                                                    <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-opacity-20 hover:bg-opacity-30' : '' }}">
                                                            <i class="fas fa-check mr-1"></i>{{ __('Marquer comme lu') }}
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if(isset($notification->data['type']) && $notification->data['type'] === 'conge_created')
                                                    <a href="{{ url('/employeur/conges/' . $notification->data['id']) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-opacity-20 hover:bg-opacity-30' : '' }}">
                                                        <i class="fas fa-eye mr-1"></i>{{ __('Voir la demande') }}
                                                    </a>
                                                @elseif(isset($notification->data['type']) && $notification->data['type'] === 'conge_status_changed')
                                                    <a href="{{ url('/employe/conges/' . $notification->data['id']) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-opacity-20 hover:bg-opacity-30' : '' }}">
                                                        <i class="fas fa-eye mr-1"></i>{{ __('Voir les détails') }}
                                                    </a>
                                                @elseif(isset($notification->data['type']) && ($notification->data['type'] === 'planning_created' || $notification->data['type'] === 'planning_updated'))
                                                    <a href="{{ url('/employe/plannings/calendar') }}?mois={{ $notification->data['mois'] ?? date('m') }}&annee={{ $notification->data['annee'] ?? date('Y') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-opacity-20 hover:bg-opacity-30' : '' }}">
                                                        <i class="fas fa-calendar-alt mr-1"></i>{{ __('Voir mon planning') }}
                                                    </a>
                                                @elseif(isset($notification->data['type']) && ($notification->data['type'] === 'exchange_requested' || $notification->data['type'] === 'exchange_status_changed'))
                                                    <a href="{{ url('/employe/echanges') }}" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-opacity-20 hover:bg-opacity-30' : '' }}">
                                                        <i class="fas fa-exchange-alt mr-1"></i>{{ __('Voir les échanges') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 {{ request()->cookie('theme', 'light') === 'dark' ? 'bg-opacity-20' : '' }} mb-4">
                                <i class="fas fa-bell-slash text-2xl text-gray-500"></i>
                            </div>
                            <p class="text-lg font-medium {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-300' : 'text-gray-600' }}">{{ __('Aucune notification') }}</p>
                            <p class="text-sm {{ request()->cookie('theme', 'light') === 'dark' ? 'text-gray-400' : 'text-gray-500' }}">{{ __('Vous n\'avez pas encore reçu de notifications.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

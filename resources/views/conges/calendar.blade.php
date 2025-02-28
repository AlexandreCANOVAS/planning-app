<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes Congés') }}
            </h2>
            <a href="{{ route('employe.conges.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Demander un congé
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- Liste des congés -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des demandes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($conges as $conge)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $conge->periode }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $conge->type?->nom ?? 'Non spécifié' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $conge->statut === 'accepte' ? 'bg-green-100 text-green-800' : 
                                                   ($conge->statut === 'refuse' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $conge->statut === 'accepte' ? 'Accepté' : 
                                                   ($conge->statut === 'refuse' ? 'Refusé' : 'En attente') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($conge->statut === 'en_attente')
                                                <form action="{{ route('employe.conges.annuler', $conge) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Annuler</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.css' rel='stylesheet' />
    <style>
        .fc .fc-daygrid-day-events {
            margin-top: 0;
        }
        .fc .fc-daygrid-body-balanced .fc-daygrid-day-events {
            position: absolute;
            left: 0;
            right: 0;
        }
        .fc-daygrid-event {
            position: relative !important;
            margin: 1px 2px !important;
            padding: 2px !important;
            height: 20px !important;
        }
        .fc-daygrid-block-event .fc-event-time,
        .fc-daygrid-dot-event .fc-event-time {
            display: none !important;
        }
        .fc-h-event {
            border: none !important;
        }
        .fc-day-grid-event {
            margin: 1px 2px 0 !important;
            padding: 2px !important;
        }
        .fc-event-title {
            padding: 0 1px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
    </style>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: "{{ route('employe.conges.events') }}",
                eventDisplay: 'block',
                displayEventEnd: false,
                dayMaxEvents: false,
                eventTimeFormat: {
                    hour: undefined,
                    minute: undefined
                },
                eventContent: function(arg) {
                    return {
                        html: '<div class="fc-event-title">' + arg.event.title + '</div>'
                    };
                },
                eventClick: function(info) {
                    alert('Congé : ' + info.event.title);
                }
            });
            calendar.render();
        });
    </script>
    @endpush
</x-app-layout>

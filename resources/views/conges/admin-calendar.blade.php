<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Calendrier des congés') }}
            </h2>
            <a href="{{ route('conges.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Retour à la liste
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
                events: "{{ route('conges.calendar.events') }}",
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
                    // Afficher plus de détails sur le congé
                    var event = info.event;
                    var content = 'Employé : ' + event.title + '\n';
                    content += 'Période : ' + event.start.toLocaleDateString('fr-FR') + ' au ' + event.end.toLocaleDateString('fr-FR') + '\n';
                    content += 'Durée : ' + event.extendedProps.duree + ' jours';
                    alert(content);
                }
            });
            calendar.render();
        });
    </script>
    @endpush
</x-app-layout>

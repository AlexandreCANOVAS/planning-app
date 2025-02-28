<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- FullCalendar CSS -->
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- FullCalendar Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        locale: 'fr',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,dayGridWeek'
                        },
                        events: "{{ route('conges.events') }}",
                        eventDidMount: function(info) {
                            info.el.title = `${info.event.title}\nDurée: ${info.event.extendedProps.duree} jours`;
                        },
                        displayEventTime: false,
                        firstDay: 1,
                        buttonText: {
                            prev: 'Précédent',
                            next: 'Suivant',
                            today: "Aujourd'hui",
                            month: 'Mois',
                            week: 'Semaine'
                        },
                        height: 'auto',
                        eventColor: '#4CAF50',
                        dayMaxEvents: true,
                        views: {
                            dayGridMonth: {
                                titleFormat: { year: 'numeric', month: 'long' }
                            },
                            dayGridWeek: {
                                titleFormat: { year: 'numeric', month: 'long', day: '2-digit' }
                            }
                        }
                    });
                    calendar.render();
                }
            });
        </script>
    </body>
</html>

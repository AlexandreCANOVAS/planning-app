@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
            <!-- En-tête -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('employe.plannings.collegue', $employe->id) }}" 
                           class="inline-flex items-center px-3 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-white/20 hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Retour
                        </a>
                        <h2 class="text-2xl font-bold text-white">
                            Comparer les plannings
                        </h2>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-lg text-white/90">
                        {{ $firstDay->locale('fr')->monthName }} {{ $selectedYear }}
                    </div>
                    <div class="flex space-x-4">
                        <div class="text-white text-lg font-semibold px-4 py-2 rounded-lg bg-white/10 backdrop-blur-sm">
                            <span class="text-sm font-normal">Vous : </span>{{ $totalHeuresCurrent }}h
                        </div>
                        <div class="text-white text-lg font-semibold px-4 py-2 rounded-lg bg-white/10 backdrop-blur-sm">
                            <span class="text-sm font-normal">{{ $employe->prenom }} : </span>{{ $totalHeuresCollegue }}h
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu -->
            <div class="p-6">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Formulaire d'échange -->
                <form id="exchange-form" action="{{ route('employe.plannings.proposer-echange') }}" method="POST" class="mb-8">
                    @csrf
                    <input type="hidden" name="collegue_id" value="{{ $employe->id }}">
                    <input type="hidden" id="your_day" name="your_day" value="">
                    <input type="hidden" id="collegue_day" name="collegue_day" value="">
                    
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Échanger des jours de travail</h3>
                                <p class="text-sm text-gray-500">Sélectionnez un jour dans votre planning et un jour dans celui de {{ $employe->prenom }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span id="selected-days-text" class="text-sm text-gray-500">Aucun jour sélectionné</span>
                                <button type="submit" id="submit-exchange" disabled
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Proposer l'échange
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="motif" class="block text-sm font-medium text-gray-700">Motif de l'échange</label>
                            <textarea id="motif" name="motif" rows="2" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Expliquez pourquoi vous souhaitez échanger ces jours"></textarea>
                        </div>
                    </div>
                </form>
                
                <!-- Calendriers côte à côte -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Votre planning -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-indigo-50 px-4 py-3 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-indigo-800">Votre planning</h3>
                            <p class="text-sm text-indigo-600">{{ $currentEmploye->prenom }} {{ $currentEmploye->nom }}</p>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @php
                                $currentDate = clone $firstDay;
                            @endphp
                            @while($currentDate->lte($lastDay))
                                @php
                                    $dateKey = $currentDate->format('Y-m-d');
                                    $hasPlannings = isset($planningsCurrent[$dateKey]);
                                    $dayClass = $hasPlannings ? 'bg-white hover:bg-indigo-50' : 'bg-gray-50';
                                    $textClass = $hasPlannings ? 'cursor-pointer' : 'opacity-50';
                                @endphp
                                <div class="p-4 {{ $dayClass }} {{ $textClass }} your-day" data-date="{{ $dateKey }}" data-has-planning="{{ $hasPlannings ? 'true' : 'false' }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $currentDate->locale('fr')->isoFormat('dddd D') }}</span>
                                        </div>
                                        @if($hasPlannings)
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    <svg class="mr-1.5 h-2 w-2 text-indigo-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Travaillé
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($hasPlannings)
                                        <div class="mt-2 space-y-2">
                                            @foreach($planningsCurrent[$dateKey] as $planning)
                                                <div class="flex items-center text-sm">
                                                    <span class="font-medium text-gray-600">{{ substr($planning->heure_debut, 0, 5) }} - {{ substr($planning->heure_fin, 0, 5) }}</span>
                                                    <span class="ml-2 text-gray-500">{{ $planning->lieu->nom }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @php
                                    $currentDate->addDay();
                                @endphp
                            @endwhile
                        </div>
                    </div>
                    
                    <!-- Planning du collègue -->
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-purple-50 px-4 py-3 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-purple-800">Planning de {{ $employe->prenom }}</h3>
                            <p class="text-sm text-purple-600">{{ $employe->prenom }} {{ $employe->nom }}</p>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @php
                                $currentDate = clone $firstDay;
                            @endphp
                            @while($currentDate->lte($lastDay))
                                @php
                                    $dateKey = $currentDate->format('Y-m-d');
                                    $hasPlannings = isset($planningsCollegue[$dateKey]);
                                    $dayClass = $hasPlannings ? 'bg-white hover:bg-purple-50' : 'bg-gray-50';
                                    $textClass = $hasPlannings ? 'cursor-pointer' : 'opacity-50';
                                @endphp
                                <div class="p-4 {{ $dayClass }} {{ $textClass }} collegue-day" data-date="{{ $dateKey }}" data-has-planning="{{ $hasPlannings ? 'true' : 'false' }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">{{ $currentDate->locale('fr')->isoFormat('dddd D') }}</span>
                                        </div>
                                        @if($hasPlannings)
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    <svg class="mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    Travaillé
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($hasPlannings)
                                        <div class="mt-2 space-y-2">
                                            @foreach($planningsCollegue[$dateKey] as $planning)
                                                <div class="flex items-center text-sm">
                                                    <span class="font-medium text-gray-600">{{ substr($planning->heure_debut, 0, 5) }} - {{ substr($planning->heure_fin, 0, 5) }}</span>
                                                    <span class="ml-2 text-gray-500">{{ $planning->lieu->nom }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @php
                                    $currentDate->addDay();
                                @endphp
                            @endwhile
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedYourDay = null;
        let selectedCollegueDay = null;
        const yourDayInput = document.getElementById('your_day');
        const collegueDayInput = document.getElementById('collegue_day');
        const submitButton = document.getElementById('submit-exchange');
        const selectedDaysText = document.getElementById('selected-days-text');
        
        // Sélection de votre jour
        document.querySelectorAll('.your-day[data-has-planning="true"]').forEach(day => {
            day.addEventListener('click', function() {
                // Réinitialiser les sélections précédentes
                document.querySelectorAll('.your-day').forEach(d => {
                    d.classList.remove('ring-2', 'ring-indigo-500', 'ring-inset');
                });
                
                // Appliquer la sélection
                this.classList.add('ring-2', 'ring-indigo-500', 'ring-inset');
                selectedYourDay = this.dataset.date;
                yourDayInput.value = selectedYourDay;
                
                updateSelectionStatus();
            });
        });
        
        // Sélection du jour du collègue
        document.querySelectorAll('.collegue-day[data-has-planning="true"]').forEach(day => {
            day.addEventListener('click', function() {
                // Réinitialiser les sélections précédentes
                document.querySelectorAll('.collegue-day').forEach(d => {
                    d.classList.remove('ring-2', 'ring-purple-500', 'ring-inset');
                });
                
                // Appliquer la sélection
                this.classList.add('ring-2', 'ring-purple-500', 'ring-inset');
                selectedCollegueDay = this.dataset.date;
                collegueDayInput.value = selectedCollegueDay;
                
                updateSelectionStatus();
            });
        });
        
        // Mettre à jour le statut de sélection et activer/désactiver le bouton
        function updateSelectionStatus() {
            if (selectedYourDay && selectedCollegueDay) {
                submitButton.disabled = false;
                const yourDate = new Date(selectedYourDay);
                const collegueDate = new Date(selectedCollegueDay);
                const formatDate = date => {
                    return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
                };
                selectedDaysText.textContent = `Échanger votre ${formatDate(yourDate)} avec le ${formatDate(collegueDate)}`;
                selectedDaysText.classList.remove('text-gray-500');
                selectedDaysText.classList.add('text-indigo-600', 'font-medium');
            } else {
                submitButton.disabled = true;
                if (selectedYourDay) {
                    selectedDaysText.textContent = "Sélectionnez un jour dans le planning du collègue";
                } else if (selectedCollegueDay) {
                    selectedDaysText.textContent = "Sélectionnez un jour dans votre planning";
                } else {
                    selectedDaysText.textContent = "Aucun jour sélectionné";
                }
                selectedDaysText.classList.remove('text-indigo-600', 'font-medium');
                selectedDaysText.classList.add('text-gray-500');
            }
        }
        
        // Validation du formulaire avant soumission
        document.getElementById('exchange-form').addEventListener('submit', function(e) {
            if (!selectedYourDay || !selectedCollegueDay) {
                e.preventDefault();
                alert('Veuillez sélectionner un jour dans chaque planning.');
                return false;
            }
            
            if (!document.getElementById('motif').value.trim()) {
                e.preventDefault();
                alert('Veuillez indiquer un motif pour l\'échange.');
                return false;
            }
            
            return true;
        });
    });
</script>
@endsection
@endsection

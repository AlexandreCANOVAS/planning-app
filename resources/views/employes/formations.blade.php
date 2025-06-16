<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if(isset($employe))
                    {{ __('Formations de') }} {{ $employe->prenom }} {{ $employe->nom }}
                @else
                    {{ __('Formations des employés') }}
                @endif
            </h2>
            <a href="{{ route('employes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>
                {{ __('Retour') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(isset($employe))
                <!-- Single employee view -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-graduation-cap text-blue-500 mr-2"></i>
                                État des formations
                            </h3>
                            <a href="{{ route('employes.edit', $employe) }}#formations" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 active:bg-blue-700 transition">
                                <i class="fas fa-plus mr-2"></i>
                                Ajouter une formation
                            </a>
                        </div>
                        

                        
                        @if(!isset($formations) || $formations->isEmpty())
                            <div class="flex items-center justify-center h-48 bg-gray-50 rounded-lg">
                                <p class="text-gray-500 flex items-center">
                                    <i class="fas fa-info-circle text-gray-400 mr-2 text-xl"></i>
                                    Aucune formation enregistrée
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($formations as $formation)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-medium text-gray-800">{{ $formation->nom }}</h4>
                                            @php
                                                $dateObtention = $formation->date_obtention ? \Carbon\Carbon::parse($formation->date_obtention) : null;
                                                $dateRecyclage = $formation->date_recyclage ? \Carbon\Carbon::parse($formation->date_recyclage) : null;
                                                $isValid = $dateRecyclage ? $dateRecyclage->isFuture() : true; // Si pas de date de recyclage, on considère la formation comme valide
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $isValid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $isValid ? 'Valide' : 'À recycler' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">{{ $formation->description }}</p>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            @if($dateObtention)
                                                <div>
                                                    <span class="text-gray-500">Obtention:</span>
                                                    <span class="font-medium">{{ $dateObtention->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                            @if($dateRecyclage)
                                                <div>
                                                    <span class="text-gray-500">Recyclage:</span>
                                                    <span class="font-medium">{{ $dateRecyclage->format('d/m/Y') }}</span>
                                                    
                                                    @php
                                                        $now = \Carbon\Carbon::now();
                                                        $daysUntilRecyclage = $now->diffInDays($dateRecyclage, false);
                                                        $percentLeft = 100;
                                                        $barColor = 'bg-green-500';
                                                        
                                                        if ($daysUntilRecyclage < 0) {
                                                            $percentLeft = 0;
                                                            $barColor = 'bg-red-500';
                                                        } else {
                                                            // Supposons qu'une formation est généralement valide pour 2 ans (730 jours)
                                                            $totalDays = 730;
                                                            if ($dateObtention) {
                                                                $totalDays = $dateObtention->diffInDays($dateRecyclage);
                                                            }
                                                            $daysPassed = $totalDays - $daysUntilRecyclage;
                                                            $percentLeft = max(0, min(100, 100 - ($daysPassed / $totalDays * 100)));
                                                            
                                                            if ($percentLeft < 25) {
                                                                $barColor = 'bg-red-500';
                                                            } elseif ($percentLeft < 50) {
                                                                $barColor = 'bg-orange-500';
                                                            } elseif ($percentLeft < 75) {
                                                                $barColor = 'bg-yellow-500';
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    <div class="mt-1">
                                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                            <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $percentLeft }}%"></div>
                                                        </div>
                                                        <div class="text-xs mt-1 {{ $daysUntilRecyclage < 0 ? 'text-red-600 font-bold' : ($daysUntilRecyclage < 30 ? 'text-orange-600' : 'text-gray-500') }}">
                                                            @if($daysUntilRecyclage < 0)
                                                                Expiré depuis {{ abs($daysUntilRecyclage) }} jour(s)
                                                            @elseif($daysUntilRecyclage == 0)
                                                                Expire aujourd'hui
                                                            @else
                                                                Expire dans {{ $daysUntilRecyclage }} jour(s)
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($formation->last_recyclage)
                                                <div>
                                                    <span class="text-gray-500">Dernier recyclage:</span>
                                                    <span class="font-medium">{{ \Carbon\Carbon::parse($formation->last_recyclage)->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if($formation->commentaire)
                                            <div class="mt-2 p-2 bg-yellow-50 rounded text-xs">
                                                <span class="font-medium">Note:</span> {{ $formation->commentaire }}
                                            </div>
                                        @endif
                                        
                                        <!-- Historique des recyclages -->
                                        <div class="mt-3 border-t border-gray-200 pt-3">
                                            <button type="button" onclick="toggleHistorique('historique-{{ $formation->id }}')" class="flex items-center text-xs text-blue-600 hover:text-blue-800">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                                Voir l'historique des recyclages
                                            </button>
                                            
                                            <div id="historique-{{ $formation->id }}" class="hidden mt-2 bg-gray-50 rounded-md p-2">
                                                @if($formation->last_recyclage)
                                                    <div class="text-xs mb-1 pb-1 border-b border-gray-200">
                                                        <div class="flex justify-between">
                                                            <span class="font-medium">{{ \Carbon\Carbon::parse($formation->last_recyclage)->format('d/m/Y') }}</span>
                                                            <span class="text-green-600">Recyclage effectué</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if($dateObtention)
                                                    <div class="text-xs">
                                                        <div class="flex justify-between">
                                                            <span class="font-medium">{{ $dateObtention->format('d/m/Y') }}</span>
                                                            <span class="text-blue-600">Formation initiale</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <!-- Message si pas d'historique -->
                                                @if(!$formation->last_recyclage && !$dateObtention)
                                                    <div class="text-xs text-gray-500 text-center py-1">
                                                        Aucun historique disponible
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Multiple employees view -->
                <div class="space-y-6">
                    @foreach($employes as $employe)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                                    <i class="fas fa-user text-blue-500 mr-2"></i>
                                    {{ $employe->prenom }} {{ $employe->nom }}
                                </h3>
                                
                                @if(!$employe->formations || $employe->formations->isEmpty())
                                    <div class="flex items-center justify-center h-32 bg-gray-50 rounded-lg">
                                        <p class="text-gray-500 flex items-center">
                                            <i class="fas fa-info-circle text-gray-400 mr-2 text-xl"></i>
                                            Aucune formation enregistrée
                                        </p>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($employe->formations as $formation)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="flex items-center justify-between mb-2">
                                                    <h4 class="font-medium text-gray-800">{{ $formation->nom }}</h4>
                                                    @php
                                                        $dateObtention = $formation->pivot->date_obtention ? \Carbon\Carbon::parse($formation->pivot->date_obtention) : null;
                                                        $dateRecyclage = $formation->pivot->date_recyclage ? \Carbon\Carbon::parse($formation->pivot->date_recyclage) : null;
                                                        $isValid = $dateRecyclage ? $dateRecyclage->isFuture() : true; // Si pas de date de recyclage, on considère la formation comme valide
                                                    @endphp
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $isValid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $isValid ? 'Valide' : 'À recycler' }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 mb-3">{{ $formation->description }}</p>
                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                    @if($dateObtention)
                                                        <div>
                                                            <span class="text-gray-500">Obtention:</span>
                                                            <span class="font-medium">{{ $dateObtention->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                    @if($dateRecyclage)
                                                        <div>
                                                            <span class="text-gray-500">Recyclage:</span>
                                                            <span class="font-medium">{{ $dateRecyclage->format('d/m/Y') }}</span>
                                                            
                                                            @php
                                                                $now = \Carbon\Carbon::now();
                                                                $daysUntilRecyclage = $now->diffInDays($dateRecyclage, false);
                                                                $percentLeft = 100;
                                                                $barColor = 'bg-green-500';
                                                                
                                                                if ($daysUntilRecyclage < 0) {
                                                                    $percentLeft = 0;
                                                                    $barColor = 'bg-red-500';
                                                                } else {
                                                                    // Supposons qu'une formation est généralement valide pour 2 ans (730 jours)
                                                                    $totalDays = 730;
                                                                    if ($dateObtention) {
                                                                        $totalDays = $dateObtention->diffInDays($dateRecyclage);
                                                                    }
                                                                    $daysPassed = $totalDays - $daysUntilRecyclage;
                                                                    $percentLeft = max(0, min(100, 100 - ($daysPassed / $totalDays * 100)));
                                                                    
                                                                    if ($percentLeft < 25) {
                                                                        $barColor = 'bg-red-500';
                                                                    } elseif ($percentLeft < 50) {
                                                                        $barColor = 'bg-orange-500';
                                                                    } elseif ($percentLeft < 75) {
                                                                        $barColor = 'bg-yellow-500';
                                                                    }
                                                                }
                                                            @endphp
                                                            
                                                            <div class="mt-1">
                                                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $percentLeft }}%"></div>
                                                                </div>
                                                                <div class="text-xs mt-1 {{ $daysUntilRecyclage < 0 ? 'text-red-600 font-bold' : ($daysUntilRecyclage < 30 ? 'text-orange-600' : 'text-gray-500') }}">
                                                                    @if($daysUntilRecyclage < 0)
                                                                        Expiré depuis {{ abs($daysUntilRecyclage) }} jour(s)
                                                                    @elseif($daysUntilRecyclage == 0)
                                                                        Expire aujourd'hui
                                                                    @else
                                                                        Expire dans {{ $daysUntilRecyclage }} jour(s)
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($formation->pivot->last_recyclage)
                                                        <div>
                                                            <span class="text-gray-500">Dernier recyclage:</span>
                                                            <span class="font-medium">{{ \Carbon\Carbon::parse($formation->pivot->last_recyclage)->format('d/m/Y') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($formation->pivot->commentaire)
                                                    <div class="mt-2 p-2 bg-yellow-50 rounded text-xs">
                                                        <span class="font-medium">Note:</span> {{ $formation->pivot->commentaire }}
                                                    </div>
                                                @endif
                                                
                                                <!-- Historique des recyclages -->
                                                <div class="mt-3 border-t border-gray-200 pt-3">
                                                    <button type="button" onclick="toggleHistorique('historique-{{ $employe->id }}-{{ $formation->id }}')" class="flex items-center text-xs text-blue-600 hover:text-blue-800">
                                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                        Voir l'historique des recyclages
                                                    </button>
                                                    
                                                    <div id="historique-{{ $employe->id }}-{{ $formation->id }}" class="hidden mt-2 bg-gray-50 rounded-md p-2">
                                                        @if($formation->pivot->last_recyclage)
                                                            <div class="text-xs mb-1 pb-1 border-b border-gray-200">
                                                                <div class="flex justify-between">
                                                                    <span class="font-medium">{{ \Carbon\Carbon::parse($formation->pivot->last_recyclage)->format('d/m/Y') }}</span>
                                                                    <span class="text-green-600">Recyclage effectué</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        @if($dateObtention)
                                                            <div class="text-xs">
                                                                <div class="flex justify-between">
                                                                    <span class="font-medium">{{ $dateObtention->format('d/m/Y') }}</span>
                                                                    <span class="text-blue-600">Formation initiale</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        <!-- Message si pas d'historique -->
                                                        @if(!$formation->pivot->last_recyclage && !$dateObtention)
                                                            <div class="text-xs text-gray-500 text-center py-1">
                                                                Aucun historique disponible
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    
    <script>
        function toggleHistorique(id) {
            const element = document.getElementById(id);
            if (element) {
                if (element.classList.contains('hidden')) {
                    element.classList.remove('hidden');
                    // Changer l'icône pour indiquer que l'historique est ouvert
                    const button = element.previousElementSibling;
                    const svg = button.querySelector('svg path');
                    if (svg) {
                        svg.setAttribute('d', 'M5 15l7-7 7 7'); // Flèche vers le haut
                    }
                } else {
                    element.classList.add('hidden');
                    // Changer l'icône pour indiquer que l'historique est fermé
                    const button = element.previousElementSibling;
                    const svg = button.querySelector('svg path');
                    if (svg) {
                        svg.setAttribute('d', 'M19 9l-7 7-7-7'); // Flèche vers le bas
                    }
                }
            }
        }
    </script>
</x-app-layout>

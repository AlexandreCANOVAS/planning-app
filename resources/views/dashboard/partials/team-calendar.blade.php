<div class="mb-8">
    <h2 class="text-lg font-medium text-gray-900 mb-4">Calendrier d'équipe</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Aujourd'hui -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Aujourd'hui - {{ \Carbon\Carbon::parse($today)->format('d/m/Y') }}</h3>
            </div>
            
            @if($employesAujourdhui->count() > 0)
                <div class="space-y-3">
                    @foreach($employesAujourdhui as $employeData)
                        @if($employeData['employe']->id != $employe->id)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-100">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-3">
                                        @if($employeData['employe']->photo)
                                            <img src="{{ asset('storage/' . $employeData['employe']->photo) }}" alt="{{ $employeData['employe']->nom }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-medium text-sm">{{ substr($employeData['employe']->prenom, 0, 1) }}{{ substr($employeData['employe']->nom, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $employeData['employe']->prenom }} {{ $employeData['employe']->nom }}</p>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                            <span>{{ $employeData['lieu']->nom }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @foreach($employeData['heures'] as $heure)
                                        <div class="text-sm text-gray-600 bg-blue-50 px-2 py-1 rounded mb-1 inline-block">
                                            {{ \Carbon\Carbon::parse($heure['debut'])->format('H:i') }} - {{ \Carbon\Carbon::parse($heure['fin'])->format('H:i') }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @if($employesAujourdhui->count() == 1 && isset($employesAujourdhui[$employe->id]))
                    <div class="p-4 bg-gray-50 rounded-lg text-center mt-3">
                        <p class="text-gray-500">Vous êtes le seul employé en service aujourd'hui</p>
                    </div>
                @endif
            @else
                <div class="p-4 bg-gray-50 rounded-lg text-center">
                    <p class="text-gray-500">Aucun employé en service aujourd'hui</p>
                </div>
            @endif
        </div>

        <!-- Demain -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Demain - {{ \Carbon\Carbon::parse($tomorrow)->format('d/m/Y') }}</h3>
            </div>
            
            @if($employesDemain->count() > 0)
                <div class="space-y-3">
                    @foreach($employesDemain as $employeData)
                        @if($employeData['employe']->id != $employe->id)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border border-gray-100">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-3">
                                        @if($employeData['employe']->photo)
                                            <img src="{{ asset('storage/' . $employeData['employe']->photo) }}" alt="{{ $employeData['employe']->nom }}" class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                <span class="text-purple-600 font-medium text-sm">{{ substr($employeData['employe']->prenom, 0, 1) }}{{ substr($employeData['employe']->nom, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $employeData['employe']->prenom }} {{ $employeData['employe']->nom }}</p>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                            <span>{{ $employeData['lieu']->nom }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @foreach($employeData['heures'] as $heure)
                                        <div class="text-sm text-gray-600 bg-purple-50 px-2 py-1 rounded mb-1 inline-block">
                                            {{ \Carbon\Carbon::parse($heure['debut'])->format('H:i') }} - {{ \Carbon\Carbon::parse($heure['fin'])->format('H:i') }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                @if($employesDemain->count() == 1 && isset($employesDemain[$employe->id]))
                    <div class="p-4 bg-gray-50 rounded-lg text-center mt-3">
                        <p class="text-gray-500">Vous êtes le seul employé en service demain</p>
                    </div>
                @endif
            @else
                <div class="p-4 bg-gray-50 rounded-lg text-center">
                    <p class="text-gray-500">Aucun employé en service demain</p>
                </div>
            @endif
        </div>
    </div>
</div>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight">
                {{ __('Plannings pour') }} : {{ $lieu->nom }}
            </h2>
            <a href="{{ route('lieux.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                <i class="fas fa-arrow-left mr-2"></i> Retour aux lieux
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Carte d'information du lieu -->
            <div class="mb-6 overflow-hidden rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="h-10 w-10 rounded-full mr-4 flex-shrink-0" style="background-color: {{ $lieu->couleur }}"></div>
                        <div>
                            <h2 class="text-2xl font-semibold">{{ $lieu->nom }}</h2>
                            <p class="text-sm"><i class="fas fa-map-marker-alt mr-2"></i>{{ $lieu->adresse }}, {{ $lieu->code_postal }} {{ $lieu->ville }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        @if($lieu->telephone)
                            <div class="flex items-center">
                                <i class="fas fa-phone text-indigo-500 mr-2"></i>
                                <span>{{ $lieu->telephone }}</span>
                            </div>
                        @endif
                        @if($lieu->contact_principal)
                            <div class="flex items-center">
                                <i class="fas fa-user text-indigo-500 mr-2"></i>
                                <span>{{ $lieu->contact_principal }}</span>
                            </div>
                        @endif
                        @if($lieu->horaires)
                            <div class="flex items-center">
                                <i class="fas fa-clock text-indigo-500 mr-2"></i>
                                <span>{{ $lieu->horaires }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tableau des plannings -->
            <div class="overflow-hidden rounded-lg border border-gray-200">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Plannings associés</h3>
                        
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <!-- Sélecteur d'employé -->
                            <div class="w-full md:w-1/2">
                                <label for="employe_select" class="block text-sm font-medium mb-2">Filtrer par employé</label>
                                <div class="relative">
                                    <select id="employe_select" onchange="window.location.href=this.value" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md">
                                        <option value="{{ route('lieux.plannings', ['lieu' => $lieu->id]) }}">Tous les employés</option>
                                        @foreach($employes as $employe)
                                            <option value="{{ route('lieux.plannings', ['lieu' => $lieu->id, 'employe_id' => $employe->id]) }}" {{ $selectedEmployeId == $employe->id ? 'selected' : '' }}>
                                                {{ $employe->nom }} {{ $employe->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Options de tri -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('lieux.plannings', ['lieu' => $lieu->id, 'employe_id' => $selectedEmployeId, 'sort_by' => 'date', 'sort_order' => (($sortBy == 'date' && $sortOrder == 'desc') ? 'asc' : 'desc')]) }}" 
                                   class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ $sortBy == 'date' ? 'bg-indigo-100 border-indigo-300' : 'border-gray-300 hover:bg-gray-100' }}">
                                    <i class="fas fa-calendar-day mr-1"></i> Date
                                    @if($sortBy == 'date')
                                        <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                                
                                <a href="{{ route('lieux.plannings', ['lieu' => $lieu->id, 'employe_id' => $selectedEmployeId, 'sort_by' => 'heures_travaillees', 'sort_order' => (($sortBy == 'heures_travaillees' && $sortOrder == 'desc') ? 'asc' : 'desc')]) }}" 
                                   class="inline-flex items-center px-3 py-2 border rounded-md text-sm font-medium {{ $sortBy == 'heures_travaillees' ? 'bg-indigo-100 border-indigo-300' : 'border-gray-300 hover:bg-gray-100' }}">
                                    <i class="fas fa-clock mr-1"></i> Heures
                                    @if($sortBy == 'heures_travaillees')
                                        <i class="fas fa-sort-{{ $sortOrder == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($plannings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Employé</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Heures travaillées</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Horaires</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($plannings as $planning)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150 ease-in-out">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center">
                                                    <i class="fas fa-calendar-day text-indigo-500 mr-2"></i>
                                                    {{ \Carbon\Carbon::parse($planning->date)->format('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-indigo-700">{{ substr($planning->employe->prenom, 0, 1) }}{{ substr($planning->employe->nom, 0, 1) }}</span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="font-medium">{{ $planning->employe->nom }} {{ $planning->employe->prenom }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ $planning->heures_travaillees }}h
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center">
                                                    <i class="fas fa-clock text-indigo-500 mr-2"></i>
                                                    <span class="font-medium">{{ $planning->heure_debut }} - {{ $planning->heure_fin }}</span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('plannings.edit-monthly-calendar') }}?planning_id={{ $planning->id }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center">
                                                    <i class="fas fa-edit mr-1"></i> Modifier
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6">
                            {{ $plannings->links() }}
                        </div>
                    @else
                        <div class="rounded-md p-4 border border-yellow-200 bg-yellow-50">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Aucun planning n'est associé à ce lieu pour le moment.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div class="space-y-4">
    @if($planningsByEmploye->isEmpty())
        <div class="text-center text-gray-500 py-4">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun planning</h3>
            <p class="mt-1 text-sm text-gray-500">Il n'y a pas de planning enregistré pour ce jour.</p>
        </div>
    @else
        @foreach($planningsByEmploye as $employeId => $plannings)
            <div class="p-4 border rounded-lg shadow-sm bg-gray-50">
                <div class="flex items-center mb-3">
                    <div class="flex-shrink-0">
                        @if($plannings->first()->employe->photo)
                            <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $plannings->first()->employe->photo) }}" alt="Photo de {{ $plannings->first()->employe->prenom }}">
                        @else
                            <span class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-gray-600 font-semibold">{{ strtoupper(substr($plannings->first()->employe->prenom, 0, 1)) }}{{ strtoupper(substr($plannings->first()->employe->nom, 0, 1)) }}</span>
                            </span>
                        @endif
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-semibold text-gray-800">{{ $plannings->first()->employe->nom }} {{ $plannings->first()->employe->prenom }}</h4>
                    </div>
                </div>

                <ul class="space-y-2">
                    @foreach($plannings as $planning)
                        <li class="flex items-start p-3 rounded-md bg-white border">
                            <div class="flex-shrink-0 mr-3">
                                <span class="h-8 w-8 rounded-full flex items-center justify-center" style="background-color: {{ $planning->lieu->couleur ?? '#d1d5db' }};">
                                    <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                      <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <div class="flex-grow">
                                <p class="font-semibold text-gray-700">{{ $planning->lieu->nom }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($planning->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($planning->heure_fin)->format('H:i') }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @endif
</div>

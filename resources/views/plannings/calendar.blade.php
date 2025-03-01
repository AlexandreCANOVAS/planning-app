@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-4">Créer un planning</h2>
            <div class="flex space-x-4 mb-4">
                <div class="w-1/3">
                    <label for="employe_id" class="block text-sm font-medium text-gray-700">Employé</label>
                    <select name="employe_id" id="employe_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Sélectionner un employé</option>
                        @foreach($employes as $employe)
                            <option value="{{ $employe->id }}" {{ request('employe_id') == $employe->id ? 'selected' : '' }}>
                                {{ $employe->nom }} {{ $employe->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-1/3">
                    <label for="mois" class="block text-sm font-medium text-gray-700">Mois</label>
                    <select name="mois" id="mois" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $moisActuel == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->locale('fr')->monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="w-1/3">
                    <label for="annee" class="block text-sm font-medium text-gray-700">Année</label>
                    <select name="annee" id="annee" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @foreach(range(date('Y')-1, date('Y')+5) as $a)
                            <option value="{{ $a }}" {{ $anneeActuelle == $a ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-1/3 flex items-end">
                    <button type="button" onclick="createPlanning()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Créer un planning
                    </button>
                </div>
            </div>

            <!-- Récapitulatif des plannings -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Récapitulatif des Plannings - {{ $anneeActuelle }}</h3>
                
                @php
                    \Log::info('Données dans la vue', [
                        'annee' => $anneeActuelle,
                        'recapitulatif' => $recapitulatifMensuel
                    ]);
                @endphp

                @if(empty($recapitulatifMensuel))
                    <div class="text-center text-gray-500 py-4">
                        Aucun planning n'a été créé pour cette année.
                    </div>
                @else
                    @foreach($recapitulatifMensuel as $mois => $data)
                        <div class="mb-8 bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $data['nom_mois'] }}</h3>
                            </div>
                            <div class="border-t border-gray-200">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employé</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jours travaillés</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total heures</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails par lieu</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($data['stats_par_employe'] as $stat)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $stat['employe']->nom }} {{ $stat['employe']->prenom }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ count($stat['lieux']) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ number_format($stat['total_heures'], 2) }}h
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @foreach($stat['lieux'] as $lieu => $details)
                                                        <div>{{ $lieu }}: {{ $details['count'] }} jours ({{ number_format($details['heures'], 2) }}h)</div>
                                                    @endforeach
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    <a href="{{ url('/plannings/view-monthly-calendar/'.$stat['employe']->id.'/'.$mois.'/'.$anneeActuelle) }}" 
                                                        class="text-blue-600 hover:text-blue-900 mr-4">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                    <a href="#" onclick="modifierPlanning('{{ $stat['employe']->id }}', '{{ $mois }}', '{{ $anneeActuelle }}')" 
                                                        class="text-blue-600 hover:text-blue-900 mr-4">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                    <button onclick="supprimerPlanning('{{ $stat['employe']->id }}', '{{ str_pad($mois, 2, '0', STR_PAD_LEFT) }}', '{{ $anneeActuelle }}')" 
                                                        class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i> Supprimer
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function modifierPlanning(employeId, mois, annee) {
            @if(auth()->user() && auth()->user()->isEmployeur())
                window.location.href = "{{ url('/plannings/create-monthly-calendar') }}" + `?employe_id=${employeId}&mois=${mois}&annee=${annee}`;
            @else
                alert('Vous devez être connecté en tant qu\'employeur pour modifier les plannings.');
            @endif
        }

        function createPlanning() {
            const employe_id = document.getElementById('employe_id').value;
            const mois = document.getElementById('mois').value;
            const annee = document.getElementById('annee').value;

            if (!employe_id) {
                alert('Veuillez sélectionner un employé');
                return;
            }

            @if(auth()->user() && auth()->user()->isEmployeur())
                window.location.href = "{{ url('/plannings/create-monthly-calendar') }}" + `?employe_id=${employe_id}&mois=${mois}&annee=${annee}`;
            @else
                alert('Vous devez être connecté en tant qu\'employeur pour créer des plannings.');
            @endif
        }

        function supprimerPlanning(employeId, mois, annee) {
            if (confirm('Êtes-vous sûr de vouloir supprimer tous les plannings de cet employé pour ce mois ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/plannings/destroy-monthly/${employeId}/${annee}-${mois.padStart(2, '0')}`;
                form.style.display = 'none';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endpush
@endsection

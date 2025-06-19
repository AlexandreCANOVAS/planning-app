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
                <div class="w-1/3 flex items-end space-x-2">
                    <button type="button" onclick="createPlanning()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Créer un planning
                    </button>
                </div>
            </div>

            <!-- Récapitulatif des plannings -->
            <div class="mt-8 flex flex-col">
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
                                                    {{ App\Http\Controllers\PlanningController::convertToHHMM($stat['total_heures']) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @foreach($stat['lieux'] as $lieu => $details)
                                                        <div>{{ $lieu }}: {{ $details['count'] }} jours ({{ App\Http\Controllers\PlanningController::convertToHHMM($details['heures']) }})</div>
                                                    @endforeach
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    <div class="flex items-center space-x-3">
                                                        <a href="{{ url('/plannings/view-monthly-calendar/'.$stat['employe']->id.'/'.$mois.'/'.$anneeActuelle) }}" 
                                                            class="text-blue-600 hover:text-blue-900">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                        <a href="#" onclick="modifierPlanning('{{ $stat['employe']->id }}', '{{ $mois }}', '{{ $anneeActuelle }}')" 
                                                            class="text-blue-600 hover:text-blue-900">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                        <a href="{{ route('plannings.export-pdf', [
                                                            'employe_id' => $stat['employe']->id,
                                                            'mois' => $moisActuel,
                                                            'annee' => $anneeActuelle
                                                        ]) }}" 
                                                        class="text-indigo-600 hover:text-indigo-800"
                                                        title="Télécharger le planning en PDF"
                                                        download>
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                        </a>
                                                        <a href="#" 
                                                           class="text-red-600 hover:text-red-900"
                                                           onclick="openConfirmModal('{{ $stat['employe']->id }}', '{{ $mois }}', '{{ $stat['employe']->nom }} {{ $stat['employe']->prenom }}', '{{ \Carbon\Carbon::create(null, $mois, 1)->locale('fr')->monthName }}', '{{ $anneeActuelle }}')">
                                                            <i class="fas fa-trash"></i> Supprimer
                                                        </a>
                                                    </div>
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

    <!-- Boîte de dialogue modale de confirmation -->
    <div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0" style="display: none;">
        <div class="fixed inset-0 transform transition-all">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="transform transition-all sm:w-full sm:max-w-md">
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmation de suppression</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="confirmModalText">
                                    Êtes-vous sûr de vouloir supprimer tous les plannings de cet employé pour ce mois ?
                                    Cette action est irréversible.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <a href="#" id="confirmDeleteBtn"
                       class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Supprimer
                    </a>
                    <button type="button" 
                            onclick="closeConfirmModal()" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openConfirmModal(employeId, mois, employeName, monthName, annee) {
            // Mettre à jour le texte de la modale
            document.getElementById('confirmModalText').innerHTML = `
                Êtes-vous sûr de vouloir supprimer tous les plannings de <strong>${employeName}</strong> pour le mois de <strong>${monthName}</strong> ?<br>
                Cette action est irréversible.
            `;
            
            // Mettre à jour le lien de suppression
            const monthStr = mois.toString().padStart(2, '0');
            const yearMonth = annee + '-' + monthStr;
            const deleteUrl = '{{ route("plannings.destroy_monthly_confirm", [":employeId", ":yearMonth"]) }}';
            const finalUrl = deleteUrl
                .replace(':employeId', employeId)
                .replace(':yearMonth', yearMonth);
                
            document.getElementById('confirmDeleteBtn').href = finalUrl;
            
            // Afficher la modale
            document.getElementById('confirmModal').style.display = 'flex';
            
            // Empêcher le défilement de la page
            document.body.style.overflow = 'hidden';
            
            return false;
        }
        
        function closeConfirmModal() {
            // Cacher la modale
            document.getElementById('confirmModal').style.display = 'none';
            
            // Réactiver le défilement de la page
            document.body.style.overflow = 'auto';
            
            return false;
        }
        
        // Fermer la modale en cliquant sur le fond
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('confirmModal');
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeConfirmModal();
                }
            });
            
            // Fermer la modale avec la touche Echap
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    closeConfirmModal();
                }
            });
        });
        
        function modifierPlanning(employeId, mois, annee) {
            @if(auth()->user() && auth()->user()->isEmployeur())
                window.location.href = "{{ url('/plannings/edit-monthly-calendar') }}" + `?employe_id=${employeId}&mois=${mois}&annee=${annee}`;
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
            console.log('Fonction supprimerPlanning appelée avec:', { employeId, mois, annee });
            
            try {
                if (confirm('Êtes-vous sûr de vouloir supprimer tous les plannings de cet employé pour ce mois ?')) {
                    console.log('Confirmation acceptée, création du formulaire');
                    
                    // Créer un formulaire avec la méthode POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    
                    // Utiliser la route nommée correcte définie dans web.php
                    const routeTemplate = '{{ route("plannings.destroy_monthly", [":employe_id", ":year_month"]) }}';
                    const yearMonth = annee + '-' + mois.padStart(2, '0');
                    const url = routeTemplate
                        .replace(':employe_id', employeId)
                        .replace(':year_month', yearMonth);
                    
                    form.action = url;
                    form.style.display = 'none';
                    
                    console.log('URL du formulaire:', url);

                    // Ajouter le token CSRF
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    console.log('Token CSRF ajouté:', csrfToken.value);

                    // Ajouter le champ de méthode pour simuler DELETE
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);
                    console.log('Champ méthode DELETE ajouté');

                    // Ajouter le formulaire au document et le soumettre
                    document.body.appendChild(form);
                    console.log('Formulaire ajouté au document, tentative de soumission...');
                    
                    // Petit délai pour s'assurer que tout est bien attaché au DOM
                    setTimeout(() => {
                        try {
                            form.submit();
                            console.log('Formulaire soumis avec succès');
                        } catch (submitError) {
                            console.error('Erreur lors de la soumission du formulaire:', submitError);
                            alert('Erreur lors de la suppression: ' + submitError.message);
                        }
                    }, 100);
                } else {
                    console.log('Suppression annulée par l\'utilisateur');
                }
            } catch (error) {
                console.error('Erreur dans la fonction supprimerPlanning:', error);
                alert('Une erreur est survenue: ' + error.message);
            }
        }
    </script>
    @endpush
@endsection

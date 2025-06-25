@extends('layouts.app')

@section('content')
<div class="bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="space-y-10">
            
            <!-- Panneau de création de planning -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="p-6 border-b border-slate-200">
                    <h2 class="text-2xl font-bold text-slate-800">Générer un planning mensuel</h2>
                    <p class="text-slate-500 mt-1">Sélectionnez un employé, un mois et une année pour créer ou afficher un planning.</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                        <!-- Sélection Employé -->
                        <div>
                            <label for="employe_id" class="block text-sm font-medium text-slate-700 mb-2">Employé</label>
                            <select name="employe_id" id="employe_id" class="w-full bg-white border-slate-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                <option value="" class="text-slate-400">Sélectionner...</option>
                                @foreach($employes as $employe)
                                    <option value="{{ $employe->id }}" {{ request('employe_id') == $employe->id ? 'selected' : '' }}>
                                        {{ $employe->nom }} {{ $employe->prenom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Sélection Mois -->
                        <div>
                            <label for="mois" class="block text-sm font-medium text-slate-700 mb-2">Mois</label>
                            <select name="mois" id="mois" class="w-full bg-white border-slate-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $moisActuel == $m ? 'selected' : '' }}>
                                        {{ ucfirst(\Carbon\Carbon::create(null, $m, 1)->locale('fr')->monthName) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Sélection Année -->
                        <div>
                            <label for="annee" class="block text-sm font-medium text-slate-700 mb-2">Année</label>
                            <select name="annee" id="annee" class="w-full bg-white border-slate-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                                @foreach(range(date('Y')-2, date('Y')+3) as $a)
                                    <option value="{{ $a }}" {{ $anneeActuelle == $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Bouton Créer -->
                        <div class="md:col-span-1">
                            <button type="button" onclick="createPlanning()" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-purple-600 border border-transparent rounded-lg font-semibold text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                Créer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Récapitulatif des plannings -->
            <div>
                <h3 class="text-3xl font-bold text-slate-800 mb-6">Récapitulatif - {{ $anneeActuelle }}</h3>
                
                @if(empty($recapitulatifMensuel))
                    <div class="text-center text-slate-500 py-16 bg-white rounded-2xl shadow-sm border border-slate-200">
                        <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <h3 class="mt-4 text-lg font-semibold text-slate-800">Aucun planning trouvé</h3>
                        <p class="mt-1 text-sm text-slate-500">Aucun planning n'a été créé pour cette année.</p>
                    </div>
                @else
                    <div class="space-y-8">
                        @foreach($recapitulatifMensuel as $mois => $data)
                            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                                <div class="p-5 bg-slate-50 border-b border-slate-200">
                                    <h4 class="text-xl font-bold text-purple-700">{{ $data['nom_mois'] }}</h4>
                                </div>
                                <div class="divide-y divide-slate-200">
                                    @foreach($data['stats_par_employe'] as $stat)
                                        <div class="p-5 hover:bg-slate-50 transition duration-150">
                                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
                                                <div class="flex-grow mb-4 sm:mb-0">
                                                    <p class="font-bold text-lg text-slate-800">{{ $stat['employe']->nom }} {{ $stat['employe']->prenom }}</p>
                                                    <div class="text-sm text-slate-600 mt-2 space-y-1.5">
                                                        @foreach($stat['lieux'] as $lieu => $details)
                                                            <div><span class="font-semibold text-slate-700">{{ $lieu }}:</span> {{ $details['count'] }} jours <span class="text-slate-400 mx-1">|</span> {{ App\Http\Controllers\PlanningController::convertToHHMM($details['heures']) }}</div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-6 sm:ml-6">
                                                    <div class="text-center">
                                                        <p class="text-sm text-slate-500">Jours</p>
                                                        <p class="font-bold text-2xl text-slate-800">{{ count($stat['lieux']) }}</p>
                                                    </div>
                                                    <div class="text-center">
                                                        <p class="text-sm text-slate-500">Heures</p>
                                                        <p class="font-bold text-2xl text-slate-800">{{ App\Http\Controllers\PlanningController::convertToHHMM($stat['total_heures']) }}</p>
                                                    </div>
                                                    <div class="flex items-center space-x-1 pl-6 border-l border-slate-200">
                                                        <a href="{{ url('/plannings/view-monthly-calendar/'.$stat['employe']->id.'/'.$mois.'/'.$anneeActuelle) }}" class="p-2 rounded-full text-slate-500 hover:text-purple-600 hover:bg-purple-100 transition" title="Voir">
                                                            <i class="fas fa-eye w-5 h-5"></i>
                                                        </a>
                                                        <a href="#" onclick="modifierPlanning('{{ $stat['employe']->id }}', '{{ $mois }}', '{{ $anneeActuelle }}')" class="p-2 rounded-full text-slate-500 hover:text-blue-600 hover:bg-blue-100 transition" title="Modifier">
                                                            <i class="fas fa-edit w-5 h-5"></i>
                                                        </a>
                                                        <a href="{{ route('plannings.export-pdf', ['employe_id' => $stat['employe']->id, 'mois' => $mois, 'annee' => $anneeActuelle]) }}" class="p-2 rounded-full text-slate-500 hover:text-green-600 hover:bg-green-100 transition" title="Télécharger PDF" download>
                                                            <i class="fas fa-download w-5 h-5"></i>
                                                        </a>
                                                        <a href="#" onclick="openConfirmModal('{{ $stat['employe']->id }}', '{{ $mois }}', '{{ $stat['employe']->nom }} {{ $stat['employe']->prenom }}', '{{ \Carbon\Carbon::create(null, $mois, 1)->locale('fr')->monthName }}', '{{ $anneeActuelle }}')" class="p-2 rounded-full text-slate-500 hover:text-red-600 hover:bg-red-100 transition" title="Supprimer">
                                                            <i class="fas fa-trash w-5 h-5"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Boîte de dialogue modale de confirmation -->
<div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0" style="display: none;">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
    <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg mx-4">
        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg leading-6 font-bold text-slate-900">Confirmation de suppression</h3>
                    <div class="mt-2">
                        <div class="text-sm text-slate-500" id="confirmModalText"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <a href="#" id="confirmDeleteBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition">Supprimer</a>
            <button type="button" onclick="closeConfirmModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">Annuler</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openConfirmModal(employeId, mois, employeName, monthName, annee) {
        document.getElementById('confirmModalText').innerHTML = `Êtes-vous sûr de vouloir supprimer tous les plannings de <strong>${employeName}</strong> pour le mois de <strong>${monthName}</strong> ?<br>Cette action est irréversible.`;
        const monthStr = mois.toString().padStart(2, '0');
        const yearMonth = annee + '-' + monthStr;
        const deleteUrl = '{{ route("plannings.destroy_monthly_confirm", [":employeId", ":yearMonth"]) }}'.replace(':employeId', employeId).replace(':yearMonth', yearMonth);
        document.getElementById('confirmDeleteBtn').href = deleteUrl;
        document.getElementById('confirmModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('confirmModal');
        modal.addEventListener('click', (e) => (e.target === modal) && closeConfirmModal());
        document.addEventListener('keydown', (e) => (e.key === 'Escape' && modal.style.display === 'flex') && closeConfirmModal());
    });

    function modifierPlanning(employeId, mois, annee) {
        @if(auth()->user() && auth()->user()->isEmployeur())
            window.location.href = `{{ url('/plannings/edit-monthly-calendar') }}?employe_id=${employeId}&mois=${mois}&annee=${annee}`;
        @else
            alert('Vous devez être connecté en tant qu\'employeur pour modifier les plannings.');
        @endif
    }

    function createPlanning() {
        const employe_id = document.getElementById('employe_id').value;
        const mois = document.getElementById('mois').value;
        const annee = document.getElementById('annee').value;
        if (!employe_id) {
            alert('Veuillez sélectionner un employé.');
            return;
        }
        window.location.href = `{{ url('/plannings/create-monthly-calendar') }}?employe_id=${employe_id}&mois=${mois}&annee=${annee}`;
    }
    </script>
    @endpush
@endsection

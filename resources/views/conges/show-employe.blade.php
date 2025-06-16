<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Détails de la demande de congé
            </h2>
            <a href="{{ route('employe.conges.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium mb-4">Informations de l'employé</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom complet</dt>
                                    <dd class="mt-1 text-sm">{{ $conge->employe->nom }} {{ $conge->employe->prenom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                    <dd class="mt-1 text-sm">{{ $conge->employe->user->email }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium mb-4">Détails du congé</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Période</dt>
                                    <dd class="mt-1 text-sm">
                                        Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}
                                        au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Durée</dt>
                                    <dd class="mt-1 text-sm">{{ number_format($conge->duree, 1) }} jours</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Motif</dt>
                                    <dd class="mt-1 text-sm">{{ $conge->motif }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($conge->statut === 'en_attente') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($conge->statut === 'accepte') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $conge->statut)) }}
                                        </span>
                                    </dd>
                                </div>
                                @if($conge->commentaire)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Commentaire</dt>
                                    <dd class="mt-1 text-sm">{{ $conge->commentaire }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($conge->statut === 'en_attente' && $conge->employe_id === Auth::user()->employe->id)
                        <div class="mt-8 flex justify-end space-x-4">
                            <form action="{{ route('employe.conges.annuler', $conge) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande de congé ?');">
                                @csrf
                                @method('DELETE')
                                <x-danger-button>
                                    Annuler la demande
                                </x-danger-button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Détails de la demande de congé
            </h2>
            <a href="{{ route('conges.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de l'employé</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $conge->employe->nom }} {{ $conge->employe->prenom }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $conge->employe->user->email }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Détails du congé</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Période</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        Du {{ \Carbon\Carbon::parse($conge->date_debut)->format('d/m/Y') }}
                                        au {{ \Carbon\Carbon::parse($conge->date_fin)->format('d/m/Y') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Durée</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ number_format($conge->duree, 1) }} jours</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Motif</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $conge->motif }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Statut</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($conge->statut === 'en_attente') bg-yellow-100 text-yellow-800
                                            @elseif($conge->statut === 'accepte') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($conge->statut) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($conge->statut === 'en_attente')
                        <div class="mt-8 flex justify-end space-x-4">
                            <form action="{{ route('conges.update-status', $conge) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="statut" value="accepte">
                                <x-primary-button>
                                    Accepter la demande
                                </x-primary-button>
                            </form>
                            <form action="{{ route('conges.update-status', $conge) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="statut" value="refuse">
                                <x-danger-button>
                                    Refuser la demande
                                </x-danger-button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

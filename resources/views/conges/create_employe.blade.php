<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Demande de congé') }}
            </h2>
            <a href="{{ route('employe.conges.calendar') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                {{ __('Voir le calendrier') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('employe.conges.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="date_debut" :value="__('Date de début')" />
                                <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" :value="old('date_debut')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('date_debut')" />
                            </div>

                            <div>
                                <x-input-label for="date_fin" :value="__('Date de fin')" />
                                <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" :value="old('date_fin')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('date_fin')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="motif" :value="__('Motif du congé')" />
                                <select id="motif" name="motif" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Sélectionnez un motif</option>
                                    <option value="Congés payés" {{ old('motif') == 'Congés payés' ? 'selected' : '' }}>Congés payés</option>
                                    <option value="RTT" {{ old('motif') == 'RTT' ? 'selected' : '' }}>RTT</option>
                                    <option value="Maladie" {{ old('motif') == 'Maladie' ? 'selected' : '' }}>Maladie</option>
                                    <option value="Événement familial" {{ old('motif') == 'Événement familial' ? 'selected' : '' }}>Événement familial</option>
                                    <option value="Sans solde" {{ old('motif') == 'Sans solde' ? 'selected' : '' }}>Sans solde</option>
                                    <option value="Autre" {{ old('motif') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('motif')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="commentaire" :value="__('Commentaire (optionnel)')" />
                                <textarea id="commentaire" name="commentaire" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('commentaire') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('commentaire')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Envoyer ma demande') }}</x-primary-button>
                            <a href="{{ route('employe.conges.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

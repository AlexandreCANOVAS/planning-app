<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un congé') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('conges.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="employe_id" :value="__('Employé')" />
                                <select id="employe_id" name="employe_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Sélectionnez un employé</option>
                                    @foreach($employes as $employe)
                                        <option value="{{ $employe->id }}" {{ old('employe_id') == $employe->id ? 'selected' : '' }}>
                                            {{ $employe->nom }} {{ $employe->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('employe_id')" />
                            </div>

                            <div>
                                <x-input-label for="statut" :value="__('Statut')" />
                                <select id="statut" name="statut" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="accepte" {{ old('statut') == 'accepte' ? 'selected' : '' }}>Accepté</option>
                                    <option value="refuse" {{ old('statut') == 'refuse' ? 'selected' : '' }}>Refusé</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('statut')" />
                            </div>

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
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Ajouter') }}</x-primary-button>
                            <a href="{{ route('conges.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 
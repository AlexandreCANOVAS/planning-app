<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le planning') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('plannings.update', $planning) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="employe_id" :value="__('Employé')" />
                                <select id="employe_id" name="employe_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Sélectionnez un employé</option>
                                    @foreach($employes as $employe)
                                        <option value="{{ $employe->id }}" {{ old('employe_id', $planning->employe_id) == $employe->id ? 'selected' : '' }}>
                                            {{ $employe->nom }} {{ $employe->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('employe_id')" />
                            </div>

                            <div>
                                <x-input-label for="lieu_id" :value="__('Lieu de travail')" />
                                <select id="lieu_id" name="lieu_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Sélectionnez un lieu</option>
                                    @foreach($lieux as $lieu)
                                        <option value="{{ $lieu->id }}" {{ old('lieu_id', $planning->lieu_id) == $lieu->id ? 'selected' : '' }}>
                                            {{ $lieu->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('lieu_id')" />
                            </div>

                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $planning->date)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('date')" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="heure_debut" :value="__('Heure de début')" />
                                    <x-text-input id="heure_debut" name="heure_debut" type="time" class="mt-1 block w-full" :value="old('heure_debut', \Carbon\Carbon::parse($planning->heure_debut)->format('H:i'))" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('heure_debut')" />
                                </div>

                                <div>
                                    <x-input-label for="heure_fin" :value="__('Heure de fin')" />
                                    <x-text-input id="heure_fin" name="heure_fin" type="time" class="mt-1 block w-full" :value="old('heure_fin', \Carbon\Carbon::parse($planning->heure_fin)->format('H:i'))" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('heure_fin')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Mettre à jour') }}</x-primary-button>
                            <a href="{{ route('plannings.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 
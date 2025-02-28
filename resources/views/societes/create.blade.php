<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer votre entreprise') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('societes.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nom" :value="__('Nom')" />
                                <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom')" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                            </div>

                            <div>
                                <x-input-label for="siret" :value="__('SIRET')" />
                                <x-text-input id="siret" name="siret" type="text" class="mt-1 block w-full" :value="old('siret')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('siret')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="forme_juridique" :value="__('Forme Juridique')" />
                                <select id="forme_juridique" name="forme_juridique" class="block mt-1 w-full rounded-md border-gray-300" required>
                                    <option value="">Sélectionnez une forme juridique</option>
                                    <option value="SARL" {{ old('forme_juridique') == 'SARL' ? 'selected' : '' }}>SARL</option>
                                    <option value="EURL" {{ old('forme_juridique') == 'EURL' ? 'selected' : '' }}>EURL</option>
                                    <option value="SAS" {{ old('forme_juridique') == 'SAS' ? 'selected' : '' }}>SAS</option>
                                    <option value="SASU" {{ old('forme_juridique') == 'SASU' ? 'selected' : '' }}>SASU</option>
                                    <option value="SA" {{ old('forme_juridique') == 'SA' ? 'selected' : '' }}>SA</option>
                                    <option value="SCI" {{ old('forme_juridique') == 'SCI' ? 'selected' : '' }}>SCI</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('forme_juridique')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="adresse" :value="__('Adresse')" />
                                <x-text-area id="adresse" name="adresse" class="mt-1 block w-full" required>{{ old('adresse') }}</x-text-area>
                                <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="telephone" :value="__('Téléphone')" />
                                <x-text-input id="telephone" name="telephone" type="tel" class="mt-1 block w-full" :value="old('telephone')" placeholder="01 23 45 67 89" />
                                <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Créer') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 
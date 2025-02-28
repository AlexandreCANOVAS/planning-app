<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier la société') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('societes.update', $societe) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="nom" :value="__('Nom de la société')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $societe->nom)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <div>
                            <x-input-label for="siret" :value="__('Numéro SIRET')" />
                            <x-text-input id="siret" name="siret" type="text" class="mt-1 block w-full" :value="old('siret', $societe->siret)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('siret')" />
                        </div>

                        <div>
                            <x-input-label for="forme_juridique" :value="__('Forme juridique')" />
                            <x-text-input id="forme_juridique" name="forme_juridique" type="text" class="mt-1 block w-full" :value="old('forme_juridique', $societe->forme_juridique)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('forme_juridique')" />
                        </div>

                        <div>
                            <x-input-label for="adresse" :value="__('Adresse')" />
                            <x-text-area id="adresse" name="adresse" class="mt-1 block w-full" required>{{ old('adresse', $societe->adresse) }}</x-text-area>
                            <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                        </div>

                        <div>
                            <x-input-label for="telephone" :value="__('Téléphone')" />
                            <x-text-input id="telephone" name="telephone" type="tel" class="mt-1 block w-full" :value="old('telephone', $societe->telephone)" />
                            <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Mettre à jour') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 
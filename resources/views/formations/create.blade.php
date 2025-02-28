<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter une formation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('formations.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="nom" :value="__('Nom de la formation')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="duree_validite_mois" :value="__('Durée de validité (en mois)')" />
                            <x-text-input id="duree_validite_mois" name="duree_validite_mois" type="number" class="mt-1 block w-full" :value="old('duree_validite_mois')" min="1" />
                            <x-input-error class="mt-2" :messages="$errors->get('duree_validite_mois')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                            <a href="{{ route('formations.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

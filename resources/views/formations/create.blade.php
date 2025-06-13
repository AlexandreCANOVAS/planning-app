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
                            <x-input-label for="objectifs_pedagogiques" :value="__('Objectifs pédagogiques')" />
                            <textarea id="objectifs_pedagogiques" name="objectifs_pedagogiques" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('objectifs_pedagogiques') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Décrivez les compétences et connaissances que cette formation permet d'acquérir.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('objectifs_pedagogiques')" />
                        </div>

                        <div>
                            <x-input-label for="prerequis" :value="__('Prérequis')" />
                            <textarea id="prerequis" name="prerequis" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('prerequis') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Indiquez les connaissances ou compétences nécessaires avant de suivre cette formation.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('prerequis')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="duree_validite_mois" :value="__('Durée de validité (en mois)')" />
                                <x-text-input id="duree_validite_mois" name="duree_validite_mois" type="number" class="mt-1 block w-full" :value="old('duree_validite_mois')" min="1" />
                                <x-input-error class="mt-2" :messages="$errors->get('duree_validite_mois')" />
                            </div>

                            <div>
                                <x-input-label for="duree_recommandee_heures" :value="__('Durée recommandée (en heures)')" />
                                <x-text-input id="duree_recommandee_heures" name="duree_recommandee_heures" type="number" class="mt-1 block w-full" :value="old('duree_recommandee_heures')" min="1" />
                                <x-input-error class="mt-2" :messages="$errors->get('duree_recommandee_heures')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="organisme_formateur" :value="__('Organisme formateur')" />
                                <x-text-input id="organisme_formateur" name="organisme_formateur" type="text" class="mt-1 block w-full" :value="old('organisme_formateur')" />
                                <x-input-error class="mt-2" :messages="$errors->get('organisme_formateur')" />
                            </div>

                            <div>
                                <x-input-label for="cout" :value="__('Coût de la formation (€)')" />
                                <x-text-input id="cout" name="cout" type="number" step="0.01" class="mt-1 block w-full" :value="old('cout')" />
                                <x-input-error class="mt-2" :messages="$errors->get('cout')" />
                            </div>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="formateur_interne" value="1" {{ old('formateur_interne') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Formation dispensée par un formateur interne') }}</span>
                            </label>
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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier l\'employé') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('employes.update', $employe) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nom" :value="__('Nom')" />
                                <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $employe->nom)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                            </div>

                            <div>
                                <x-input-label for="prenom" :value="__('Prénom')" />
                                <x-text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" :value="old('prenom', $employe->prenom)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('prenom')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $employe->email)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="telephone" :value="__('Téléphone')" />
                                <x-text-input id="telephone" name="telephone" type="tel" class="mt-1 block w-full" :value="old('telephone', $employe->telephone)" />
                                <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                            </div>
                        </div>

                        <!-- Section Formations -->
                        <div class="mt-8 border-t pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4" id="formations">Formations</h3>
                            @if($formations->isEmpty())
                                <p class="text-gray-500">Aucune formation disponible.</p>
                            @else
                                <div class="space-y-4">
                                    @foreach($formations as $formation)
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 pt-1">
                                                    <input type="checkbox" 
                                                           id="formation_{{ $formation->id }}"
                                                           name="formations[{{ $formation->id }}][selected]" 
                                                           value="1"
                                                           {{ $employe->formations->contains($formation->id) ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <label for="formation_{{ $formation->id }}" class="font-medium text-gray-700">{{ $formation->nom }}</label>
                                                    @if($formation->description)
                                                        <p class="text-sm text-gray-500 mt-1">{{ $formation->description }}</p>
                                                    @endif
                                                    
                                                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <x-input-label for="date_obtention_{{ $formation->id }}" :value="__('Date d\'obtention')" />
                                                            <x-text-input id="date_obtention_{{ $formation->id }}" 
                                                                         type="date" 
                                                                         name="formations[{{ $formation->id }}][date_obtention]"
                                                                         :value="$employe->formations->find($formation->id)?->pivot?->date_obtention"
                                                                         class="mt-1 block w-full" />
                                                        </div>
                                                        <div>
                                                            <x-input-label for="date_recyclage_{{ $formation->id }}" :value="__('Date de recyclage')" />
                                                            <x-text-input id="date_recyclage_{{ $formation->id }}" 
                                                                         type="date" 
                                                                         name="formations[{{ $formation->id }}][date_recyclage]"
                                                                         :value="$employe->formations->find($formation->id)?->pivot?->date_recyclage"
                                                                         class="mt-1 block w-full" />
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="mt-4">
                                                        <x-input-label for="commentaire_{{ $formation->id }}" :value="__('Commentaire')" />
                                                        <textarea id="commentaire_{{ $formation->id }}" 
                                                                  name="formations[{{ $formation->id }}][commentaire]"
                                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500"
                                                                  rows="2">{{ $employe->formations->find($formation->id)?->pivot?->commentaire }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <x-primary-button>{{ __('Mettre à jour') }}</x-primary-button>
                            <a href="{{ route('employes.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
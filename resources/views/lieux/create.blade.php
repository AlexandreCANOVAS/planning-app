<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un lieu de travail') }}
        </h2>
    </x-slot>
    
    @include('lieux.partials.address-autocomplete')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('lieux.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <!-- Informations principales -->
                            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-building text-blue-500 mr-2"></i> Informations principales
                                </h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="nom" :value="__('Nom du lieu')" class="text-gray-700 font-medium" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-tag text-gray-400"></i>
                                            </div>
                                            <x-text-input id="nom" name="nom" type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('nom')" required autofocus placeholder="Nom du lieu de travail" />
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                                    </div>
                                </div>
                            </div>

                            <!-- Adresse -->
                            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i> Adresse
                                </h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="adresse" :value="__('Adresse complète')" class="text-gray-700 font-medium" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-home text-gray-400"></i>
                                            </div>
                                            <x-text-area id="adresse" name="adresse" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required placeholder="Numéro et nom de rue">{{ old('adresse') }}</x-text-area>
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="code_postal" :value="__('Code postal')" class="text-gray-700 font-medium" />
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-map-pin text-gray-400"></i>
                                                </div>
                                                <x-text-input id="code_postal" name="code_postal" type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('code_postal')" required placeholder="Code postal" />
                                            </div>
                                            <x-input-error class="mt-2" :messages="$errors->get('code_postal')" />
                                        </div>

                                        <div>
                                            <x-input-label for="ville" :value="__('Ville')" class="text-gray-700 font-medium" />
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-city text-gray-400"></i>
                                                </div>
                                                <x-text-input id="ville" name="ville" type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('ville')" required placeholder="Ville" />
                                            </div>
                                            <x-input-error class="mt-2" :messages="$errors->get('ville')" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Coordonnées géographiques -->
                            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-map-marked-alt text-blue-500 mr-2"></i> Coordonnées géographiques
                                </h4>
                                
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <x-input-label for="latitude" :value="__('Latitude')" class="text-gray-700 font-medium" />
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-map-pin text-gray-400"></i>
                                                </div>
                                                <x-text-input id="latitude" name="latitude" type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('latitude')" placeholder="Ex: 48.8566" />
                                            </div>
                                            <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                                        </div>
                                        
                                        <div>
                                            <x-input-label for="longitude" :value="__('Longitude')" class="text-gray-700 font-medium" />
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-map-pin text-gray-400"></i>
                                                </div>
                                                <x-text-input id="longitude" name="longitude" type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('longitude')" placeholder="Ex: 2.3522" />
                                            </div>
                                            <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                                        </div>
                                    </div>
                                    
                                    <div class="bg-blue-50 p-4 rounded-md">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-info-circle text-blue-500"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-blue-700">
                                                    Ces coordonnées permettent d'afficher le lieu sur la carte interactive. Vous pouvez les obtenir sur <a href="https://www.google.com/maps" target="_blank" class="font-medium underline">Google Maps</a> en faisant un clic droit sur l'emplacement et en sélectionnant "Plus d'infos sur cet endroit".
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Informations de contact -->
                            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-100">
                                <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-address-card text-blue-500 mr-2"></i> Informations de contact
                                </h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="telephone" :value="__('Téléphone')" class="text-gray-700 font-medium" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-phone text-gray-400"></i>
                                            </div>
                                            <x-text-input id="telephone" name="telephone" type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('telephone')" placeholder="Numéro de téléphone" />
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                                    </div>
                                    
                                    <div>
                                        <x-input-label for="horaires" :value="__('Horaires d\'ouverture')" class="text-gray-700 font-medium" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-clock text-gray-400"></i>
                                            </div>
                                            <x-text-input id="horaires" name="horaires" type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('horaires')" placeholder="Ex: Lun-Ven 9h-18h" />
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('horaires')" />
                                    </div>
                                    
                                    <div>
                                        <x-input-label for="contact_principal" :value="__('Contact principal')" class="text-gray-700 font-medium" />
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-user-tie text-gray-400"></i>
                                            </div>
                                            <x-text-input id="contact_principal" name="contact_principal" type="text" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" :value="old('contact_principal')" placeholder="Nom et fonction du contact" />
                                        </div>
                                        <x-input-error class="mt-2" :messages="$errors->get('contact_principal')" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Ajouter') }}</x-primary-button>
                            <a href="{{ route('lieux.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
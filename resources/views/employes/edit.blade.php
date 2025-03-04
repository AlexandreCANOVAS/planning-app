<x-app-layout>
    <x-slot name="header">
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg p-8">
            <div class="absolute top-0 right-0 transform translate-x-1/3 -translate-y-1/3">
                <div class="w-48 h-48 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="absolute bottom-0 left-0 transform -translate-x-1/3 translate-y-1/3">
                <div class="w-32 h-32 rounded-full bg-white opacity-10"></div>
            </div>
            <div class="relative">
                <h2 class="text-3xl font-bold text-white mb-2">
                    {{ $employe->prenom }} {{ $employe->nom }}
                </h2>
                <p class="text-blue-100 text-lg flex items-center">
                    <i class="fas fa-id-badge mr-2"></i>
                    Modifier les informations de l'employé
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl">
                <div class="p-8">
                    @if($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                <strong class="text-red-700 font-medium">Veuillez corriger les erreurs suivantes :</strong>
                            </div>
                            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employes.update', $employe) }}" class="space-y-8" id="employeForm">
                        @csrf
                        @method('PUT')

                        <!-- Section Identité -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-user mr-3 text-blue-600"></i>
                                Identité
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nom -->
                                <div>
                                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" id="nom" name="nom"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('nom', $employe->nom) }}" required>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                                </div>

                                <!-- Prénom -->
                                <div>
                                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" id="prenom" name="prenom"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('prenom', $employe->prenom) }}" required>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('prenom')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email professionnel</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input type="email" id="email" name="email"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('email', $employe->email) }}" required>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Téléphone -->
                                <div>
                                    <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                        <input type="tel" id="telephone" name="telephone"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('telephone', $employe->telephone) }}"
                                            placeholder="01 23 45 67 89">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                                </div>
                            </div>
                        </div>

                        <!-- Section Formations -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-graduation-cap mr-3 text-blue-600"></i>
                                Formations
                            </h3>
                            @if($formations->isEmpty())
                                <div class="bg-gray-50 rounded-lg p-6 text-center">
                                    <i class="fas fa-book-open text-gray-400 text-4xl mb-3"></i>
                                    <p class="text-gray-500">Aucune formation disponible pour le moment.</p>
                                </div>
                            @else
                                <div class="space-y-6">
                                    @foreach($formations as $formation)
                                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 hover:border-blue-300 transition-colors duration-200">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 pt-1">
                                                    <input type="checkbox" 
                                                           id="formation_{{ $formation->id }}"
                                                           name="formations[{{ $formation->id }}][selected]" 
                                                           value="1"
                                                           {{ $employe->formations->contains($formation->id) ? 'checked' : '' }}
                                                           class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition duration-150">
                                                </div>
                                                <div class="ml-4 flex-1">
                                                    <label for="formation_{{ $formation->id }}" class="text-lg font-medium text-gray-900 mb-1 block">
                                                        {{ $formation->nom }}
                                                    </label>
                                                    @if($formation->description)
                                                        <p class="text-gray-600 text-sm mb-4">{{ $formation->description }}</p>
                                                    @endif
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                                        <div>
                                                            <label for="date_obtention_{{ $formation->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                                Date d'obtention
                                                            </label>
                                                            <div class="relative rounded-md shadow-sm">
                                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <i class="fas fa-calendar-check text-gray-400"></i>
                                                                </div>
                                                                <input type="date" 
                                                                       id="date_obtention_{{ $formation->id }}"
                                                                       name="formations[{{ $formation->id }}][date_obtention]"
                                                                       value="{{ $employe->formations->find($formation->id)?->pivot?->date_obtention }}"
                                                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                                            </div>
                                                        </div>
                                                        
                                                        <div>
                                                            <label for="date_recyclage_{{ $formation->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                                Date de recyclage
                                                            </label>
                                                            <div class="relative rounded-md shadow-sm">
                                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <i class="fas fa-sync text-gray-400"></i>
                                                                </div>
                                                                <input type="date"
                                                                       id="date_recyclage_{{ $formation->id }}"
                                                                       name="formations[{{ $formation->id }}][date_recyclage]"
                                                                       value="{{ $employe->formations->find($formation->id)?->pivot?->date_recyclage }}"
                                                                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4">
                                                        <label for="commentaire_{{ $formation->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                            Commentaire
                                                        </label>
                                                        <div class="relative rounded-md shadow-sm">
                                                            <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                                                <i class="fas fa-comment text-gray-400"></i>
                                                            </div>
                                                            <textarea id="commentaire_{{ $formation->id }}"
                                                                      name="formations[{{ $formation->id }}][commentaire]"
                                                                      class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                                                      rows="2"
                                                                      placeholder="Ajoutez un commentaire sur cette formation...">{{ $employe->formations->find($formation->id)?->pivot?->commentaire }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Boutons d'action -->
                        <div class="pt-6 flex flex-col sm:flex-row gap-4">
                            <button type="submit" class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-800 text-white font-medium rounded-lg shadow-lg hover:from-blue-700 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Enregistrer les modifications
                            </button>
                            <a href="{{ route('employes.index') }}" 
                               class="flex-1 sm:flex-initial inline-flex justify-center items-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Animation du formulaire
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('form > div');
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    section.style.transition = 'all 0.5s ease-out';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, 100 * (index + 1));
            });
        });

        // Formattage automatique du téléphone
        const telInput = document.getElementById('telephone');
        telInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 10) value = value.slice(0, 10);
            e.target.value = value.replace(/(\d{2})(?=\d)/g, '$1 ').trim();
        });

        // Validation du formulaire
        document.getElementById('employeForm').addEventListener('submit', function(e) {
            const telValue = telInput.value.replace(/\s/g, '');
            if (telValue && telValue.length !== 10) {
                e.preventDefault();
                alert('Le numéro de téléphone doit contenir 10 chiffres.');
                return;
            }
        });
    </script>
    @endpush
</x-app-layout>
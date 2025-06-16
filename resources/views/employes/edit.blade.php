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

                    <form method="POST" action="{{ route('employes.update', $employe) }}" class="space-y-8" id="employeForm" enctype="multipart/form-data">
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
                                        <input type="text" id="nom" name="nom" required
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('nom', $employe->nom) }}">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                                </div>

                                <!-- Prénom -->
                                <div>
                                    <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                                    <input type="text" id="prenom" name="prenom" required
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                        value="{{ old('prenom', $employe->prenom) }}">
                                    <x-input-error class="mt-2" :messages="$errors->get('prenom')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input type="email" id="email" name="email" required
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('email', $employe->email) }}">
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
                                        <input type="text" id="telephone" name="telephone"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('telephone', $employe->telephone) }}">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                                </div>

                                <!-- Adresse -->
                                <div class="col-span-2">
                                    <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                                        </div>
                                        <input type="text" id="adresse" name="adresse"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('adresse', $employe->adresse) }}"
                                            placeholder="123 rue de la Paix, 75000 Paris">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                                </div>

                                <!-- Date d'embauche -->
                                <div>
                                    <label for="date_embauche" class="block text-sm font-medium text-gray-700 mb-2">Date d'embauche</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-calendar-alt text-gray-400"></i>
                                        </div>
                                        <input type="date" id="date_embauche" name="date_embauche"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('date_embauche', $employe->date_embauche) }}">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('date_embauche')" />
                                </div>
                            </div>
                        </div>

                        <!-- Section Informations professionnelles -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-briefcase mr-3 text-blue-600"></i>
                                Informations professionnelles
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Poste/Fonction -->
                                <div>
                                    <label for="poste" class="block text-sm font-medium text-gray-700 mb-2">Poste/Fonction</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-briefcase text-gray-400"></i>
                                        </div>
                                        <input type="text" id="poste" name="poste"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('poste', $employe->poste) }}" placeholder="Titre du poste occupé">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('poste')" />
                                </div>

                                <!-- Type de contrat -->
                                <div>
                                    <label for="type_contrat" class="block text-sm font-medium text-gray-700 mb-2">Type de contrat</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-file-signature text-gray-400"></i>
                                        </div>
                                        <select id="type_contrat" name="type_contrat"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                            <option value="">Sélectionner un type de contrat</option>
                                            <option value="CDI" {{ old('type_contrat', $employe->type_contrat) == 'CDI' ? 'selected' : '' }}>CDI</option>
                                            <option value="CDD" {{ old('type_contrat', $employe->type_contrat) == 'CDD' ? 'selected' : '' }}>CDD</option>
                                            <option value="Intérim" {{ old('type_contrat', $employe->type_contrat) == 'Intérim' ? 'selected' : '' }}>Intérim</option>
                                            <option value="Stage" {{ old('type_contrat', $employe->type_contrat) == 'Stage' ? 'selected' : '' }}>Stage</option>
                                            <option value="Alternance" {{ old('type_contrat', $employe->type_contrat) == 'Alternance' ? 'selected' : '' }}>Alternance</option>
                                            <option value="Freelance" {{ old('type_contrat', $employe->type_contrat) == 'Freelance' ? 'selected' : '' }}>Freelance</option>
                                        </select>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('type_contrat')" />
                                </div>

                                <!-- Date début contrat -->
                                <div>
                                    <label for="date_debut_contrat" class="block text-sm font-medium text-gray-700 mb-2">Date de début du contrat</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-calendar-alt text-gray-400"></i>
                                        </div>
                                        <input type="date" id="date_debut_contrat" name="date_debut_contrat"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('date_debut_contrat', $employe->date_debut_contrat) }}">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('date_debut_contrat')" />
                                </div>

                                <!-- Date fin contrat -->
                                <div>
                                    <label for="date_fin_contrat" class="block text-sm font-medium text-gray-700 mb-2">Date de fin du contrat</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-calendar-alt text-gray-400"></i>
                                        </div>
                                        <input type="date" id="date_fin_contrat" name="date_fin_contrat"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('date_fin_contrat', $employe->date_fin_contrat) }}">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('date_fin_contrat')" />
                                </div>

                                <!-- Temps de travail -->
                                <div>
                                    <label for="temps_travail" class="block text-sm font-medium text-gray-700 mb-2">Temps de travail</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-clock text-gray-400"></i>
                                        </div>
                                        <select id="temps_travail" name="temps_travail"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            onchange="togglePourcentageTravail()">
                                            <option value="">Sélectionner un temps de travail</option>
                                            <option value="Temps plein" {{ old('temps_travail', $employe->temps_travail) == 'Temps plein' ? 'selected' : '' }}>Temps plein</option>
                                            <option value="Temps partiel" {{ old('temps_travail', $employe->temps_travail) == 'Temps partiel' ? 'selected' : '' }}>Temps partiel</option>
                                        </select>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('temps_travail')" />
                                </div>

                                <!-- Pourcentage temps partiel -->
                                <div id="pourcentage_travail_container" style="{{ old('temps_travail', $employe->temps_travail) == 'Temps partiel' ? '' : 'display: none;' }}">
                                    <label for="pourcentage_travail" class="block text-sm font-medium text-gray-700 mb-2">Pourcentage du temps de travail</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-percentage text-gray-400"></i>
                                        </div>
                                        <input type="number" id="pourcentage_travail" name="pourcentage_travail"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('pourcentage_travail', $employe->pourcentage_travail) }}" min="1" max="99" placeholder="Ex: 80">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('pourcentage_travail')" />
                                </div>
                            </div>
                        </div>

                        <!-- Section Informations personnelles -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-user-shield mr-3 text-blue-600"></i>
                                Informations personnelles
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Photo de profil -->
                                <div>
                                    <label for="photo_profil" class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if($employe->photo_profil)
                                                <img src="{{ asset('storage/' . $employe->photo_profil) }}" alt="Photo de {{ $employe->prenom }} {{ $employe->nom }}" class="h-16 w-16 rounded-full object-cover">
                                            @else
                                                <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-500 text-xl font-semibold">{{ substr($employe->prenom, 0, 1) }}{{ substr($employe->nom, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" id="photo_profil" name="photo_profil" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                            <p class="mt-1 text-xs text-gray-500">JPG, PNG ou GIF. 2 Mo maximum.</p>
                                        </div>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('photo_profil')" />
                                </div>

                                <!-- Date de naissance -->
                                <div>
                                    <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-birthday-cake text-gray-400"></i>
                                        </div>
                                        <input type="date" id="date_naissance" name="date_naissance"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('date_naissance', $employe->date_naissance) }}">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('date_naissance')" />
                                </div>

                                <!-- Numéro de sécurité sociale -->
                                <div>
                                    <label for="numero_securite_sociale" class="block text-sm font-medium text-gray-700 mb-2">Numéro de sécurité sociale</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-id-card text-gray-400"></i>
                                        </div>
                                        <input type="text" id="numero_securite_sociale" name="numero_securite_sociale"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('numero_securite_sociale', $employe->numero_securite_sociale) }}"
                                            placeholder="1 99 12 34 567 890 12">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('numero_securite_sociale')" />
                                </div>

                                <!-- Situation familiale -->
                                <div>
                                    <label for="situation_familiale" class="block text-sm font-medium text-gray-700 mb-2">Situation familiale</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user-friends text-gray-400"></i>
                                        </div>
                                        <select id="situation_familiale" name="situation_familiale"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                            <option value="" {{ old('situation_familiale', $employe->situation_familiale) == '' ? 'selected' : '' }}>Sélectionner...</option>
                                            <option value="Célibataire" {{ old('situation_familiale', $employe->situation_familiale) == 'Célibataire' ? 'selected' : '' }}>Célibataire</option>
                                            <option value="Marié(e)" {{ old('situation_familiale', $employe->situation_familiale) == 'Marié(e)' ? 'selected' : '' }}>Marié(e)</option>
                                            <option value="Pacsé(e)" {{ old('situation_familiale', $employe->situation_familiale) == 'Pacsé(e)' ? 'selected' : '' }}>Pacsé(e)</option>
                                            <option value="Divorcé(e)" {{ old('situation_familiale', $employe->situation_familiale) == 'Divorcé(e)' ? 'selected' : '' }}>Divorcé(e)</option>
                                            <option value="Veuf/Veuve" {{ old('situation_familiale', $employe->situation_familiale) == 'Veuf/Veuve' ? 'selected' : '' }}>Veuf/Veuve</option>
                                        </select>
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('situation_familiale')" />
                                </div>

                                <!-- Nombre d'enfants -->
                                <div>
                                    <label for="nombre_enfants" class="block text-sm font-medium text-gray-700 mb-2">Nombre d'enfants</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-child text-gray-400"></i>
                                        </div>
                                        <input type="number" id="nombre_enfants" name="nombre_enfants"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('nombre_enfants', $employe->nombre_enfants) }}" min="0" step="1">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('nombre_enfants')" />
                                </div>

                                <!-- Contact d'urgence - Nom -->
                                <div>
                                    <label for="contact_urgence_nom" class="block text-sm font-medium text-gray-700 mb-2">Contact d'urgence - Nom</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-ambulance text-gray-400"></i>
                                        </div>
                                        <input type="text" id="contact_urgence_nom" name="contact_urgence_nom"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('contact_urgence_nom', $employe->contact_urgence_nom) }}"
                                            placeholder="Nom et prénom du contact">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('contact_urgence_nom')" />
                                </div>

                                <!-- Contact d'urgence - Téléphone -->
                                <div>
                                    <label for="contact_urgence_telephone" class="block text-sm font-medium text-gray-700 mb-2">Contact d'urgence - Téléphone</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-phone-alt text-gray-400"></i>
                                        </div>
                                        <input type="text" id="contact_urgence_telephone" name="contact_urgence_telephone"
                                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                            value="{{ old('contact_urgence_telephone', $employe->contact_urgence_telephone) }}"
                                            placeholder="06 12 34 56 78">
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('contact_urgence_telephone')" />
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
                                                           {{ ($employe->formations && $employe->formations->contains($formation->id)) ? 'checked' : '' }}
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
                                                                       value="{{ ($employe->formations && $employe->formations->find($formation->id)) ? $employe->formations->find($formation->id)->pivot->date_obtention : '' }}"
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
                                                                       value="{{ $employe->formations && $employe->formations->find($formation->id) ? $employe->formations->find($formation->id)->pivot->date_recyclage : '' }}"
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
                                                                      placeholder="Ajoutez un commentaire sur cette formation...">{{ ($employe->formations !== null && $employe->formations->find($formation->id)) ? $employe->formations->find($formation->id)->pivot->commentaire : '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Template pour nouvelle formation -->
                            <template id="formation-template">
                                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 hover:border-blue-300 transition-colors duration-200 mb-4 new-formation">
                                    <div class="flex items-start">
                                        <div class="ml-4 flex-1">
                                            <div class="flex justify-between items-center mb-4">
                                                <div class="flex-1">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la formation</label>
                                                    <input type="text" 
                                                           name="new_formations[__INDEX__][nom]" 
                                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                           placeholder="Nom de la formation">
                                                </div>
                                                <div class="ml-4">
                                                    <button type="button" class="text-red-600 hover:text-red-800" onclick="removeNewFormation(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                <textarea name="new_formations[__INDEX__][description]" 
                                                          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                          rows="2"
                                                          placeholder="Description de la formation"></textarea>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'obtention</label>
                                                    <div class="relative rounded-md shadow-sm">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <i class="fas fa-calendar-check text-gray-400"></i>
                                                        </div>
                                                        <input type="date" 
                                                               name="new_formations[__INDEX__][date_obtention]"
                                                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de recyclage</label>
                                                    <div class="relative rounded-md shadow-sm">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <i class="fas fa-sync text-gray-400"></i>
                                                        </div>
                                                        <input type="date"
                                                               name="new_formations[__INDEX__][date_recyclage]"
                                                               class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Commentaire</label>
                                                <div class="relative rounded-md shadow-sm">
                                                    <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                                        <i class="fas fa-comment text-gray-400"></i>
                                                    </div>
                                                    <textarea name="new_formations[__INDEX__][commentaire]"
                                                              class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                                                              rows="2"
                                                              placeholder="Ajoutez un commentaire sur cette formation..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Conteneur pour nouvelles formations -->
                            <div id="new-formations-container" class="space-y-6 mt-6"></div>
                            
                            <!-- Bouton pour ajouter une formation -->
                            <div class="mt-6">
                                <button type="button" id="add-formation" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter une formation
                                </button>
                            </div>
                        </div>

                        <!-- Section Informations administratives -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-file-alt mr-3 text-blue-600"></i>
                                Informations administratives
                            </h3>

                            <!-- Documents administratifs -->
                            <div id="documents" class="mb-8">
                                <h4 class="text-md font-medium text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-folder-open mr-2 text-blue-500"></i>
                                    Documents administratifs
                                </h4>

                                <!-- Liste des documents existants -->
                                @if($employe->documentsAdministratifs && $employe->documentsAdministratifs->count() > 0)
                                    <div class="mb-6 space-y-4">
                                        @foreach($employe->documentsAdministratifs as $index => $document)
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <input type="hidden" name="documents[{{ $index }}][id]" value="{{ $document->id }}">
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <!-- Nom du document -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                                                <input type="text" name="documents[{{ $index }}][nom]" value="{{ old('documents.'.$index.'.nom', $document->nom) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Type de document -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                                <input type="text" name="documents[{{ $index }}][type]" value="{{ old('documents.'.$index.'.type', $document->type) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Numéro du document -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro</label>
                                                                <input type="text" name="documents[{{ $index }}][numero]" value="{{ old('documents.'.$index.'.numero', $document->numero) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Fichier -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
                                                                <div class="flex items-center">
                                                                    @if($document->fichier)
                                                                        <span class="text-xs text-gray-500 mr-2">
                                                                            <a href="{{ asset('storage/'.$document->fichier) }}" target="_blank" class="text-blue-600 hover:underline">
                                                                                <i class="fas fa-file-pdf mr-1"></i> Voir le document
                                                                            </a>
                                                                        </span>
                                                                    @endif
                                                                    <input type="file" name="documents[{{ $index }}][fichier]" 
                                                                        class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Date d'émission -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'émission</label>
                                                                <input type="date" name="documents[{{ $index }}][date_emission]" value="{{ old('documents.'.$index.'.date_emission', $document->date_emission) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Date d'expiration -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                                                <input type="date" name="documents[{{ $index }}][date_expiration]" value="{{ old('documents.'.$index.'.date_expiration', $document->date_expiration) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Bouton supprimer -->
                                                    <div class="ml-4">
                                                        <button type="button" class="text-red-600 hover:text-red-800" onclick="removeDocument(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <input type="hidden" name="documents[{{ $index }}][supprimer]" value="0" class="document-delete-flag">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Template pour nouveau document -->
                                <template id="document-template">
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4 new-document">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <!-- Nom du document -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                                        <input type="text" name="new_documents[__INDEX__][nom]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Type de document -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                        <input type="text" name="new_documents[__INDEX__][type]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Numéro du document -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro</label>
                                                        <input type="text" name="new_documents[__INDEX__][numero]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Fichier -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fichier</label>
                                                        <input type="file" name="new_documents[__INDEX__][fichier]" 
                                                            class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                    </div>
                                                    
                                                    <!-- Date d'émission -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'émission</label>
                                                        <input type="date" name="new_documents[__INDEX__][date_emission]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Date d'expiration -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                                        <input type="date" name="new_documents[__INDEX__][date_expiration]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Bouton supprimer -->
                                            <div class="ml-4">
                                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeNewDocument(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Conteneur pour nouveaux documents -->
                                <div id="new-documents-container"></div>
                                
                                <!-- Bouton pour ajouter un document -->
                                <button type="button" id="add-document" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter un document
                                </button>
                            </div>

                            <!-- Matériel attribué -->
                            <div id="materiel" class="mb-8">
                                <h4 class="text-md font-medium text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-laptop mr-2 text-blue-500"></i>
                                    Matériel attribué
                                </h4>

                                <!-- Liste du matériel existant -->
                                @if($employe->materiels && $employe->materiels->count() > 0)
                                    <div class="mb-6 space-y-4">
                                        @foreach($employe->materiels as $index => $materiel)
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <input type="hidden" name="materiels[{{ $index }}][id]" value="{{ $materiel->id }}">
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <!-- Type de matériel -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                                <input type="text" name="materiels[{{ $index }}][type]" value="{{ old('materiels.'.$index.'.type', $materiel->type) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Marque -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                                                                <input type="text" name="materiels[{{ $index }}][marque]" value="{{ old('materiels.'.$index.'.marque', $materiel->marque) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Modèle -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Modèle</label>
                                                                <input type="text" name="materiels[{{ $index }}][modele]" value="{{ old('materiels.'.$index.'.modele', $materiel->modele) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Numéro de série -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de série</label>
                                                                <input type="text" name="materiels[{{ $index }}][numero_serie]" value="{{ old('materiels.'.$index.'.numero_serie', $materiel->numero_serie) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Identifiant -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Identifiant</label>
                                                                <input type="text" name="materiels[{{ $index }}][identifiant]" value="{{ old('materiels.'.$index.'.identifiant', $materiel->identifiant) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- État -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
                                                                <select name="materiels[{{ $index }}][etat]" 
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                                    <option value="Neuf" {{ old('materiels.'.$index.'.etat', $materiel->etat) == 'Neuf' ? 'selected' : '' }}>Neuf</option>
                                                                    <option value="Très bon" {{ old('materiels.'.$index.'.etat', $materiel->etat) == 'Très bon' ? 'selected' : '' }}>Très bon</option>
                                                                    <option value="Bon" {{ old('materiels.'.$index.'.etat', $materiel->etat) == 'Bon' ? 'selected' : '' }}>Bon</option>
                                                                    <option value="Moyen" {{ old('materiels.'.$index.'.etat', $materiel->etat) == 'Moyen' ? 'selected' : '' }}>Moyen</option>
                                                                    <option value="Mauvais" {{ old('materiels.'.$index.'.etat', $materiel->etat) == 'Mauvais' ? 'selected' : '' }}>Mauvais</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <!-- Date d'attribution -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'attribution</label>
                                                                <input type="date" name="materiels[{{ $index }}][date_attribution]" value="{{ old('materiels.'.$index.'.date_attribution', $materiel->date_attribution) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Date de retour -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de retour prévue</label>
                                                                <input type="date" name="materiels[{{ $index }}][date_retour]" value="{{ old('materiels.'.$index.'.date_retour', $materiel->date_retour) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Bouton supprimer -->
                                                    <div class="ml-4">
                                                        <button type="button" class="text-red-600 hover:text-red-800" onclick="removeMateriel(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <input type="hidden" name="materiels[{{ $index }}][supprimer]" value="0" class="materiel-delete-flag">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Template pour nouveau matériel -->
                                <template id="materiel-template">
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4 new-materiel">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <!-- Type de matériel -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                        <input type="text" name="new_materiels[__INDEX__][type]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Marque -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                                                        <input type="text" name="new_materiels[__INDEX__][marque]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Modèle -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Modèle</label>
                                                        <input type="text" name="new_materiels[__INDEX__][modele]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Numéro de série -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de série</label>
                                                        <input type="text" name="new_materiels[__INDEX__][numero_serie]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Identifiant -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Identifiant</label>
                                                        <input type="text" name="new_materiels[__INDEX__][identifiant]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- État -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">État</label>
                                                        <select name="new_materiels[__INDEX__][etat]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            <option value="Neuf">Neuf</option>
                                                            <option value="Très bon">Très bon</option>
                                                            <option value="Bon">Bon</option>
                                                            <option value="Moyen">Moyen</option>
                                                            <option value="Mauvais">Mauvais</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- Date d'attribution -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'attribution</label>
                                                        <input type="date" name="new_materiels[__INDEX__][date_attribution]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Date de retour -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de retour prévue</label>
                                                        <input type="date" name="new_materiels[__INDEX__][date_retour]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Bouton supprimer -->
                                            <div class="ml-4">
                                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeNewMateriel(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Conteneur pour nouveau matériel -->
                                <div id="new-materiels-container"></div>
                                
                                <!-- Bouton pour ajouter du matériel -->
                                <button type="button" id="add-materiel" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter du matériel
                                </button>
                            </div>

                            <!-- Badges d'accès -->
                            <div id="badges" class="mb-8">
                                <h4 class="text-md font-medium text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-id-card mr-2 text-blue-500"></i>
                                    Badges d'accès
                                </h4>

                                <!-- Liste des badges existants -->
                                @if($employe->badgesAcces && $employe->badgesAcces->count() > 0)
                                    <div class="mb-6 space-y-4">
                                        @foreach($employe->badgesAcces as $index => $badge)
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <input type="hidden" name="badges[{{ $index }}][id]" value="{{ $badge->id }}">
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <!-- Type de badge -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                                <input type="text" name="badges[{{ $index }}][type]" value="{{ old('badges.'.$index.'.type', $badge->type) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Numéro -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro</label>
                                                                <input type="text" name="badges[{{ $index }}][numero]" value="{{ old('badges.'.$index.'.numero', $badge->numero) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Zone d'accès -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone d'accès</label>
                                                                <input type="text" name="badges[{{ $index }}][zone_acces]" value="{{ old('badges.'.$index.'.zone_acces', $badge->zone_acces) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Date d'activation -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'activation</label>
                                                                <input type="date" name="badges[{{ $index }}][date_activation]" value="{{ old('badges.'.$index.'.date_activation', $badge->date_activation) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Date d'expiration -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                                                <input type="date" name="badges[{{ $index }}][date_expiration]" value="{{ old('badges.'.$index.'.date_expiration', $badge->date_expiration) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Statut -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                                                <select name="badges[{{ $index }}][statut]" 
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                                    <option value="Actif" {{ old('badges.'.$index.'.statut', $badge->statut) == 'Actif' ? 'selected' : '' }}>Actif</option>
                                                                    <option value="Inactif" {{ old('badges.'.$index.'.statut', $badge->statut) == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                                                                    <option value="Perdu" {{ old('badges.'.$index.'.statut', $badge->statut) == 'Perdu' ? 'selected' : '' }}>Perdu</option>
                                                                    <option value="En attente" {{ old('badges.'.$index.'.statut', $badge->statut) == 'En attente' ? 'selected' : '' }}>En attente</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Bouton supprimer -->
                                                    <div class="ml-4">
                                                        <button type="button" class="text-red-600 hover:text-red-800" onclick="removeBadge(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <input type="hidden" name="badges[{{ $index }}][supprimer]" value="0" class="badge-delete-flag">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Template pour nouveau badge -->
                                <template id="badge-template">
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4 new-badge">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <!-- Type de badge -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                        <input type="text" name="new_badges[__INDEX__][type]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Numéro -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Numéro</label>
                                                        <input type="text" name="new_badges[__INDEX__][numero]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Zone d'accès -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Zone d'accès</label>
                                                        <input type="text" name="new_badges[__INDEX__][zone_acces]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Date d'activation -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'activation</label>
                                                        <input type="date" name="new_badges[__INDEX__][date_activation]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Date d'expiration -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                                        <input type="date" name="new_badges[__INDEX__][date_expiration]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Statut -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                                        <select name="new_badges[__INDEX__][statut]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            <option value="Actif">Actif</option>
                                                            <option value="Inactif">Inactif</option>
                                                            <option value="Perdu">Perdu</option>
                                                            <option value="En attente">En attente</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Bouton supprimer -->
                                            <div class="ml-4">
                                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeNewBadge(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Conteneur pour nouveaux badges -->
                                <div id="new-badges-container"></div>
                                
                                <!-- Bouton pour ajouter un badge -->
                                <button type="button" id="add-badge" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter un badge
                                </button>
                            </div>

                            <!-- Accès informatiques -->
                            <div id="acces-informatiques" class="mb-8">
                                <h4 class="text-md font-medium text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-desktop mr-2 text-blue-500"></i>
                                    Accès informatiques
                                </h4>

                                <!-- Liste des accès existants -->
                                @if($employe->accesInformatiques && $employe->accesInformatiques->count() > 0)
                                    <div class="mb-6 space-y-4">
                                        @foreach($employe->accesInformatiques as $index => $acces)
                                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <input type="hidden" name="acces[{{ $index }}][id]" value="{{ $acces->id }}">
                                                        
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <!-- Type d'accès -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                                <input type="text" name="acces[{{ $index }}][type]" value="{{ old('acces.'.$index.'.type', $acces->type) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Nom d'utilisateur -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                                                                <input type="text" name="acces[{{ $index }}][nom_utilisateur]" value="{{ old('acces.'.$index.'.nom_utilisateur', $acces->nom_utilisateur) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Email -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                                                <input type="email" name="acces[{{ $index }}][email]" value="{{ old('acces.'.$index.'.email', $acces->email) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Plateforme / Logiciel -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Plateforme / Logiciel</label>
                                                                <input type="text" name="acces[{{ $index }}][plateforme]" value="{{ old('acces.'.$index.'.plateforme', $acces->plateforme) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Niveau d'accès -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Niveau d'accès</label>
                                                                <select name="acces[{{ $index }}][niveau_acces]" 
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                                    <option value="Administrateur" {{ old('acces.'.$index.'.niveau_acces', $acces->niveau_acces) == 'Administrateur' ? 'selected' : '' }}>Administrateur</option>
                                                                    <option value="Utilisateur" {{ old('acces.'.$index.'.niveau_acces', $acces->niveau_acces) == 'Utilisateur' ? 'selected' : '' }}>Utilisateur</option>
                                                                    <option value="Invité" {{ old('acces.'.$index.'.niveau_acces', $acces->niveau_acces) == 'Invité' ? 'selected' : '' }}>Invité</option>
                                                                    <option value="Lecture seule" {{ old('acces.'.$index.'.niveau_acces', $acces->niveau_acces) == 'Lecture seule' ? 'selected' : '' }}>Lecture seule</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <!-- Date de création -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date de création</label>
                                                                <input type="date" name="acces[{{ $index }}][date_creation]" value="{{ old('acces.'.$index.'.date_creation', $acces->date_creation) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Date d'expiration -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                                                <input type="date" name="acces[{{ $index }}][date_expiration]" value="{{ old('acces.'.$index.'.date_expiration', $acces->date_expiration) }}"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            </div>
                                                            
                                                            <!-- Notes -->
                                                            <div class="md:col-span-2">
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                                <textarea name="acces[{{ $index }}][notes]" rows="2"
                                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('acces.'.$index.'.notes', $acces->notes) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Bouton supprimer -->
                                                    <div class="ml-4">
                                                        <button type="button" class="text-red-600 hover:text-red-800" onclick="removeAcces(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <input type="hidden" name="acces[{{ $index }}][supprimer]" value="0" class="acces-delete-flag">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                <!-- Template pour nouvel accès -->
                                <template id="acces-template">
                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4 new-acces">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <!-- Type d'accès -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                        <input type="text" name="new_acces[__INDEX__][type]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Nom d'utilisateur -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur</label>
                                                        <input type="text" name="new_acces[__INDEX__][nom_utilisateur]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Email -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                                        <input type="email" name="new_acces[__INDEX__][email]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Plateforme / Logiciel -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Plateforme / Logiciel</label>
                                                        <input type="text" name="new_acces[__INDEX__][plateforme]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Niveau d'accès -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Niveau d'accès</label>
                                                        <select name="new_acces[__INDEX__][niveau_acces]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                            <option value="Administrateur">Administrateur</option>
                                                            <option value="Utilisateur">Utilisateur</option>
                                                            <option value="Invité">Invité</option>
                                                            <option value="Lecture seule">Lecture seule</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- Date de création -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de création</label>
                                                        <input type="date" name="new_acces[__INDEX__][date_creation]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Date d'expiration -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                                        <input type="date" name="new_acces[__INDEX__][date_expiration]" 
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                    </div>
                                                    
                                                    <!-- Notes -->
                                                    <div class="md:col-span-2">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                        <textarea name="new_acces[__INDEX__][notes]" rows="2"
                                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Bouton supprimer -->
                                            <div class="ml-4">
                                                <button type="button" class="text-red-600 hover:text-red-800" onclick="removeNewAcces(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                
                                <!-- Conteneur pour nouveaux accès -->
                                <div id="new-acces-container"></div>
                                
                                <!-- Bouton pour ajouter un accès -->
                                <button type="button" id="add-acces" class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter un accès informatique
                                </button>
                            </div>

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
        // Gestion du pourcentage de temps de travail
        function togglePourcentageTravail() {
            const tempsSelect = document.getElementById('temps_travail');
            const pourcentageContainer = document.getElementById('pourcentage_travail_container');
            
            if (tempsSelect.value === 'Temps partiel') {
                pourcentageContainer.style.display = '';
            } else {
                pourcentageContainer.style.display = 'none';
            }
        }
        
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
    </script>
    @endpush
    </form>
</div>

<script>
    // Variables pour suivre les index des nouveaux éléments
    let newDocumentIndex = 0;
    let newMaterielIndex = 0;
    let newBadgeIndex = 0;
    let newAccesIndex = 0;
    let newFormationIndex = 0;
    
    // Documents administratifs
    document.getElementById('add-document').addEventListener('click', function() {
        const template = document.getElementById('document-template');
        const container = document.getElementById('new-documents-container');
        
        // Cloner le template
        const clone = document.importNode(template.content, true);
        
        // Remplacer l'index dans tous les attributs name
        const inputs = clone.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace('__INDEX__', newDocumentIndex);
            }
        });
        
        // Ajouter le clone au conteneur
        container.appendChild(clone);
        newDocumentIndex++;
    });
    
    // Matériel attribué
    document.getElementById('add-materiel').addEventListener('click', function() {
        const template = document.getElementById('materiel-template');
        const container = document.getElementById('new-materiels-container');
        
        // Cloner le template
        const clone = document.importNode(template.content, true);
        
        // Remplacer l'index dans tous les attributs name
        const inputs = clone.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace('__INDEX__', newMaterielIndex);
            }
        });
        
        // Ajouter le clone au conteneur
        container.appendChild(clone);
        newMaterielIndex++;
    });
    
    // Badges d'accès
    document.getElementById('add-badge').addEventListener('click', function() {
        const template = document.getElementById('badge-template');
        const container = document.getElementById('new-badges-container');
        
        // Cloner le template
        const clone = document.importNode(template.content, true);
        
        // Remplacer l'index dans tous les attributs name
        const inputs = clone.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace('__INDEX__', newBadgeIndex);
            }
        });
        
        // Ajouter le clone au conteneur
        container.appendChild(clone);
        newBadgeIndex++;
    });
    
    // Accès informatiques
    document.getElementById('add-acces').addEventListener('click', function() {
        const template = document.getElementById('acces-template');
        const container = document.getElementById('new-acces-container');
        
        // Cloner le template
        const clone = document.importNode(template.content, true);
        
        // Remplacer l'index dans tous les attributs name
        const inputs = clone.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace('__INDEX__', newAccesIndex);
            }
        });
        
        // Ajouter le clone au conteneur
        container.appendChild(clone);
        newAccesIndex++;
    });
    
    // Formations
    document.getElementById('add-formation').addEventListener('click', function() {
        const template = document.getElementById('formation-template');
        const container = document.getElementById('new-formations-container');
        
        // Cloner le template
        const clone = document.importNode(template.content, true);
        
        // Remplacer l'index dans tous les attributs name
        const inputs = clone.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace('__INDEX__', newFormationIndex);
            }
        });
        
        // Ajouter le clone au conteneur
        container.appendChild(clone);
        newFormationIndex++;
    });
    
    // Fonctions de suppression pour les éléments existants
    function removeDocument(button) {
        const documentDiv = button.closest('.bg-gray-50');
        const deleteFlag = documentDiv.querySelector('.document-delete-flag');
        
        // Marquer comme supprimé
        deleteFlag.value = '1';
        
        // Griser visuellement
        documentDiv.classList.add('opacity-50');
        documentDiv.classList.add('pointer-events-none');
        
        // Changer le bouton
        button.innerHTML = '<i class="fas fa-undo"></i>';
        button.classList.remove('text-red-600', 'hover:text-red-800');
        button.classList.add('text-green-600', 'hover:text-green-800');
        button.onclick = function() { restoreDocument(this); };
    }
    
    function restoreDocument(button) {
        const documentDiv = button.closest('.bg-gray-50');
        const deleteFlag = documentDiv.querySelector('.document-delete-flag');
        
        // Marquer comme non supprimé
        deleteFlag.value = '0';
        
        // Restaurer l'apparence
        documentDiv.classList.remove('opacity-50');
        documentDiv.classList.remove('pointer-events-none');
        
        // Restaurer le bouton
        button.innerHTML = '<i class="fas fa-trash"></i>';
        button.classList.remove('text-green-600', 'hover:text-green-800');
        button.classList.add('text-red-600', 'hover:text-red-800');
        button.onclick = function() { removeDocument(this); };
    }
    
    function removeMateriel(button) {
        const materielDiv = button.closest('.bg-gray-50');
        const deleteFlag = materielDiv.querySelector('.materiel-delete-flag');
        
        // Marquer comme supprimé
        deleteFlag.value = '1';
        
        // Griser visuellement
        materielDiv.classList.add('opacity-50');
        materielDiv.classList.add('pointer-events-none');
        
        // Changer le bouton
        button.innerHTML = '<i class="fas fa-undo"></i>';
        button.classList.remove('text-red-600', 'hover:text-red-800');
        button.classList.add('text-green-600', 'hover:text-green-800');
        button.onclick = function() { restoreMateriel(this); };
    }
    
    function restoreMateriel(button) {
        const materielDiv = button.closest('.bg-gray-50');
        const deleteFlag = materielDiv.querySelector('.materiel-delete-flag');
        
        // Marquer comme non supprimé
        deleteFlag.value = '0';
        
        // Restaurer l'apparence
        materielDiv.classList.remove('opacity-50');
        materielDiv.classList.remove('pointer-events-none');
        
        // Restaurer le bouton
        button.innerHTML = '<i class="fas fa-trash"></i>';
        button.classList.remove('text-green-600', 'hover:text-green-800');
        button.classList.add('text-red-600', 'hover:text-red-800');
        button.onclick = function() { removeMateriel(this); };
    }
    
    function removeBadge(button) {
        const badgeDiv = button.closest('.bg-gray-50');
        const deleteFlag = badgeDiv.querySelector('.badge-delete-flag');
        
        // Marquer comme supprimé
        deleteFlag.value = '1';
        
        // Griser visuellement
        badgeDiv.classList.add('opacity-50');
        badgeDiv.classList.add('pointer-events-none');
        
        // Changer le bouton
        button.innerHTML = '<i class="fas fa-undo"></i>';
        button.classList.remove('text-red-600', 'hover:text-red-800');
        button.classList.add('text-green-600', 'hover:text-green-800');
        button.onclick = function() { restoreBadge(this); };
    }
    
    function restoreBadge(button) {
        const badgeDiv = button.closest('.bg-gray-50');
        const deleteFlag = badgeDiv.querySelector('.badge-delete-flag');
        
        // Marquer comme non supprimé
        deleteFlag.value = '0';
        
        // Restaurer l'apparence
        badgeDiv.classList.remove('opacity-50');
        badgeDiv.classList.remove('pointer-events-none');
        
        // Restaurer le bouton
        button.innerHTML = '<i class="fas fa-trash"></i>';
        button.classList.remove('text-green-600', 'hover:text-green-800');
        button.classList.add('text-red-600', 'hover:text-red-800');
        button.onclick = function() { removeBadge(this); };
    }
    
    function removeAcces(button) {
        const accesDiv = button.closest('.bg-gray-50');
        const deleteFlag = accesDiv.querySelector('.acces-delete-flag');
        
        // Marquer comme supprimé
        deleteFlag.value = '1';
        
        // Griser visuellement
        accesDiv.classList.add('opacity-50');
        accesDiv.classList.add('pointer-events-none');
        
        // Changer le bouton
        button.innerHTML = '<i class="fas fa-undo"></i>';
        button.classList.remove('text-red-600', 'hover:text-red-800');
        button.classList.add('text-green-600', 'hover:text-green-800');
        button.onclick = function() { restoreAcces(this); };
    }
    
    function restoreAcces(button) {
        const accesDiv = button.closest('.bg-gray-50');
        const deleteFlag = accesDiv.querySelector('.acces-delete-flag');
        
        // Marquer comme non supprimé
        deleteFlag.value = '0';
        
        // Restaurer l'apparence
        accesDiv.classList.remove('opacity-50');
        accesDiv.classList.remove('pointer-events-none');
        
        // Restaurer le bouton
        button.innerHTML = '<i class="fas fa-trash"></i>';
        button.classList.remove('text-green-600', 'hover:text-green-800');
        button.classList.add('text-red-600', 'hover:text-red-800');
        button.onclick = function() { removeAcces(this); };
    }
    
    // Fonctions de suppression pour les nouveaux éléments
    function removeNewDocument(button) {
        const documentDiv = button.closest('.new-document');
        documentDiv.remove();
    }
    
    function removeNewMateriel(button) {
        const materielDiv = button.closest('.new-materiel');
        materielDiv.remove();
    }
    
    function removeNewBadge(button) {
        const badgeDiv = button.closest('.new-badge');
        badgeDiv.remove();
    }
    
    function removeNewAcces(button) {
        const accesDiv = button.closest('.new-acces');
        accesDiv.remove();
    }
    
    function removeNewFormation(button) {
        const formationDiv = button.closest('.new-formation');
        formationDiv.remove();
    }
</script>

</x-app-layout>
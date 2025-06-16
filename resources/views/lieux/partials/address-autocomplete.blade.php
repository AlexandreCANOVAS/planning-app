@push('styles')
<style>
    .address-suggestions {
        position: absolute;
        z-index: 1000;
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
        background-color: rgba(30, 30, 30, 0.95);
        border: 1px solid rgba(75, 85, 99, 0.5);
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-top: 2px;
        display: none;
    }
    
    .address-suggestion-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid rgba(75, 85, 99, 0.2);
        transition: background-color 0.2s;
        color: rgba(209, 213, 219, 0.9);
    }
    
    .address-suggestion-item:hover, .address-suggestion-item.selected {
        background-color: rgba(55, 65, 81, 0.5);
        color: white;
    }
    
    .address-suggestion-item:last-child {
        border-bottom: none;
    }
    
    .address-suggestion-item .suggestion-main {
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    
    .address-suggestion-item .suggestion-secondary {
        font-size: 0.875rem;
        opacity: 0.8;
    }
    
    .address-loading {
        display: none;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(156, 163, 175, 0.8);
    }
</style>
@endpush

@push('scripts')
<script>
// Définir une variable globale pour stocker la dernière adresse sélectionnée
let lastSelectedAddress = null;

document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const debounceDelay = 300; // Délai avant de lancer la recherche
    const minChars = 3; // Nombre minimum de caractères pour lancer la recherche
    
    // Fonction pour trouver un champ de formulaire avec plusieurs stratégies
    function findFormField(fieldName) {
        // Liste des sélecteurs à essayer, du plus spécifique au plus général
        const selectors = [
            `[name="${fieldName}"]`,                      // Par attribut name exact
            `#${fieldName}`,                              // Par ID exact
            `[id*="${fieldName}"]`,                      // Par ID contenant le nom
            `[name*="${fieldName}"]`,                    // Par name contenant le nom
            `textarea[id*="${fieldName}"]`,              // Textarea par ID
            `textarea[name*="${fieldName}"]`             // Textarea par name
        ];
        
        // Essayer chaque sélecteur jusqu'à trouver un élément
        for (const selector of selectors) {
            try {
                const element = document.querySelector(selector);
                if (element) {
                    console.log(`Champ ${fieldName} trouvé avec le sélecteur: ${selector}`);
                    return element;
                }
            } catch (e) {
                console.warn(`Sélecteur invalide: ${selector}`, e);
            }
        }
        
        // Recherche spéciale par label (ne peut pas être fait avec querySelector)
        try {
            const labels = document.getElementsByTagName('label');
            const searchText = fieldName.replace('_', ' ');
            
            for (let i = 0; i < labels.length; i++) {
                if (labels[i].textContent.toLowerCase().includes(searchText.toLowerCase())) {
                    // Trouver l'input associé au label
                    let input = null;
                    const forAttr = labels[i].getAttribute('for');
                    
                    if (forAttr) {
                        // Si le label a un attribut 'for', utiliser cet ID
                        input = document.getElementById(forAttr);
                    } else {
                        // Sinon, chercher l'input dans le label
                        input = labels[i].querySelector('input, textarea, select');
                    }
                    
                    if (input) {
                        console.log(`Champ ${fieldName} trouvé via label contenant "${searchText}"`);
                        return input;
                    }
                }
            }
        } catch (e) {
            console.warn(`Erreur lors de la recherche par label:`, e);
        }
        
        console.warn(`Champ ${fieldName} introuvable dans le formulaire`);
        return null;
    }
    
    // Trouver tous les champs du formulaire
    const adresseInput = findFormField('adresse');
    const villeInput = findFormField('ville');
    const codePostalInput = findFormField('code_postal');
    const latitudeInput = findFormField('latitude');
    const longitudeInput = findFormField('longitude');
    
    // Déboguer les éléments trouvés
    console.log('Champs trouvés:', {
        adresse: adresseInput,
        ville: villeInput,
        codePostal: codePostalInput,
        latitude: latitudeInput,
        longitude: longitudeInput
    });
    
    // Vérifier si nous avons trouvé le champ d'adresse (essentiel pour l'autocomplétion)
    if (!adresseInput) {
        console.error('ERREUR: Champ adresse introuvable. L\'autocomplétion ne fonctionnera pas.');
    }
    
    // Créer les éléments pour l'autocomplétion
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'address-suggestions';
    suggestionsContainer.id = 'address-suggestions';
    
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'address-loading';
    loadingIndicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Ajouter les éléments au DOM
    if (adresseInput) {
        console.log('Ajout du conteneur de suggestions pour le champ adresse');
        
        // S'assurer que le parent a une position relative pour le positionnement absolu des suggestions
        const parentElement = adresseInput.parentNode;
        parentElement.style.position = 'relative';
        
        // Ajouter les éléments après le champ d'adresse
        parentElement.appendChild(suggestionsContainer);
        parentElement.appendChild(loadingIndicator);
        
        // Ajouter un événement focus pour montrer les suggestions si l'utilisateur a déjà saisi quelque chose
        adresseInput.addEventListener('focus', function() {
            if (adresseInput.value.length >= minChars) {
                searchAddress(adresseInput.value);
            }
        });
    } else {
        console.error('Champ adresse non trouvé dans le formulaire');
    }
    
    // Variables pour le debounce
    let debounceTimer;
    let selectedIndex = -1;
    let suggestions = [];
    
    // Fonction pour rechercher des adresses
    function searchAddress(query) {
        if (query.length < minChars) {
            hideSuggestions();
            return;
        }
        
        loadingIndicator.style.display = 'block';
        
        // Ajouter un en-tête User-Agent pour respecter les conditions d'utilisation de Nominatim
        const headers = {
            'User-Agent': 'PlanningApp/1.0 (contact@planningapp.fr)'
        };
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=fr&limit=5`, { headers })
            .then(response => response.json())
            .then(data => {
                loadingIndicator.style.display = 'none';
                suggestions = data;
                displaySuggestions(data);
            })
            .catch(error => {
                console.error('Erreur lors de la recherche d\'adresses:', error);
                loadingIndicator.style.display = 'none';
                hideSuggestions();
            });
    }
    
    // Fonction pour afficher les suggestions
    function displaySuggestions(data) {
        suggestionsContainer.innerHTML = '';
        selectedIndex = -1;
        
        if (data.length === 0) {
            hideSuggestions();
            return;
        }
        
        data.forEach((item, index) => {
            const suggestionItem = document.createElement('div');
            suggestionItem.className = 'address-suggestion-item';
            suggestionItem.dataset.index = index;
            
            // Extraire les informations de l'adresse
            const addressParts = item.display_name.split(', ');
            const mainPart = addressParts.slice(0, 2).join(', ');
            const secondaryPart = addressParts.slice(2).join(', ');
            
            suggestionItem.innerHTML = `
                <div class="suggestion-main">${mainPart}</div>
                <div class="suggestion-secondary">${secondaryPart}</div>
            `;
            
            // Stocker l'item directement dans l'élément DOM pour y accéder lors du clic
            suggestionItem._nominatimItem = item;
            
            // Ajouter l'événement de clic directement sur l'élément
            suggestionItem.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Clic sur suggestion détecté');
                selectAddress(this._nominatimItem);
                return false;
            };
            
            suggestionsContainer.appendChild(suggestionItem);
        });
        
        // S'assurer que le conteneur est visible
        suggestionsContainer.style.display = 'block';
    }
    
    // Fonction pour extraire la ville française à partir des données de Nominatim
    function extractFrenchCity(item) {
        // Si nous avons des données structurées
        if (item.address) {
            // Priorité aux champs spécifiques pour les villes françaises
            return item.address.city || // Grande ville
                   item.address.town || // Ville moyenne
                   item.address.village || // Village
                   item.address.municipality || // Municipalité
                   item.address.hamlet || // Hameau
                   item.address.suburb || // Quartier (si c'est tout ce qu'on a)
                   '';
        }
        
        // Si pas de données structurées, extraction manuelle
        const addressParts = item.display_name.split(', ');
        
        // Extraire le code postal (format français: 5 chiffres)
        const postalCodeMatch = item.display_name.match(/\b\d{5}\b/);
        if (postalCodeMatch) {
            const postalCode = postalCodeMatch[0];
            
            // En France, la ville est souvent juste après le code postal
            for (let i = 0; i < addressParts.length; i++) {
                if (addressParts[i].includes(postalCode) && i + 1 < addressParts.length) {
                    return addressParts[i + 1];
                }
            }
        }
        
        // Si on n'a pas trouvé la ville avec le code postal
        if (addressParts.length > 2) {
            // Éviter de prendre le pays (généralement la dernière partie)
            // En France, la ville est souvent la 2e partie de l'adresse
            return addressParts[1];
        } else if (addressParts.length > 1) {
            return addressParts[0]; // Prendre la première partie si seulement deux parties
        }
        
        return '';
    }
    
    // Fonction pour extraire les données d'adresse d'un item Nominatim
    function extractAddressData(item) {
        const addressParts = item.display_name.split(', ');
        let street = '';
        let postalCode = '';
        let city = extractFrenchCity(item);
        
        // Utiliser les données structurées de l'API si disponibles
        if (item.address) {
            // Format structuré de l'API
            street = [item.address.house_number, item.address.road].filter(Boolean).join(' ');
            postalCode = item.address.postcode || '';
            
            // Si la rue est vide, essayer d'autres champs
            if (!street) {
                street = item.address.pedestrian || item.address.footway || item.address.path || addressParts[0];
            }
            
            console.log('Données d\'adresse structurées:', item.address);
        } else {
            // Format non structuré, extraction manuelle
            street = addressParts[0];
            
            // Extraire le code postal (format français: 5 chiffres)
            const postalCodeMatch = item.display_name.match(/\b\d{5}\b/);
            if (postalCodeMatch) {
                postalCode = postalCodeMatch[0];
            }
            
            console.log('Extraction manuelle de l\'adresse:', { addressParts, postalCode, city });
        }
        
        return {
            street,
            postalCode,
            city,
            latitude: item.lat,
            longitude: item.lon
        };
    }
    
    // Fonction pour remplir un champ de formulaire de manière robuste
    function fillFormField(field, value, fieldName) {
        if (!field) {
            console.error(`Champ ${fieldName} introuvable dans le formulaire`);
            return false;
        }
        
        if (!value) {
            console.warn(`Valeur vide pour le champ ${fieldName}`);
            return false;
        }
        
        console.log(`Remplissage du champ ${fieldName} avec:`, value);
        
        // Essayer plusieurs méthodes pour définir la valeur
        try {
            // Méthode 1: Affectation directe
            field.value = value;
            
            // Méthode 2: Utiliser defaultValue
            field.defaultValue = value;
            
            // Méthode 3: Simuler une saisie utilisateur
            field.focus();
            field.select();
            document.execCommand('insertText', false, value);
            
            // Déclencher plusieurs événements pour s'assurer que tous les frameworks détectent le changement
            const events = ['input', 'change', 'keyup'];
            events.forEach(eventType => {
                field.dispatchEvent(new Event(eventType, { bubbles: true }));
            });
            
            return true;
        } catch (e) {
            console.error(`Erreur lors du remplissage du champ ${fieldName}:`, e);
            return false;
        }
    }
    
    // Fonction pour appliquer l'adresse sélectionnée au formulaire
    function applyAddressToForm(item) {
        if (!item) {
            console.error('Aucune adresse à appliquer');
            return;
        }
        
        console.log('Application de l\'adresse au formulaire:', item);
        
        // Extraire les données de l'adresse
        const addressData = extractAddressData(item);
        
        // Remplir les champs du formulaire avec les valeurs extraites
        const fieldsToFill = [
            { field: adresseInput, value: addressData.street, name: 'adresse' },
            { field: codePostalInput, value: addressData.postalCode, name: 'code_postal' },
            { field: villeInput, value: addressData.city, name: 'ville' },
            { field: latitudeInput, value: addressData.latitude, name: 'latitude' },
            { field: longitudeInput, value: addressData.longitude, name: 'longitude' }
        ];
        
        // Remplir chaque champ et compter les succès
        const successCount = fieldsToFill.reduce((count, fieldInfo) => {
            return count + (fillFormField(fieldInfo.field, fieldInfo.value, fieldInfo.name) ? 1 : 0);
        }, 0);
        
        console.log(`Champs remplis avec succès: ${successCount}/${fieldsToFill.length}`);
        
        // Simuler un clic en dehors pour déclencher les validations de formulaire
        document.body.click();
        
        return successCount;
    }
    
    // Fonction pour sélectionner une adresse
    function selectAddress(item) {
        console.log('Sélection de l\'adresse:', item);
        
        // Stocker l'adresse sélectionnée dans la variable globale
        lastSelectedAddress = item;
        
        // Ajouter un bouton de secours après le champ d'adresse si ce n'est pas déjà fait
        if (!document.getElementById('apply-address-button') && adresseInput) {
            const helpButton = document.createElement('button');
            helpButton.id = 'apply-address-button';
            helpButton.type = 'button';
            helpButton.className = 'mt-2 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition ease-in-out duration-150';
            helpButton.innerHTML = '<i class="fas fa-map-marker-alt mr-2"></i> Appliquer l\'adresse sélectionnée';
            helpButton.onclick = function() {
                if (lastSelectedAddress) {
                    console.log('Application manuelle de l\'adresse sélectionnée');
                    applyAddressToForm(lastSelectedAddress);
                }
            };
            
            // Insérer le bouton après le champ d'adresse
            adresseInput.parentNode.appendChild(helpButton);
        }
        
        // Appliquer l'adresse au formulaire
        const successCount = applyAddressToForm(item);
        
        // Masquer les suggestions
        hideSuggestions();
        
        // Retourner le nombre de champs remplis avec succès
        return successCount;
    }
    
    // Fonction pour masquer les suggestions
    function hideSuggestions() {
        suggestionsContainer.style.display = 'none';
        suggestionsContainer.innerHTML = '';
    }
    
    // Événement de saisie dans le champ d'adresse
    if (adresseInput) {
        adresseInput.addEventListener('input', function(e) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                searchAddress(e.target.value);
            }, debounceDelay);
        });
        
        // Navigation au clavier dans les suggestions
        adresseInput.addEventListener('keydown', function(e) {
            const items = suggestionsContainer.querySelectorAll('.address-suggestion-item');
            
            // Flèche bas
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateSelection(items);
            }
            // Flèche haut
            else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelection(items);
            }
            // Entrée
            else if (e.key === 'Enter' && selectedIndex >= 0) {
                e.preventDefault();
                selectAddress(suggestions[selectedIndex]);
            }
            // Échap
            else if (e.key === 'Escape') {
                hideSuggestions();
            }
        });
    }
    
    // Mettre à jour la sélection visuelle
    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('selected');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('selected');
            }
        });
    }
    
    // Masquer les suggestions lors d'un clic en dehors
    document.addEventListener('click', function(e) {
        if (!suggestionsContainer.contains(e.target) && e.target !== adresseInput) {
            hideSuggestions();
        }
    });
});
</script>
@endpush

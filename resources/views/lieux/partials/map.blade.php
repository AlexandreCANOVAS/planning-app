<div id="map-container" class="w-full h-96 rounded-lg shadow-md overflow-hidden mb-6">
    <div id="interactive-map" class="w-full h-full"></div>
</div>

<div id="map-details" class="hidden bg-white rounded-lg shadow-md p-4 mb-6 border-l-4 border-blue-500">
    <div class="flex justify-between items-start">
        <h3 id="map-lieu-nom" class="text-lg font-medium text-gray-900"></h3>
        <button id="close-map-details" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <p id="map-lieu-adresse" class="text-sm text-gray-600 mt-1"></p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        <div class="flex items-center">
            <div class="text-blue-500 mr-2"><i class="fas fa-phone"></i></div>
            <div>
                <div class="text-xs text-gray-500">Téléphone</div>
                <div id="map-lieu-telephone" class="text-sm"></div>
            </div>
        </div>
        <div class="flex items-center">
            <div class="text-blue-500 mr-2"><i class="fas fa-clock"></i></div>
            <div>
                <div class="text-xs text-gray-500">Horaires</div>
                <div id="map-lieu-horaires" class="text-sm"></div>
            </div>
        </div>
        <div class="flex items-center">
            <div class="text-blue-500 mr-2"><i class="fas fa-user-tie"></i></div>
            <div>
                <div class="text-xs text-gray-500">Contact principal</div>
                <div id="map-lieu-contact" class="text-sm"></div>
            </div>
        </div>
    </div>
    
    <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-100">
        <div class="text-center">
            <div class="text-xs text-gray-500 uppercase mb-1">
                <i class="fas fa-users mr-1"></i> Employés aujourd'hui
            </div>
            <div id="map-lieu-employes" class="text-xl font-bold text-blue-600">0</div>
        </div>
        <div class="text-center">
            <div class="text-xs text-gray-500 uppercase mb-1">
                <i class="fas fa-chart-line mr-1"></i> Heures ce mois
            </div>
            <div id="map-lieu-heures" class="text-xl font-bold text-green-600">0</div>
        </div>
        <a id="map-lieu-edit" href="#" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-edit mr-1"></i> Modifier
        </a>
    </div>
</div>

@push('styles')
<style>
    .map-marker-icon {
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 3px 8px rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        width: 36px !important;
        height: 36px !important;
        font-size: 20px;
        transform: translateY(-18px);
    }
    
    .map-marker-icon i {
        filter: drop-shadow(0 1px 2px rgba(0,0,0,0.5));
    }
    
    /* Effet de pulsation pour les marqueurs */
    .map-marker-pulse {
        position: absolute;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        transform: translateY(-18px);
        animation: pulse 2s infinite;
        opacity: 0.6;
        z-index: -1;
    }
    
    @keyframes pulse {
        0% {
            transform: translateY(-18px) scale(1);
            opacity: 0.6;
        }
        70% {
            transform: translateY(-18px) scale(1.5);
            opacity: 0;
        }
        100% {
            transform: translateY(-18px) scale(1.5);
            opacity: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la carte
    const map = L.map('interactive-map').setView([46.603354, 1.888334], 5); // Centre sur la France
    
    // Ajouter la couche de tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Utiliser les données passées directement depuis le contrôleur
    const lieux = @json($lieuxAvecCoordonnees);
    
    if (lieux.length === 0) {
        // Aucun lieu avec adresse
        const noDataDiv = document.createElement('div');
        noDataDiv.className = 'absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10';
        noDataDiv.innerHTML = `
            <div class="text-center p-6">
                <i class="fas fa-map-marker-alt text-gray-400 text-4xl mb-2"></i>
                <p class="text-gray-600">Aucun lieu avec adresse complète.</p>
                <p class="text-sm text-gray-500 mt-2">Assurez-vous que vos lieux ont une adresse complète pour les voir sur la carte.</p>
            </div>
        `;
        document.getElementById('map-container').appendChild(noDataDiv);
    } else {
        // Créer un compteur pour suivre les géocodages terminés
        let geocodingCounter = 0;
        const markers = [];
        const totalLieux = lieux.length;
        
        // Afficher un indicateur de chargement
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'map-loading';
        loadingDiv.className = 'absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10';
        loadingDiv.innerHTML = `
            <div class="text-center p-6">
                <i class="fas fa-spinner fa-spin text-blue-500 text-4xl mb-2"></i>
                <p class="text-gray-600">Chargement des lieux sur la carte...</p>
            </div>
        `;
        document.getElementById('map-container').appendChild(loadingDiv);
        
        // Fonction pour géocoder une adresse et mettre à jour le cache
        function geocodeAddress(lieu, cacheKey, cacheData) {
            // Construire l'adresse complète
            const adresse = `${lieu.adresse}, ${lieu.code_postal} ${lieu.ville}, France`;
            
            // Ajouter un en-tête User-Agent pour respecter les conditions d'utilisation de Nominatim
            const headers = {
                'User-Agent': 'PlanningApp/1.0 (contact@planningapp.fr)'
            };
            
            // Appel à l'API Nominatim pour géocoder l'adresse
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(adresse)}&limit=1`, { headers })
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const result = data[0];
                        const lat = parseFloat(result.lat);
                        const lon = parseFloat(result.lon);
                        
                        // Mettre à jour le cache
                        cacheData[cacheKey] = {
                            lat: lat,
                            lon: lon,
                            timestamp: Date.now()
                        };
                        
                        // Sauvegarder le cache mis à jour
                        try {
                            localStorage.setItem('geocodeCache', JSON.stringify(cacheData));
                        } catch (e) {
                            console.error('Erreur lors de la sauvegarde du cache:', e);
                            // En cas d'erreur (stockage plein), essayer de nettoyer le cache
                            try {
                                // Supprimer les entrées de plus de 60 jours
                                const oldTimestamp = Date.now() - (60 * 24 * 60 * 60 * 1000);
                                Object.keys(cacheData).forEach(key => {
                                    if (cacheData[key].timestamp < oldTimestamp) {
                                        delete cacheData[key];
                                    }
                                });
                                localStorage.setItem('geocodeCache', JSON.stringify(cacheData));
                            } catch (e2) {
                                console.error('Impossible de nettoyer le cache:', e2);
                            }
                        }
                        
                        // Créer une icône personnalisée avec la couleur du lieu
                        const icon = L.divIcon({
                            className: 'map-marker-icon',
                            html: `<i class="fas fa-map-marker-alt"></i>`,
                            iconSize: [36, 36],
                            iconAnchor: [18, 36]
                        });
                        
                        // Créer le marqueur
                        const marker = L.marker([lat, lon], {
                            icon: icon,
                            title: lieu.nom,
                            riseOnHover: true // Le marqueur s'élève au survol pour améliorer la visibilité
                        }).addTo(map);
                        
                        // Appliquer la couleur du lieu au marqueur
                        setTimeout(() => {
                            const markerElement = marker.getElement();
                            if (markerElement) {
                                const iconElement = markerElement.querySelector('.map-marker-icon');
                                if (iconElement) {
                                    iconElement.style.backgroundColor = lieu.couleur;
                                    
                                    // Ajouter un effet de pulsation
                                    const pulseElement = document.createElement('div');
                                    pulseElement.className = 'map-marker-pulse';
                                    pulseElement.style.backgroundColor = lieu.couleur;
                                    iconElement.appendChild(pulseElement);
                                }
                            }
                        }, 0);
                        
                        // Ajouter un événement de clic sur le marqueur
                        marker.on('click', function() {
                            showLieuDetails(lieu);
                        });
                        
                        markers.push(marker);
                    }
                    
                    // Incrémenter le compteur et vérifier si tous les lieux ont été géocodés
                    geocodingCounter++;
                    if (geocodingCounter === totalLieux) {
                        finishLoading();
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du géocodage de l\'adresse:', error);
                    geocodingCounter++;
                    if (geocodingCounter === totalLieux) {
                        finishLoading();
                    }
                });
        }
        
        // Vérifier si les données de géocodage sont en cache dans le localStorage
        const geocodeCache = localStorage.getItem('geocodeCache');
        let cacheData = {};
        
        if (geocodeCache) {
            try {
                cacheData = JSON.parse(geocodeCache);
            } catch (e) {
                console.error('Erreur lors de la lecture du cache:', e);
                // Réinitialiser le cache en cas d'erreur
                cacheData = {};
            }
        }
        
        // Géocoder chaque lieu avec un délai réduit et utiliser le cache si disponible
        lieux.forEach((lieu, index) => {
            // Créer une clé unique pour ce lieu basée sur son adresse
            const adresse = `${lieu.adresse}, ${lieu.code_postal} ${lieu.ville}, France`;
            const cacheKey = adresse.toLowerCase().replace(/\s+/g, '');
            
            // Si les coordonnées sont en cache et ont moins de 30 jours
            if (cacheData[cacheKey] && cacheData[cacheKey].timestamp > Date.now() - (30 * 24 * 60 * 60 * 1000)) {
                // Utiliser les coordonnées en cache
                const cachedCoords = cacheData[cacheKey];
                
                // Créer le marqueur avec les coordonnées en cache
                const icon = L.divIcon({
                    className: 'map-marker-icon',
                    html: `<i class="fas fa-map-marker-alt"></i>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 36]
                });
                
                const marker = L.marker([cachedCoords.lat, cachedCoords.lon], {
                    icon: icon,
                    title: lieu.nom,
                    riseOnHover: true
                }).addTo(map);
                
                // Appliquer la couleur et l'effet de pulsation
                setTimeout(() => {
                    const markerElement = marker.getElement();
                    if (markerElement) {
                        const iconElement = markerElement.querySelector('.map-marker-icon');
                        if (iconElement) {
                            iconElement.style.backgroundColor = lieu.couleur;
                            
                            const pulseElement = document.createElement('div');
                            pulseElement.className = 'map-marker-pulse';
                            pulseElement.style.backgroundColor = lieu.couleur;
                            iconElement.appendChild(pulseElement);
                        }
                    }
                }, 0);
                
                // Ajouter l'événement de clic
                marker.on('click', function() {
                    showLieuDetails(lieu);
                });
                
                markers.push(marker);
                
                // Incrémenter le compteur
                geocodingCounter++;
                if (geocodingCounter === totalLieux) {
                    finishLoading();
                }
            } else {
                // Géocoder avec un délai réduit
                setTimeout(() => {
                    geocodeAddress(lieu, cacheKey, cacheData);
                }, index * 250); // 250ms d'intervalle entre chaque requête (4 fois plus rapide)
            }
        });
        
        // Fonction pour terminer le chargement et ajuster la vue
        function finishLoading() {
            // Supprimer l'indicateur de chargement
            const loadingElement = document.getElementById('map-loading');
            if (loadingElement) {
                loadingElement.remove();
            }
            
            // Ajuster la vue de la carte pour montrer tous les marqueurs
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            } else {
                // Aucun marqueur n'a pu être placé
                const noDataDiv = document.createElement('div');
                noDataDiv.className = 'absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10';
                noDataDiv.innerHTML = `
                    <div class="text-center p-6">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-2"></i>
                        <p class="text-gray-600">Impossible de localiser les adresses sur la carte.</p>
                        <p class="text-sm text-gray-500 mt-2">Vérifiez que les adresses sont correctes et complètes.</p>
                    </div>
                `;
                document.getElementById('map-container').appendChild(noDataDiv);
            }
        }
    }
    
    // Fonction pour afficher les détails d'un lieu
    function showLieuDetails(lieu) {
        // Remplir les détails du lieu
        document.getElementById('map-lieu-nom').textContent = lieu.nom;
        
        // Construire l'adresse complète
        const adresseComplete = `${lieu.adresse}, ${lieu.code_postal} ${lieu.ville}`;
        document.getElementById('map-lieu-adresse').textContent = adresseComplete;
        
        // Récupérer les détails complémentaires via une requête AJAX pour avoir les statistiques
        fetch(`/lieux/${lieu.id}/edit`)
            .then(response => response.text())
            .then(html => {
                // Créer un parser HTML temporaire
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extraire les statistiques du lieu depuis la page d'édition
                // Comme nous n'avons pas accès direct aux statistiques, nous utilisons des valeurs par défaut
                document.getElementById('map-lieu-telephone').textContent = lieu.telephone || 'Non renseigné';
                document.getElementById('map-lieu-horaires').textContent = lieu.horaires || 'Non renseignés';
                document.getElementById('map-lieu-contact').textContent = lieu.contact_principal || 'Non renseigné';
                document.getElementById('map-lieu-employes').textContent = '0'; // Valeur par défaut
                document.getElementById('map-lieu-heures').textContent = '0.0'; // Valeur par défaut
            })
            .catch(error => {
                console.error('Erreur lors du chargement des détails complémentaires:', error);
                // En cas d'erreur, afficher des valeurs par défaut
                document.getElementById('map-lieu-telephone').textContent = lieu.telephone || 'Non renseigné';
                document.getElementById('map-lieu-horaires').textContent = lieu.horaires || 'Non renseignés';
                document.getElementById('map-lieu-contact').textContent = lieu.contact_principal || 'Non renseigné';
                document.getElementById('map-lieu-employes').textContent = '0';
                document.getElementById('map-lieu-heures').textContent = '0.0';
            });
        
        // Mettre à jour le lien d'édition
        document.getElementById('map-lieu-edit').href = `/lieux/${lieu.id}/edit`;
        
        // Appliquer la couleur du lieu
        document.getElementById('map-details').style.borderColor = lieu.couleur;
        
        // Afficher la section de détails
        document.getElementById('map-details').classList.remove('hidden');
    }
    
    // Fermer les détails du lieu
    document.getElementById('close-map-details').addEventListener('click', function() {
        document.getElementById('map-details').classList.add('hidden');
    });
});
</script>
@endpush

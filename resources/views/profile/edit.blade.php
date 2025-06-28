<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar Menu -->
                <div class="w-full md:w-1/4">
                    <div class="p-4 bg-white rounded-lg shadow-sm">
                        <nav class="space-y-1">
                            <a href="#profile-information" data-section="profile-information" class="profile-nav-link profile-menu-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-900 bg-gray-100">
                                Informations du Profil
                            </a>
                            <a href="#update-password" data-section="update-password" class="profile-nav-link profile-menu-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                Mot de passe
                            </a>
                            <a href="#two-factor-auth" data-section="two-factor-auth" class="profile-nav-link profile-menu-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                Authentification à 2 facteurs
                            </a>
                            <a href="#export-data" data-section="export-data" class="profile-nav-link profile-menu-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                Exporter les données
                            </a>
                            <a href="#delete-account" data-section="delete-account" class="profile-nav-link profile-menu-item group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50">
                                Supprimer le compte
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="w-full md:w-3/4">
                    <div id="profile-information" class="profile-content-section space-y-6">
                        <div class="p-4 sm:p-8 bg-white rounded-lg shadow-sm">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div id="update-password" class="profile-content-section space-y-6 hidden">
                        <div class="p-4 sm:p-8 bg-white rounded-lg shadow-sm">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div id="two-factor-auth" class="profile-content-section space-y-6 hidden">
                        <div class="p-4 sm:p-8 bg-white rounded-lg shadow-sm">
                            @php
                                // Importer la classe TwoFactorQrCodeGenerator n'est pas nécessaire dans le bloc @php
                                // car nous pouvons l'utiliser avec son namespace complet
                                $user = auth()->user();
                                $enabled = $user->two_factor_secret ? true : false;
                                $showingQrCode = $user->two_factor_secret ? true : false;
                                $showingRecoveryCodes = $user->two_factor_recovery_codes ? true : false;
                                
                                // Générer le QR code si l'authentification à deux facteurs est activée
                                $qrCodeSvg = null;
                                $recoveryCodes = [];
                                $twoFactorSecret = null;
                                
                                if ($user->two_factor_secret) {
                                    // Générer le code QR
                                    $qrCodeSvg = \App\Http\Helpers\TwoFactorQrCodeGenerator::generateQrCode($user);
                                    
                                    // Ces variables seront utilisées dans la vue
                                    if ($user->two_factor_recovery_codes) {
                                        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);
                                    }
                                    if ($user->two_factor_secret) {
                                        $twoFactorSecret = decrypt($user->two_factor_secret);
                                    }
                                }
                            @endphp
                            
                            @include('profile.two-factor-authentication-form-new', [
                                'enabled' => $enabled,
                                'showingQrCode' => $showingQrCode,
                                'showingRecoveryCodes' => $showingRecoveryCodes,
                                'user' => $user,
                                'qrCodeSvg' => $qrCodeSvg,
                                'recoveryCodes' => $recoveryCodes,
                                'twoFactorSecret' => $twoFactorSecret
                            ])
                        </div>
                    </div>

                    <div id="export-data" class="profile-content-section space-y-6 hidden">
                        <div class="p-4 sm:p-8 bg-white rounded-lg shadow-sm">
                            @include('profile.partials.export-data-form')
                        </div>
                    </div>

                    <div id="delete-account" class="profile-content-section space-y-6 hidden">
                        <div class="p-4 sm:p-8 bg-white rounded-lg shadow-sm">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fonction pour afficher une section spécifique
            function showSection(sectionId) {
                // Masquer toutes les sections
                document.querySelectorAll('.profile-content-section').forEach(function(section) {
                    section.classList.add('hidden');
                });
                
                // Afficher la section demandée
                const targetSection = document.getElementById(sectionId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                    
                    // Si c'est la section 2FA, exécuter le script de débogage après un court délai
                    if (sectionId === 'two-factor-auth') {
                        setTimeout(function() {
                            console.log('Vérification de la section 2FA après délai');
                            if (!targetSection.classList.contains('hidden')) {
                                console.log('Section 2FA visible');
                                debugTwoFactorActivation();
                            }
                        }, 500);
                    }
                }
                
                // Mettre à jour les liens actifs
                document.querySelectorAll('.profile-nav-link').forEach(function(link) {
                    link.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-gray-100');
                    link.classList.add('text-gray-600', 'dark:text-gray-400', 'hover:text-gray-900', 'dark:hover:text-gray-100', 'hover:bg-gray-50', 'dark:hover:bg-gray-700');
                });
                
                const activeLink = document.querySelector(`.profile-nav-link[data-section="${sectionId}"]`);
                if (activeLink) {
                    activeLink.classList.remove('text-gray-600', 'dark:text-gray-400', 'hover:text-gray-900', 'dark:hover:text-gray-100', 'hover:bg-gray-50', 'dark:hover:bg-gray-700');
                    activeLink.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-gray-100');
                }
            }
            
            // Fonction de débogage pour l'activation 2FA
            function debugTwoFactorActivation() {
                console.log('Recherche du bouton d\'activation...');
                
                // Recherche par ID
                let activateButton = document.getElementById('two-factor-activate-button');
                if (activateButton) {
                    console.log('Bouton d\'activation trouvé par ID');
                    // Ne pas cliquer automatiquement pour éviter la boucle infinie
                    console.log('Bouton prêt pour activation manuelle');
                    return;
                }
                
                console.log('Bouton non trouvé par ID, essai par texte...');
                
                // Recherche par texte
                const allButtons = document.querySelectorAll('button');
                for (let i = 0; i < allButtons.length; i++) {
                    if (allButtons[i].textContent.trim().toLowerCase().includes('activer')) {
                        console.log('Bouton d\'activation trouvé par texte');
                        // Ne pas cliquer automatiquement
                        console.log('Bouton prêt pour activation manuelle');
                        return;
                    }
                }
                
                console.log('Recherche dans le conteneur d\'activation 2FA...');
                
                // Recherche dans le conteneur spécifique
                const container = document.getElementById('two-factor-activation-container');
                if (container) {
                    const containerButtons = container.querySelectorAll('button');
                    if (containerButtons.length > 0) {
                        console.log('Bouton trouvé dans le conteneur d\'activation');
                        // Ne pas cliquer automatiquement
                        console.log('Bouton prêt pour activation manuelle');
                        return;
                    } else {
                        console.log('Contenu du conteneur:', container.innerHTML);
                    }
                } else {
                    console.log('Conteneur d\'activation non trouvé');
                }
                
                // Dernier recours : afficher tous les boutons de la page
                console.log('Bouton d\'activation introuvable malgré plusieurs tentatives');
                console.log('Liste de tous les boutons sur la page:');
                allButtons.forEach((btn, index) => {
                    console.log(`Bouton ${index}:`, btn.outerHTML);
                });
            }
            
            // Ajouter des écouteurs d'événements aux liens de navigation
            document.querySelectorAll('.profile-nav-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sectionId = this.getAttribute('data-section');
                    showSection(sectionId);
                    
                    // Mettre à jour l'URL avec un paramètre de requête
                    const url = new URL(window.location);
                    url.searchParams.set('section', sectionId);
                    window.history.pushState({}, '', url);
                });
            });
            
            // Vérifier s'il y a un paramètre de section dans l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const sectionParam = urlParams.get('section');
            
            if (sectionParam && document.getElementById(sectionParam)) {
                showSection(sectionParam);
            } else {
                // Afficher la première section par défaut
                const firstSection = document.querySelector('.profile-nav-link');
                if (firstSection) {
                    const defaultSectionId = firstSection.getAttribute('data-section');
                    showSection(defaultSectionId);
                }
            }
            
            // Forcer l'affichage de la section 2FA pour le débogage
            if (urlParams.get('debug2fa') === '1') {
                setTimeout(function() {
                    showSection('two-factor-auth');
                    console.log('Affichage forcé de la section 2FA pour le débogage');
                }, 1000);
            }
            
            // Vérifier si Alpine est chargé correctement
            if (window.Alpine) {
                console.log('Alpine.js est chargé correctement');
            } else {
                console.error('Alpine.js n\'est pas chargé');
            }
        });
    </script>
    @endpush
</x-app-layout>

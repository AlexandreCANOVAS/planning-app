/**
 * Session Timeout Handler
 * Détecte l'inactivité de l'utilisateur et affiche un avertissement avant déconnexion
 */
class SessionTimeoutHandler {
    constructor(options = {}) {
        // Configuration par défaut
        this.options = {
            // Temps d'inactivité avant avertissement (en millisecondes)
            warningTime: options.warningTime || 5 * 60 * 1000, // 5 minutes par défaut
            
            // Temps d'attente après avertissement avant déconnexion (en millisecondes)
            redirectTime: options.redirectTime || 30 * 1000, // 30 secondes par défaut
            
            // Événements à surveiller pour réinitialiser le minuteur
            events: options.events || ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'],
            
            // Callbacks
            onWarning: options.onWarning || null,
            onTimeout: options.onTimeout || null
        };

        // Minuteurs
        this.warningTimer = null;
        this.countdownInterval = null;
        this.remainingSeconds = Math.floor(this.options.redirectTime / 1000);

        // Éléments DOM
        this.modal = document.getElementById('session-timeout-modal');
        this.continueButton = document.getElementById('session-continue');
        this.logoutButton = document.getElementById('session-logout');
        this.countdownElement = document.getElementById('session-countdown');

        // Initialisation
        this.init();
        
        console.log('SessionTimeoutHandler initialisé');
        console.log('Temps avant avertissement:', this.options.warningTime / 1000, 'secondes');
        console.log('Temps avant déconnexion:', this.options.redirectTime / 1000, 'secondes');
    }

    /**
     * Initialise les écouteurs d'événements et les minuteurs
     */
    init() {
        // Vérifier que la modal existe
        if (!this.modal) {
            console.error('Modal session timeout non trouvée dans le DOM');
            return;
        }

        console.log('Modal trouvée:', this.modal);

        // Ajouter les écouteurs d'événements pour réinitialiser le minuteur
        this.options.events.forEach(event => {
            document.addEventListener(event, () => this.resetTimers());
        });

        // Configurer les boutons de la modal
        if (this.continueButton) {
            this.continueButton.addEventListener('click', () => {
                console.log('Bouton continuer cliqué');
                this.continueSession();
            });
        } else {
            console.error('Bouton continuer non trouvé');
        }
        
        if (this.logoutButton) {
            this.logoutButton.addEventListener('click', () => {
                console.log('Bouton déconnexion cliqué');
                this.logout();
            });
        } else {
            console.error('Bouton déconnexion non trouvé');
        }

        // Démarrer le minuteur initial
        this.resetTimers();
    }

    /**
     * Réinitialise les minuteurs d'inactivité
     */
    resetTimers() {
        // Effacer les minuteurs existants
        clearTimeout(this.warningTimer);
        clearInterval(this.countdownInterval);

        // Cacher la modal si elle est visible
        if (this.modal && this.modal.style.display === 'flex') {
            this.hideModal();
        }

        // Démarrer un nouveau minuteur pour l'avertissement
        this.warningTimer = setTimeout(() => {
            console.log('Minuteur d\'inactivité déclenché');
            this.showWarning();
        }, this.options.warningTime);
    }

    /**
     * Affiche l'avertissement de fin de session
     */
    showWarning() {
        // Appeler le callback onWarning si défini
        if (typeof this.options.onWarning === 'function') {
            this.options.onWarning();
        }

        console.log('Affichage de l\'avertissement de session');
        
        // Afficher la modal
        this.showModal();

        // Démarrer le compte à rebours
        this.startCountdown();
    }

    /**
     * Initialise le compte à rebours
     */
    startCountdown() {
        console.log('Démarrage du compte à rebours');
        
        // Réinitialiser le compte à rebours
        this.remainingSeconds = Math.floor(this.options.redirectTime / 1000);
        this.updateCountdown();
        
        // Démarrer le compte à rebours
        this.countdownInterval = setInterval(() => {
            this.remainingSeconds--;
            this.updateCountdown();
            
            if (this.remainingSeconds <= 0) {
                console.log('Compte à rebours terminé');
                clearInterval(this.countdownInterval);
                this.timeout();
            }
        }, 1000);
    }
    
    /**
     * Met à jour l'affichage du compte à rebours
     */
    updateCountdown() {
        if (this.countdownElement) {
            this.countdownElement.textContent = this.remainingSeconds;
            console.log(`Compte à rebours: ${this.remainingSeconds} secondes`);
        } else {
            console.error("L'élément de compte à rebours n'a pas été trouvé dans le DOM");
        }
    }

    /**
     * Affiche la modal d'avertissement
     */
    showModal() {
        if (!this.modal) {
            console.error('Modal non trouvée dans le DOM');
            return;
        }
        
        console.log('Affichage de la modal d\'avertissement');
        
        // Créer un backdrop pour l'assombrissement
        const backdrop = document.createElement('div');
        backdrop.id = 'session-modal-backdrop';
        backdrop.style.position = 'fixed';
        backdrop.style.top = '0';
        backdrop.style.left = '0';
        backdrop.style.width = '100%';
        backdrop.style.height = '100%';
        backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        backdrop.style.zIndex = '40';
        document.body.appendChild(backdrop);
        
        // Afficher la modal
        this.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Animation de la modal
        const modalContent = this.modal.querySelector('div > div');
        if (modalContent) {
            modalContent.style.transform = 'scale(1)';
            modalContent.style.opacity = '1';
        }
    }

    /**
     * Cache la modal d'avertissement
     */
    hideModal() {
        if (!this.modal) return;
        
        console.log('Fermeture de la modal d\'avertissement');
        
        // Cacher la modal
        this.modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Supprimer le backdrop
        const backdrop = document.getElementById('session-modal-backdrop');
        if (backdrop) backdrop.remove();
    }

    /**
     * Poursuit la session utilisateur
     */
    continueSession() {
        console.log('Continuation de la session');
        this.hideModal();
        this.resetTimers();
    }

    /**
     * Gère la fin de session par timeout
     */
    timeout() {
        console.log('Timeout de session atteint');
        
        // Appeler le callback onTimeout si défini
        if (typeof this.options.onTimeout === 'function') {
            this.options.onTimeout();
        }
        
        // Soumettre le formulaire de déconnexion
        const logoutForm = document.getElementById('logout-form');
        if (logoutForm) {
            console.log('Soumission du formulaire de déconnexion');
            logoutForm.submit();
        } else {
            console.error('Formulaire de déconnexion non trouvé');
        }
    }

    /**
     * Déconnecte l'utilisateur immédiatement
     */
    logout() {
        console.log('Déconnexion manuelle');
        const logoutForm = document.getElementById('logout-form');
        if (logoutForm) {
            logoutForm.submit();
        } else {
            console.error('Formulaire de déconnexion non trouvé');
        }
    }
}

// Exporter la classe pour une utilisation avec import/require
if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
    module.exports = SessionTimeoutHandler;
}

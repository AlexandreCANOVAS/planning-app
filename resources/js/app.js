import './bootstrap';
// import './cookie-consent.js'; // Désactivé temporairement pour test d'isolation
import Alpine from 'alpinejs';

// Import des composants personnalisés
import './notification-dropdown';
import './notification-center';
import './toast';

window.Alpine = Alpine;

Alpine.start();

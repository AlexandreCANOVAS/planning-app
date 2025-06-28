import './bootstrap';

// Import des composants personnalisés
import './notification-dropdown';
import './notification-center';
import './toast';

// Vérifier si Alpine.js est déjà chargé par Livewire
if (!window.Alpine) {
    import('alpinejs').then(module => {
        window.Alpine = module.default;
        window.Alpine.start();
    });
} else {
    console.log('Alpine.js est déjà chargé par Livewire, pas besoin de le charger à nouveau');
}

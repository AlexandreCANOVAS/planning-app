/**
 * Système de notifications toast pour l'application
 */

// Fonction pour afficher une notification toast
window.showToast = function(message, type = 'info', duration = 5000) {
    // Types disponibles: 'info', 'success', 'warning', 'error'
    const colors = {
        info: {
            bg: 'bg-blue-50',
            border: 'border-blue-200',
            icon: 'fa-info-circle text-blue-600',
            progress: 'bg-blue-600'
        },
        success: {
            bg: 'bg-green-50',
            border: 'border-green-200',
            icon: 'fa-check-circle text-green-600',
            progress: 'bg-green-600'
        },
        warning: {
            bg: 'bg-amber-50',
            border: 'border-amber-200',
            icon: 'fa-exclamation-triangle text-amber-600',
            progress: 'bg-amber-600'
        },
        error: {
            bg: 'bg-red-50',
            border: 'border-red-200',
            icon: 'fa-exclamation-circle text-red-600',
            progress: 'bg-red-600'
        },
        purple: {
            bg: 'bg-purple-50',
            border: 'border-purple-200',
            icon: 'fa-bell text-purple-600',
            progress: 'bg-purple-600'
        }
    };
    
    // Créer le conteneur de toast s'il n'existe pas
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 flex flex-col gap-2 max-w-xs w-full';
        document.body.appendChild(toastContainer);
    }
    
    // Créer l'élément toast
    const toast = document.createElement('div');
    toast.className = `flex items-start p-3 rounded-lg shadow-md border ${colors[type].bg} ${colors[type].border} transform transition-all duration-300 translate-x-full opacity-0`;
    
    // Contenu du toast
    toast.innerHTML = `
        <div class="flex-shrink-0 mr-3">
            <i class="fas ${colors[type].icon}"></i>
        </div>
        <div class="flex-1 pr-2">
            <p class="text-sm text-gray-800">${message}</p>
        </div>
        <button class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times"></i>
        </button>
        <div class="absolute bottom-0 left-0 h-1 ${colors[type].progress} toast-progress" style="width: 100%; transition: width ${duration}ms linear;"></div>
    `;
    
    // Ajouter le toast au conteneur
    toastContainer.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
    }, 10);
    
    // Configurer la barre de progression
    const progressBar = toast.querySelector('.toast-progress');
    setTimeout(() => {
        progressBar.style.width = '0';
    }, 100);
    
    // Configurer le bouton de fermeture
    const closeButton = toast.querySelector('button');
    closeButton.addEventListener('click', () => {
        closeToast(toast);
    });
    
    // Fermer automatiquement après la durée spécifiée
    const timeout = setTimeout(() => {
        closeToast(toast);
    }, duration);
    
    // Fonction pour fermer le toast
    function closeToast(toastElement) {
        toastElement.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toastElement.remove();
        }, 300);
    }
    
    // Retourner l'élément toast pour permettre des manipulations supplémentaires
    return toast;
};

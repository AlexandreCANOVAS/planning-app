/**
 * Système de notification toast pour l'application
 */
window.showToast = function(message, type = 'info') {
    // Définir les couleurs en fonction du type
    const colors = {
        'success': { bg: 'bg-green-500', text: 'text-white' },
        'error': { bg: 'bg-red-500', text: 'text-white' },
        'warning': { bg: 'bg-yellow-500', text: 'text-white' },
        'info': { bg: 'bg-blue-500', text: 'text-white' },
        'green': { bg: 'bg-green-500', text: 'text-white' },
        'red': { bg: 'bg-red-500', text: 'text-white' },
        'yellow': { bg: 'bg-yellow-500', text: 'text-white' },
        'blue': { bg: 'bg-blue-500', text: 'text-white' },
        'purple': { bg: 'bg-purple-500', text: 'text-white' }
    };
    
    // Utiliser les couleurs par défaut si le type n'est pas reconnu
    const colorClass = colors[type] || colors['info'];
    
    // Créer l'élément toast
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded shadow-lg ${colorClass.bg} ${colorClass.text} transition-opacity duration-500 flex items-center`;
    toast.style.opacity = '0';
    
    // Ajouter l'icône en fonction du type
    let icon = '';
    if (type === 'success' || type === 'green') {
        icon = '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
    } else if (type === 'error' || type === 'red') {
        icon = '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
    } else {
        icon = '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
    }
    
    // Ajouter le contenu
    toast.innerHTML = `
        ${icon}
        <span>${message}</span>
        <button class="ml-auto text-white focus:outline-none" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
    `;
    
    // Ajouter au DOM
    document.body.appendChild(toast);
    
    // Animation d'apparition
    setTimeout(() => {
        toast.style.opacity = '1';
    }, 10);
    
    // Disparition automatique après 5 secondes
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 500);
    }, 5000);
    
    return toast;
};

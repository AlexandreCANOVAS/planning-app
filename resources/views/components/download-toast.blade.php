<!-- Composant de notification Toast pour les téléchargements -->
<div id="downloadToast" class="fixed bottom-5 right-5 transform translate-y-full opacity-0 transition-all duration-500 ease-in-out z-50 hidden">
    <div class="bg-purple-700 text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
        <div class="mr-3">
            <svg id="downloadToastIcon" class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
        </div>
        <div>
            <p id="downloadToastMessage" class="font-medium">Préparation du document...</p>
            <p class="text-sm text-purple-200">Veuillez patienter pendant le téléchargement</p>
        </div>
    </div>
</div>

<style>
    .toast-visible {
        transform: translateY(0) !important;
        opacity: 1 !important;
        display: block !important;
    }
    
    .toast-success {
        background-color: #10B981 !important; /* green-500 */
    }
    
    .toast-error {
        background-color: #EF4444 !important; /* red-500 */
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(20px); }
        10% { opacity: 1; transform: translateY(0); }
        90% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(20px); }
    }
    
    .animate-fadeInOut {
        animation: fadeInOut 3s ease-in-out forwards;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons/liens de téléchargement PDF et Excel
    const downloadButtons = document.querySelectorAll('a[href*=".pdf"], a[href*="export"], button[form*="export"], a[id*="download"], a[id*="export"], a[href*="comptabilite"], a[href*="plannings"]');
    
    const toast = document.getElementById('downloadToast');
    const toastMessage = document.getElementById('downloadToastMessage');
    const toastIcon = document.getElementById('downloadToastIcon');
    
    // Fonction pour montrer le toast
    function showToast(message, type = 'loading') {
        // Réinitialiser les classes
        toast.classList.remove('toast-success', 'toast-error');
        toastIcon.classList.remove('animate-spin');
        
        // Définir le message
        toastMessage.textContent = message;
        
        // Configurer selon le type
        if (type === 'success') {
            toast.classList.add('toast-success');
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
        } else if (type === 'error') {
            toast.classList.add('toast-error');
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        } else {
            // Type loading par défaut
            toastIcon.classList.add('animate-spin');
            toastIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>';
        }
        
        // Afficher le toast
        toast.classList.add('toast-visible');
        
        // Si c'est un message de succès ou d'erreur, le cacher après 3 secondes
        if (type === 'success' || type === 'error') {
            setTimeout(() => {
                toast.classList.remove('toast-visible');
            }, 3000);
        }
    }
    
    // Ajouter des écouteurs d'événements à tous les boutons de téléchargement
    downloadButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Déterminer le type de document
            const isPdf = this.href && (this.href.includes('.pdf') || this.href.includes('comptabilite') || this.href.includes('plannings'));
            const isExcel = this.href && this.href.includes('excel');
            
            let message = 'Préparation du document...';
            if (isPdf) {
                message = 'Génération du PDF en cours...';
            } else if (isExcel) {
                message = 'Génération du fichier Excel en cours...';
            }
            
            // Afficher le toast de chargement
            showToast(message);
            
            // Après un délai simulant le téléchargement, afficher un message de succès
            setTimeout(() => {
                showToast('Document prêt à être téléchargé !', 'success');
            }, 2000);
        });
    });
});
</script>

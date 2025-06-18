/**
 * Notification Center Component
 * Gère l'affichage et les interactions du centre de notifications
 */
window.notificationCenter = function() {
    return {
        notifications: [],
        unreadCount: 0,
        loading: true,
        refreshInterval: null,
        
        init() {
            this.fetchNotifications();
            
            // Rafraîchir les notifications toutes les 30 secondes
            this.refreshInterval = setInterval(() => {
                this.fetchNotifications();
            }, 30 * 1000);
            
            // S'assurer que les notifications sont rafraîchies quand l'onglet devient actif
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    this.fetchNotifications();
                }
            });
        },
        
        fetchNotifications() {
            this.loading = true;
            
            // Récupérer le token CSRF depuis la balise meta
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch('/api/notifications', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erreur réseau: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && typeof data === 'object') {
                        this.notifications = data.notifications || [];
                        this.unreadCount = data.unread_count || 0;
                        console.log('Centre de notifications - Notifications chargées:', this.notifications.length, 'Non lues:', this.unreadCount);
                    } else {
                        this.notifications = [];
                        this.unreadCount = 0;
                    }
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des notifications:', error);
                    this.notifications = [];
                    this.unreadCount = 0;
                    this.loading = false;
                });
        },
        
        formatDate(dateString) {
            if (!dateString) return '';
            
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            const diffInMinutes = Math.floor(diffInSeconds / 60);
            const diffInHours = Math.floor(diffInMinutes / 60);
            const diffInDays = Math.floor(diffInHours / 24);
            
            // Format relatif pour les dates récentes
            if (diffInSeconds < 60) {
                return 'À l\'instant';
            } else if (diffInMinutes < 60) {
                return `Il y a ${diffInMinutes} min`;
            } else if (diffInHours < 24) {
                return `Il y a ${diffInHours} h`;
            } else if (diffInDays < 7) {
                return `Il y a ${diffInDays} j`;
            }
            
            // Format standard pour les dates plus anciennes
            const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
            return date.toLocaleDateString('fr-FR', options);
        }
    };
}

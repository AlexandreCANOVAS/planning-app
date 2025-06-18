{{-- Scripts pour les notifications - Version inline pour éviter les problèmes de compilation --}}
<script>
    /**
     * Centre de notifications inline
     * Cette version est directement intégrée dans la vue pour éviter les problèmes de compilation
     */
    function notificationCenterInline() {
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
                
                // Récupérer le token CSRF depuis la balise meta ou l'objet window.Laravel
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 (window.Laravel ? window.Laravel.csrfToken : '');
                
                console.log('Récupération des notifications (Centre)...');
                
                fetch('{{ route("notifications.unread") }}', {
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
                        throw new Error('Erreur réseau: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Notifications reçues (Centre):', data);
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.count || 0;
                    console.log('Centre de notifications - Notifications chargées:', this.notifications.length, 'Non lues:', this.unreadCount);
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
                
                // Si c'est déjà au format "il y a X minutes", on le retourne tel quel
                if (dateString.includes('il y a')) {
                    return dateString;
                }
                
                try {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diffTime = Math.abs(now - date);
                    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                    
                    if (diffDays === 0) {
                        // Aujourd'hui: afficher l'heure
                        return `Aujourd'hui à ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                    } else if (diffDays === 1) {
                        // Hier
                        return `Hier à ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                    } else if (diffDays < 7) {
                        // Cette semaine
                        const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                        return `${days[date.getDay()]} à ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                    } else {
                        // Plus ancien
                        return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()} à ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}`;
                    }
                } catch (e) {
                    console.error('Erreur de formatage de date:', e);
                    return dateString;
                }
            }
        };
    }
    
    /**
     * Notification Dropdown inline
     * Cette version est directement intégrée dans la vue pour éviter les problèmes de compilation
     */
    function notificationsDropdownInline() {
        return {
            open: false,
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
            
            toggle() {
                this.open = !this.open;
                
                if (this.open) {
                    this.fetchNotifications();
                }
            },
            
            fetchNotifications() {
                this.loading = true;
                
                // Récupérer le token CSRF depuis la balise meta ou l'objet window.Laravel
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 (window.Laravel ? window.Laravel.csrfToken : '');
                
                console.log('Récupération des notifications (Dropdown)...');
                
                fetch('{{ route("notifications.unread") }}', {
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
                        console.error(`Erreur réseau: ${response.status}`);
                        throw new Error(`Erreur réseau: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && typeof data === 'object') {
                        this.notifications = data.notifications || [];
                        this.unreadCount = data.count || 0;
                        console.log('Dropdown - Notifications chargées:', this.notifications.length, 'Non lues:', this.unreadCount);
                    } else {
                        console.warn('Format de données inattendu:', data);
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
                
                // Si c'est déjà au format "il y a X minutes", on le retourne tel quel
                if (dateString.includes('il y a')) {
                    return dateString;
                }
                
                try {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diffTime = Math.abs(now - date);
                    const diffMinutes = Math.floor(diffTime / (1000 * 60));
                    
                    if (diffMinutes < 60) {
                        return `Il y a ${diffMinutes} minute${diffMinutes > 1 ? 's' : ''}`;
                    }
                    
                    const diffHours = Math.floor(diffMinutes / 60);
                    if (diffHours < 24) {
                        return `Il y a ${diffHours} heure${diffHours > 1 ? 's' : ''}`;
                    }
                    
                    const diffDays = Math.floor(diffHours / 24);
                    if (diffDays < 7) {
                        return `Il y a ${diffDays} jour${diffDays > 1 ? 's' : ''}`;
                    }
                    
                    return `${date.getDate().toString().padStart(2, '0')}/${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear()}`;
                } catch (e) {
                    console.error('Erreur de formatage de date:', e);
                    return dateString;
                }
            }
        };
    }
</script>

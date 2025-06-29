import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Logs de débogage pour Pusher
console.log('Initialisation de Laravel Echo avec Pusher');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '47391404887a6ebd18b1',
    cluster: 'eu',
    encrypted: true,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    disabledTransports: ['xhr_streaming', 'xhr_polling'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    },
    authorizer: (channel, options) => {
        console.log(`Tentative d'autorisation pour le canal: ${channel.name}`);
        return {
            authorize: (socketId, callback) => {
                console.log(`Demande d'authentification pour socketId: ${socketId}`);
                axios.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                .then(response => {
                    console.log(`Authentification réussie pour le canal: ${channel.name}`, response.data);
                    callback(false, response.data);
                })
                .catch(error => {
                    console.error(`Erreur d'authentification pour le canal: ${channel.name}`, error);
                    callback(true, error);
                });
            }
        };
    }
});

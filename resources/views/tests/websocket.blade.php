@extends('layouts.app')

@section('title', 'Test WebSocket')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-4">Test des événements WebSocket</h1>
        
        <div class="mb-6">
            <p class="text-gray-600 mb-2">Cette page permet de tester la réception des événements WebSocket en temps réel.</p>
            <p class="text-gray-600">Ouvrez la console du navigateur pour voir les événements reçus.</p>
        </div>
        
        <div class="bg-purple-50 border border-purple-100 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-medium text-purple-800 mb-2">Événements écoutés</h2>
            <ul class="list-disc list-inside text-purple-700">
                <li>Canal privé: <code class="bg-purple-100 px-2 py-1 rounded">employe.{{ $employe->id }}</code></li>
                <li>Événement: <code class="bg-purple-100 px-2 py-1 rounded">.solde.updated</code></li>
            </ul>
        </div>
        
        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-medium text-blue-800 mb-2">Soldes actuels</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div class="p-3 bg-white border border-blue-200 rounded-lg text-center">
                    <span class="block text-sm text-blue-600">Congés payés</span>
                    <span class="block text-2xl font-bold text-blue-800 solde-conges-value">{{ number_format($employe->solde_conges, 1) }}</span>
                </div>
                <div class="p-3 bg-white border border-green-200 rounded-lg text-center">
                    <span class="block text-sm text-green-600">RTT</span>
                    <span class="block text-2xl font-bold text-green-800 solde-rtt-value">{{ number_format($employe->solde_rtt, 1) }}</span>
                </div>
                <div class="p-3 bg-white border border-purple-200 rounded-lg text-center">
                    <span class="block text-sm text-purple-600">Congés exceptionnels</span>
                    <span class="block text-2xl font-bold text-purple-800 solde-exceptionnels-value">{{ number_format($employe->solde_conges_exceptionnels, 1) }}</span>
                </div>
            </div>
        </div>
        
        <div class="bg-green-50 border border-green-100 rounded-lg p-4">
            <h2 class="text-lg font-medium text-green-800 mb-2">Journal des événements</h2>
            <div id="event-log" class="bg-white border border-gray-200 rounded-lg p-4 h-64 overflow-y-auto font-mono text-sm">
                <div class="text-gray-500">En attente d'événements...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventLog = document.getElementById('event-log');
        
        // Fonction pour ajouter un message au journal
        function logEvent(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const colors = {
                info: 'text-blue-600',
                success: 'text-green-600',
                warning: 'text-amber-600',
                error: 'text-red-600'
            };
            
            const logEntry = document.createElement('div');
            logEntry.className = `mb-1 ${colors[type] || 'text-gray-800'}`;
            logEntry.innerHTML = `<span class="text-gray-500">[${timestamp}]</span> ${message}`;
            
            eventLog.appendChild(logEntry);
            eventLog.scrollTop = eventLog.scrollHeight;
        }
        
        // Écouter les événements de modification des soldes de congés
        const employeId = {{ $employe->id }};
        logEvent(`Écoute du canal: employe.${employeId}`, 'info');
        
        window.Echo.private(`employe.${employeId}`)
            .listen('.solde.updated', (event) => {
                logEvent(`Événement reçu: solde.updated`, 'success');
                logEvent(`Données: ${JSON.stringify(event)}`, 'info');
                
                // Mettre à jour les valeurs affichées
                try {
                    if (event.solde_conges !== undefined) {
                        document.querySelectorAll('.solde-conges-value').forEach(el => {
                            const oldValue = el.textContent;
                            el.textContent = parseFloat(event.solde_conges).toFixed(1);
                            logEvent(`Solde congés mis à jour: ${oldValue} → ${parseFloat(event.solde_conges).toFixed(1)}`, 'success');
                        });
                    }
                    
                    if (event.solde_rtt !== undefined) {
                        document.querySelectorAll('.solde-rtt-value').forEach(el => {
                            const oldValue = el.textContent;
                            el.textContent = parseFloat(event.solde_rtt).toFixed(1);
                            logEvent(`Solde RTT mis à jour: ${oldValue} → ${parseFloat(event.solde_rtt).toFixed(1)}`, 'success');
                        });
                    }
                    
                    if (event.solde_conges_exceptionnels !== undefined) {
                        document.querySelectorAll('.solde-exceptionnels-value').forEach(el => {
                            const oldValue = el.textContent;
                            el.textContent = parseFloat(event.solde_conges_exceptionnels).toFixed(1);
                            logEvent(`Solde exceptionnels mis à jour: ${oldValue} → ${parseFloat(event.solde_conges_exceptionnels).toFixed(1)}`, 'success');
                        });
                    }
                    
                    // Afficher une notification toast
                    if (window.showToast) {
                        window.showToast('Vos soldes de congés ont été mis à jour', 'purple');
                    }
                } catch (error) {
                    logEvent(`Erreur: ${error.message}`, 'error');
                }
            });
            
        // Afficher un message de connexion réussie
        logEvent('Connexion WebSocket établie', 'success');
    });
</script>
@endpush

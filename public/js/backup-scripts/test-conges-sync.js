/**
 * Script de test pour la synchronisation des soldes de congés
 * Ce script permet de tester la synchronisation des soldes de congés entre les différentes vues
 * sans avoir à modifier réellement les données en base.
 */

(function() {
    'use strict';
    
    // Configuration
    const DEBUG = true;
    const TEST_EMPLOYE_ID = window.employeId || 1; // Utilise l'ID de l'employé courant ou 1 par défaut
    const TEST_CP_VALUE = Math.floor(Math.random() * 20) + 1; // Valeur aléatoire entre 1 et 20
    
    // Styles pour les logs
    const logStyles = {
        info: 'color: #3b82f6; font-weight: bold;',
        success: 'color: #10b981; font-weight: bold;',
        error: 'color: #ef4444; font-weight: bold;',
        warning: 'color: #f59e0b; font-weight: bold;',
    };
    
    /**
     * Affiche un message de log stylisé
     */
    function log(message, type = 'info') {
        if (!DEBUG) return;
        console.log(`%c[Test Conges Sync] ${message}`, logStyles[type]);
    }
    
    /**
     * Simule une mise à jour des soldes de congés
     */
    function simulateUpdate() {
        log('Simulation de mise à jour des soldes de congés...');
        
        // Crée un événement personnalisé pour simuler une mise à jour
        const updateEvent = new CustomEvent('conges-cp-updated', {
            detail: {
                employeId: TEST_EMPLOYE_ID,
                cpValue: TEST_CP_VALUE
            }
        });
        
        // Déclenche l'événement
        document.dispatchEvent(updateEvent);
        
        // Stocke également dans localStorage pour tester la synchronisation entre onglets
        localStorage.setItem('conges-cp-update', JSON.stringify({
            employeId: TEST_EMPLOYE_ID,
            cpValue: TEST_CP_VALUE,
            timestamp: Date.now()
        }));
        
        log(`Événement déclenché avec employeId=${TEST_EMPLOYE_ID}, cpValue=${TEST_CP_VALUE}`, 'success');
        
        // Vérifie si les éléments ont été mis à jour
        setTimeout(checkUpdates, 500);
    }
    
    /**
     * Vérifie si les éléments ont été mis à jour
     */
    function checkUpdates() {
        log('Vérification des mises à jour...');
        
        // Recherche tous les éléments qui devraient avoir été mis à jour
        const elements = document.querySelectorAll(`[data-employe-id="${TEST_EMPLOYE_ID}"] .solde-cp, .card-employe[data-employe-id="${TEST_EMPLOYE_ID}"] .solde-cp`);
        
        if (elements.length === 0) {
            log('Aucun élément trouvé à mettre à jour. Vérifiez les sélecteurs dans conges-sync.js', 'warning');
        } else {
            log(`${elements.length} élément(s) trouvé(s)`, 'info');
            
            elements.forEach((element, index) => {
                log(`Élément ${index + 1}: ${element.textContent}`, 'info');
            });
        }
        
        // Vérifie également les éléments dans la vue d'édition
        const editElement = document.getElementById('solde_conges');
        if (editElement) {
            log(`Élément d'édition: ${editElement.value}`, 'info');
        }
    }
    
    /**
     * Initialise le test
     */
    function init() {
        log('Initialisation du test de synchronisation des soldes de congés');
        
        // Crée un bouton de test dans l'interface
        const testButton = document.createElement('button');
        testButton.textContent = 'Tester la synchronisation des soldes';
        testButton.style.position = 'fixed';
        testButton.style.bottom = '20px';
        testButton.style.right = '20px';
        testButton.style.zIndex = '9999';
        testButton.style.padding = '10px 15px';
        testButton.style.backgroundColor = '#8b5cf6';
        testButton.style.color = 'white';
        testButton.style.border = 'none';
        testButton.style.borderRadius = '5px';
        testButton.style.cursor = 'pointer';
        testButton.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.2)';
        
        testButton.addEventListener('click', simulateUpdate);
        
        document.body.appendChild(testButton);
        
        log('Test initialisé. Cliquez sur le bouton pour simuler une mise à jour des soldes.', 'success');
    }
    
    // Initialise le test au chargement de la page
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

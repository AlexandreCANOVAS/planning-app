/**
 * Script pour l'indicateur de force de mot de passe
 * Vérifie la présence de majuscules, minuscules, chiffres et caractères spéciaux
 */
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    
    // Si nous ne sommes pas sur une page avec un champ mot de passe, ne rien faire
    if (!passwordInput) return;
    
    // Créer l'élément d'indicateur de force s'il n'existe pas déjà
    let strengthIndicator = document.getElementById('password-strength-indicator');
    if (!strengthIndicator) {
        // Créer le conteneur principal
        const indicatorContainer = document.createElement('div');
        indicatorContainer.className = 'mt-2';
        
        // Créer l'élément texte pour afficher le niveau de force
        const strengthText = document.createElement('div');
        strengthText.id = 'password-strength-text';
        strengthText.className = 'text-sm font-medium';
        
        // Créer la barre de progression
        const progressContainer = document.createElement('div');
        progressContainer.className = 'w-full bg-gray-200 rounded-full h-2.5 mt-1';
        
        strengthIndicator = document.createElement('div');
        strengthIndicator.id = 'password-strength-indicator';
        strengthIndicator.className = 'h-2.5 rounded-full transition-all duration-300';
        strengthIndicator.style.width = '0%';
        
        // Assembler les éléments
        progressContainer.appendChild(strengthIndicator);
        indicatorContainer.appendChild(strengthText);
        indicatorContainer.appendChild(progressContainer);
        
        // Ajouter des critères de force
        const criteriaContainer = document.createElement('div');
        criteriaContainer.className = 'mt-2 grid grid-cols-2 gap-2 text-xs';
        
        const criteria = [
            { id: 'length', text: '8+ caractères', icon: '📏' },
            { id: 'uppercase', text: 'Majuscule', icon: 'A' },
            { id: 'lowercase', text: 'Minuscule', icon: 'a' },
            { id: 'number', text: 'Chiffre', icon: '1' }
        ];
        
        criteria.forEach(criterion => {
            const criterionElement = document.createElement('div');
            criterionElement.id = `criterion-${criterion.id}`;
            criterionElement.className = 'flex items-center text-gray-500';
            criterionElement.innerHTML = `
                <span class="inline-flex items-center justify-center w-5 h-5 mr-2 rounded-full bg-gray-200 text-gray-500">
                    ${criterion.icon}
                </span>
                ${criterion.text}
            `;
            criteriaContainer.appendChild(criterionElement);
        });
        
        indicatorContainer.appendChild(criteriaContainer);
        
        // Insérer après le champ de mot de passe
        passwordInput.parentNode.parentNode.insertBefore(indicatorContainer, passwordInput.parentNode.nextSibling);
    }
    
    // Fonction pour évaluer la force du mot de passe
    function checkPasswordStrength(password) {
        // Critères
        const hasLength = password.length >= 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        // Mettre à jour l'état visuel des critères
        updateCriterion('length', hasLength);
        updateCriterion('uppercase', hasUpperCase);
        updateCriterion('lowercase', hasLowerCase);
        updateCriterion('number', hasNumbers);
        
        // Calculer le score (0-4)
        let score = 0;
        if (hasLength) score++;
        if (hasUpperCase) score++;
        if (hasLowerCase) score++;
        if (hasNumbers) score++;
        if (hasSpecialChar) score++;
        
        // Déterminer le niveau de force
        let strength = '';
        let color = '';
        let percentage = 0;
        
        if (password.length === 0) {
            strength = '';
            color = '';
            percentage = 0;
        } else if (score <= 2) {
            strength = 'Faible';
            color = 'bg-red-500';
            percentage = 33;
        } else if (score <= 3) {
            strength = 'Moyen';
            color = 'bg-yellow-500';
            percentage = 66;
        } else {
            strength = 'Fort';
            color = 'bg-green-500';
            percentage = 100;
        }
        
        return { strength, color, percentage };
    }
    
    // Fonction pour mettre à jour l'affichage d'un critère
    function updateCriterion(id, isMet) {
        const criterion = document.getElementById(`criterion-${id}`);
        if (!criterion) return;
        
        if (isMet) {
            criterion.classList.remove('text-gray-500');
            criterion.classList.add('text-green-600');
            criterion.querySelector('span').classList.remove('bg-gray-200', 'text-gray-500');
            criterion.querySelector('span').classList.add('bg-green-100', 'text-green-600');
        } else {
            criterion.classList.remove('text-green-600');
            criterion.classList.add('text-gray-500');
            criterion.querySelector('span').classList.remove('bg-green-100', 'text-green-600');
            criterion.querySelector('span').classList.add('bg-gray-200', 'text-gray-500');
        }
    }
    
    // Écouter les changements dans le champ de mot de passe
    passwordInput.addEventListener('input', function() {
        const strengthText = document.getElementById('password-strength-text');
        const strengthIndicator = document.getElementById('password-strength-indicator');
        
        const { strength, color, percentage } = checkPasswordStrength(this.value);
        
        // Mettre à jour l'indicateur visuel
        if (strengthText) {
            strengthText.textContent = strength ? `Force: ${strength}` : '';
            
            // Réinitialiser les classes de couleur
            strengthText.classList.remove('text-red-500', 'text-yellow-500', 'text-green-500');
            
            // Ajouter la classe de couleur appropriée
            if (color === 'bg-red-500') strengthText.classList.add('text-red-500');
            if (color === 'bg-yellow-500') strengthText.classList.add('text-yellow-500');
            if (color === 'bg-green-500') strengthText.classList.add('text-green-500');
        }
        
        if (strengthIndicator) {
            // Réinitialiser les classes de couleur
            strengthIndicator.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500');
            
            // Ajouter la classe de couleur appropriée si une force est détectée
            if (color) {
                strengthIndicator.classList.add(color);
            }
            
            // Mettre à jour la largeur
            strengthIndicator.style.width = `${percentage}%`;
        }
    });
});

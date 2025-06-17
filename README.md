# Application de Gestion des Plannings

<p align="center">
  <img src="public/img/logo.png" alt="Logo Planning App" width="200">
</p>

## À propos

L'Application de Gestion des Plannings est une solution complète pour la gestion des horaires des employés, conçue pour simplifier la planification, le suivi des heures travaillées et la gestion des congés. Avec une interface minimaliste et épurée, elle offre une expérience utilisateur optimale aussi bien pour les employeurs que pour les employés.

## Fonctionnalités principales

### Gestion des plannings
- Création et modification de plannings mensuels pour chaque employé
- Visualisation des plannings par jour, semaine et mois
- Identification claire des modifications avec surlignage en rouge
- Export PDF des plannings avec mise en évidence des modifications
- Système d'échange de jours entre employés

### Gestion des employés
- Profils détaillés des employés
- Gestion des lieux de travail
- Suivi des formations
- Gestion des documents administratifs

### Comptabilité et rapports
- Calcul automatique des heures travaillées
- Suivi des heures supplémentaires
- Calcul des heures de nuit, dimanches et jours fériés
- Génération de rapports comptables
- Export PDF et Excel des données

### Gestion des congés
- Demande et approbation de congés
- Suivi des soldes de congés
- Historique des congés pris

### Système de notification
- Notifications en temps réel pour les modifications de planning
- Alertes pour les demandes de congés
- Notifications pour les échanges de jours

## Captures d'écran

<p align="center">
  <img src="public/img/screenshots/dashboard.png" alt="Dashboard" width="400">
  <img src="public/img/screenshots/planning.png" alt="Planning" width="400">
</p>

## Technologies utilisées

- **Backend**: Laravel 11
- **Frontend**: Blade, JavaScript, TailwindCSS
- **Base de données**: MySQL
- **PDF**: DomPDF
- **Excel**: PHPSpreadsheet
- **Notifications**: Pusher

## Installation

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- MySQL
- Node.js et NPM

### Étapes d'installation

1. Cloner le dépôt
   ```bash
   git clone https://github.com/votre-utilisateur/planning-app.git
   cd planning-app
   ```

2. Installer les dépendances
   ```bash
   composer install
   npm install
   ```

3. Configurer l'environnement
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configurer la base de données dans le fichier `.env`

5. Exécuter les migrations et les seeders
   ```bash
   php artisan migrate --seed
   ```

6. Compiler les assets
   ```bash
   npm run dev
   ```

7. Lancer le serveur
   ```bash
   php artisan serve
   ```

## Utilisation

### Connexion
- Employeur: admin@example.com / password
- Employé: employe@example.com / password

### Fonctionnalités principales
- **Dashboard**: Vue d'ensemble des plannings, congés et notifications
- **Plannings**: Création et modification des plannings mensuels
- **Employés**: Gestion des profils employés
- **Lieux**: Gestion des lieux de travail
- **Congés**: Gestion des demandes de congés
- **Comptabilité**: Rapports et exports des heures travaillées

## Dernières améliorations

- **Export PDF amélioré**: Mise en évidence des jours modifiés avec le jour, la date et les informations de planning en rouge
- **Système de notification**: Notifications en temps réel pour les modifications de planning
- **Interface utilisateur**: Design minimaliste et épuré avec un thème sombre élégant
- **Comptabilité**: Ajout du calcul des heures de nuit, dimanches et jours fériés

## Support

Pour toute question ou assistance, veuillez contacter l'équipe de support à support@planning-app.com

## Licence

Cette application est un logiciel propriétaire. Tous droits réservés.

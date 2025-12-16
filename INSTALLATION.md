# 🎮 Play to Help - Installation

## 📋 Prérequis
- PHP 7.4+
- MySQL/MariaDB
- Composer
- XAMPP/WAMP/MAMP

## 🚀 Installation

### 1. Cloner le projet
```bash
git clone https://github.com/azizchaihbi-spec/projet-gaming-social.git
cd projet-gaming-social
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configuration base de données
- Créer une base `play-to-help`
- Importer le fichier SQL (si fourni)
- Configurer `config/db.php`

### 4. Configuration Stripe
- Modifier `config/stripe_config.php`
- Ajouter vos clés Stripe Test

### 5. Configuration Email
- Modifier `config/email_config.php`
- Configurer SMTP Gmail

## 📁 Structure MVC
```
├── models/          # Modèles (Don, Challenge)
├── controllers/     # Contrôleurs (CRUD)
├── views/          # Vues (frontoffice/backoffice)
├── config/         # Configuration
└── assets/         # CSS/JS/Images
```

## ✅ Fonctionnalités
- ✅ CRUD complet avec architecture MVC
- ✅ Paiement Stripe intégré
- ✅ Validation JavaScript avancée
- ✅ Génération PDF automatique
- ✅ Emails de confirmation
- ✅ Design gaming responsive
- ✅ Animations CSS3/JavaScript

## 🎯 Tests
- Carte test Stripe : `4242 4242 4242 4242`
- Email test : `test@playtohelp.com`

## 🔧 Dépendances (vendor/)
Le dossier `vendor/` n'est pas versionné.
Après clonage, exécuter : `composer install`

## 📞 Support
Contact : [ton-email]
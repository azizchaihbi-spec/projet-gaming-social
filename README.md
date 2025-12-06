# Play to Help - Plateforme de Dons Gaming

Plateforme web permettant aux associations de recevoir des dons via des challenges gaming.

## Fonctionnalités

- Système de paiement en ligne avec Stripe (mode test)
- Envoi automatique d'emails de reçu aux donateurs
- Génération de PDF pour les reçus fiscaux
- Dashboard backoffice avec gestion des dons et challenges
- Système de tri et filtrage avancé
- Édition de la progression des challenges

## Installation

1. Cloner le dépôt
2. Copier `.env.example` vers `.env` et configurer vos clés
3. Installer les dépendances dans le dossier `vendor/`
4. Configurer la base de données dans `config/db.php`
5. Lancer le serveur local (XAMPP, WAMP, etc.)

## Configuration

Les clés sensibles sont stockées dans le fichier `.env` (non versionné).
Voir `.env.example` pour la structure requise.

## Technologies

- PHP 7.4+
- MySQL
- Stripe API (paiements)
- PHPMailer (emails)
- TCPDF (génération PDF)
- Bootstrap 5
- jQuery

## Sécurité

Les clés API et mots de passe sont exclus du versioning via `.gitignore`.

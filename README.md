# 🎮 Play to Help - Plateforme Gaming Solidaire

## 📋 Table des Matières
- [Vue d'ensemble](#vue-densemble)
- [Architecture du Projet](#architecture-du-projet)
- [Entités et Base de Données](#entités-et-base-de-données)
- [Pages Frontoffice](#pages-frontoffice)
- [Pages Backoffice](#pages-backoffice)
- [Intégrations et APIs](#intégrations-et-apis)
- [Technologies Utilisées](#technologies-utilisées)
- [Installation et Configuration](#installation-et-configuration)
- [Fonctionnalités Principales](#fonctionnalités-principales)

---

## 🎯 Vue d'ensemble

**Play to Help** est une plateforme innovante qui unit l'univers du gaming et l'aide humanitaire. Elle permet aux streamers et gamers de transformer leur passion en actions solidaires concrètes pour soutenir des associations caritatives.

### Concept Principal
- **Streamers** : Diffusent du contenu gaming engageant
- **Communauté** : Participe, interagit et soutient les streams
- **Associations** : Reçoivent des fonds collectés via la plateforme

---

## 🏗️ Architecture du Projet

```
projet-gaming-social/
├── api/                          # APIs REST
│   ├── clip_actions.php
│   ├── discord_test.php
│   ├── event_actions.php
│   ├── stream_actions.php
│   └── theme_actions.php
├── config/                       # Configuration
│   ├── config.php               # Base de données
│   ├── discord.php              # Discord API
│   ├── email_config.php         # Configuration email
│   ├── recaptcha.php           # reCAPTCHA
│   └── stripe_config.php       # Stripe paiements
├── controllers/                 # Contrôleurs MVC
├── models/                      # Modèles de données
├── views/
│   ├── frontoffice/            # Interface utilisateur
│   └── backoffice/             # Interface admin
└── vendor/                     # Dépendances
```

---

## 🗄️ Entités et Base de Données

### 1. **Utilisateur** (`utilisateur`)
```sql
- id_user (PK)
- nom, prenom, email
- mot_de_passe (hashé)
- date_naissance, genre, localisation
- date_inscription, role
- statut_compte
```

### 2. **Streamer** (`streamer`)
```sql
- id_user (FK vers utilisateur)
- pseudo, plateforme
- url_profil, nb_followers
- description, statut
```

### 3. **Association** (`association`)
```sql
- id_association (PK)
- name, description
- email, website, phone
- adresse, date_creation
- statut, logo_url
```

### 4. **Stream** (`stream`)
```sql
- id_stream (PK)
- id_streamer (FK)
- id_association (FK)
- titre, plateforme, url
- date_debut, date_fin, statut
- don_total, nb_vues, nb_likes
- nb_dislikes, nb_commentaires
```

### 5. **Don** (`don`)
```sql
- id_don (PK)
- id_association (FK)
- montant, devise
- nom, prenom, email
- date_don, statut_paiement
- stripe_payment_id
```

### 6. **Événement** (`evenement`)
```sql
- id_evenement (PK)
- titre, description, theme
- date_debut, date_fin
- lieu, objectif, statut
```

### 7. **Challenge** (`challenge`)
```sql
- id_challenge (PK)
- id_association (FK)
- name, description
- objectif, progression
- recompense, date_limite
```

---

## 🎨 Pages Frontoffice

### 🏠 **Accueil.php**
**Entités utilisées :** Aucune (page statique avec contenu dynamique)
**Fonctionnalités :**
- Hero Section avec animations
- Section "À propos" 
- Section "Comment ça marche" (3 étapes)
- Témoignage avec statistiques
- Section "Most Popular" (jeux populaires)
- Gaming Library
- Animations JavaScript avancées
- Particules flottantes et effets visuels

**Code principal :**
```php
// Contenu statique avec animations CSS/JS
// Pas d'interaction base de données directe
```

### 🎮 **streams.php**
**Entités utilisées :** `stream`, `streamer`, `utilisateur`
**Fonctionnalités :**
- Liste des streams en direct et planifiés
- Classement des top streamers par engagement
- Filtres par plateforme, statut, tri
- Interactions : vues, likes, dislikes, commentaires
- Thumbnails dynamiques avec fallback

**Code principal :**
```php
// Récupération des streams
$streamController = new StreamController();
$streams = $streamController->listStreams();

// JavaScript pour interactions
fetch('api/stream_actions.php?action=list')
```

### 🤝 **association.php**
**Entités utilisées :** `association`, `don`, `challenge`
**Fonctionnalités :**
- Slider des associations partenaires
- Modal détaillé pour chaque association
- Liste complète des associations
- Challenges en cours avec progression
- Statistiques de dons par association

**Code principal :**
```php
// Récupération associations avec stats
$stmt = $pdo->query("
    SELECT a.*, 
           COALESCE(SUM(d.montant), 0) as total_dons_reel,
           COUNT(d.id_don) as nombre_donateurs
    FROM association a 
    LEFT JOIN don d ON a.id_association = d.id_association 
    GROUP BY a.id_association
");
```

### 💰 **don.php**
**Entités utilisées :** `don`, `association`, `challenge`
**Fonctionnalités :**
- Formulaire de don avec Stripe
- Mode don direct (sans paiement)
- Création de challenges personnalisés
- Historique des dons récents
- Validation et sécurisation des paiements

**Code principal :**
```php
// Intégration Stripe
require_once 'config/stripe_config.php';
\Stripe\Stripe::setApiKey($stripe_secret_key);

// Création session Stripe
$session = \Stripe\Checkout\Session::create([...]);
```

### 📅 **browse.php**
**Entités utilisées :** `evenement`
**Fonctionnalités :**
- Liste des événements solidaires
- Filtres par statut, thème, date
- Design cards avec statuts visuels
- Intégration Discord pour rejoindre

**Code principal :**
```php
// Récupération événements
$eventController = new EventController();
$events = $eventController->listEvents();
```

### 👤 **profile.php**
**Entités utilisées :** `utilisateur`
**Fonctionnalités :**
- Profil utilisateur avec avatar
- Informations personnelles
- Statut en ligne/hors ligne
- Export PDF du profil
- Gestion des amis

**Code principal :**
```php
session_start();
$user = $_SESSION['user'];
// Affichage des données utilisateur
```

### 📝 **register.php** & **login.php**
**Entités utilisées :** `utilisateur`
**Fonctionnalités :**
- Inscription avec validation
- Connexion sécurisée
- reCAPTCHA protection
- Hashage des mots de passe
- Gestion des sessions

### ❓ **q&a.php**
**Entités utilisées :** `publication`, `reponse`
**Fonctionnalités :**
- Forum communautaire
- Questions/Réponses
- Système de votes
- Modération

---

## 🔧 Pages Backoffice

### 📊 **dashboard.php**
**Entités utilisées :** `stream`, `evenement`, `utilisateur`
**Fonctionnalités :**
- Graphiques Chart.js (dons par stream, répartition statuts)
- Statistiques globales
- Gestion unifiée streams/événements
- Tableaux interactifs avec actions CRUD
- Navigation par onglets

**Code principal :**
```php
$streamController = new StreamController();
$eventController = new EventController();
$streams = $streamController->listStreams();
$events = $eventController->listEvents();

// Génération données pour graphiques
$streamLabels = [];
$streamDons = [];
foreach ($streams as $stream) {
    $streamLabels[] = $stream['titre'];
    $streamDons[] = $stream['don_total'];
}
```

### 🎮 **stream/** (Gestion Streams)
- `streams.php` : Liste des streams
- `streamadd.php` : Ajout/Modification
- `deletestream.php` : Suppression

### 📅 **events/** (Gestion Événements)
- `browse.php` : Liste des événements
- `event_add_edit.php` : Ajout/Modification
- `event_actions.php` : Actions CRUD

---

## 🔌 Intégrations et APIs

### 1. **Stripe Payment**
```php
// Configuration
require_once 'config/stripe_config.php';
\Stripe\Stripe::setApiKey($stripe_secret_key);

// Création session checkout
$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'eur',
            'product_data' => ['name' => 'Don pour ' . $association_name],
            'unit_amount' => $montant * 100,
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $success_url,
    'cancel_url' => $cancel_url,
]);
```

### 2. **Gemini AI Chatbot**
```javascript
// Configuration API
const API_KEY = 'AIzaSyBJ1keN8Wog_7zfYA_c49S8KzWUdIESsPY';
const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

// Requête avec rate limiting
const response = await fetch(API_URL, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-goog-api-key': API_KEY
    },
    body: JSON.stringify({
        contents: [{ parts: [{ text: userMessage }] }]
    })
});
```

### 3. **APIs REST Internes**
- `api/stream_actions.php` : CRUD streams, interactions (vues, likes)
- `api/event_actions.php` : CRUD événements
- `api/clip_actions.php` : Gestion clips vidéo
- `api/theme_actions.php` : Gestion thèmes

### 4. **Discord Integration**
```php
// Configuration Discord
$discord_webhook_url = "https://discord.com/api/webhooks/...";
// Notifications automatiques pour nouveaux dons/événements
```

---

## 💻 Technologies Utilisées

### **Backend**
- **PHP 8.0+** : Langage principal
- **MySQL** : Base de données
- **PDO** : Accès base de données sécurisé
- **MVC Pattern** : Architecture organisée

### **Frontend**
- **HTML5/CSS3** : Structure et style
- **JavaScript ES6+** : Interactivité
- **Bootstrap 5** : Framework CSS responsive
- **Chart.js** : Graphiques interactifs
- **Owl Carousel** : Sliders
- **Font Awesome** : Icônes

### **Intégrations**
- **Stripe API** : Paiements sécurisés
- **Gemini AI** : Chatbot intelligent
- **reCAPTCHA** : Protection anti-spam
- **Discord Webhooks** : Notifications

### **Design**
- **Palette Gaming** : Violet (#8A2BE2), Indigo (#4B0082), Bleu (#00BFFF)
- **Animations CSS** : Hover effects, transitions
- **Responsive Design** : Mobile-first
- **Gaming UI** : Effets néon, glassmorphism

---

## ⚙️ Installation et Configuration

### 1. **Prérequis**
```bash
- PHP 8.0+
- MySQL 5.7+
- Serveur web (Apache/Nginx)
- Composer (optionnel)
```

### 2. **Configuration Base de Données**
```php
// config/config.php
class Config {
    private static $host = 'localhost';
    private static $dbname = 'playtohelp_db';
    private static $username = 'root';
    private static $password = '';
}
```

### 3. **Configuration Stripe**
```php
// config/stripe_config.php
$stripe_publishable_key = "pk_test_...";
$stripe_secret_key = "sk_test_...";
```

### 4. **Configuration Gemini AI**
```javascript
// Dans chatbot.php
const API_KEY = 'AIzaSyBJ1keN8Wog_7zfYA_c49S8KzWUdIESsPY';
```

### 5. **Structure Base de Données**
```sql
-- Importer playtohelp_merged.sql
-- Contient toutes les tables et données de test
```

---

## 🚀 Fonctionnalités Principales

### **Pour les Utilisateurs**
- ✅ Inscription/Connexion sécurisée
- ✅ Profil personnalisable avec avatar
- ✅ Navigation intuitive et responsive
- ✅ Chatbot AI pour assistance
- ✅ Système d'amis et statuts

### **Pour les Streamers**
- ✅ Gestion des streams (planification, statuts)
- ✅ Statistiques détaillées (vues, likes, dons)
- ✅ Intégration multi-plateformes
- ✅ Thumbnails personnalisées

### **Pour les Associations**
- ✅ Profil détaillé avec statistiques
- ✅ Réception de dons sécurisés
- ✅ Création de challenges
- ✅ Suivi des objectifs

### **Pour les Administrateurs**
- ✅ Dashboard unifié avec graphiques
- ✅ Gestion complète CRUD
- ✅ Statistiques en temps réel
- ✅ Modération du contenu

### **Paiements et Sécurité**
- ✅ Intégration Stripe complète
- ✅ Paiements sécurisés PCI-DSS
- ✅ Hashage des mots de passe
- ✅ Protection reCAPTCHA
- ✅ Validation côté serveur

### **Design et UX**
- ✅ Interface gaming futuriste
- ✅ Animations et effets visuels
- ✅ Design responsive mobile-first
- ✅ Accessibilité ARIA
- ✅ Performance optimisée

---

## 📱 Responsive Design

Le projet est entièrement responsive avec des breakpoints optimisés :
- **Mobile** : < 768px
- **Tablette** : 768px - 1024px  
- **Desktop** : > 1024px

---

## 🔒 Sécurité

- **Hashage bcrypt** pour les mots de passe
- **Validation CSRF** sur les formulaires
- **Échappement XSS** avec `htmlspecialchars()`
- **Requêtes préparées** PDO contre l'injection SQL
- **reCAPTCHA** contre les bots
- **HTTPS** recommandé en production

---

## 🎯 Objectifs du Projet

1. **Social Impact** : Transformer le gaming en force positive
2. **Innovation** : Pionnier du gaming solidaire
3. **Communauté** : Rassembler gamers et associations
4. **Technologie** : Stack moderne et performante
5. **Accessibilité** : Plateforme inclusive et intuitive

---

## 👥 Équipe de Développement

- **Aziz** : Backend & Base de données
- **Maya** : Frontend & Design
- **Ismail** : Intégrations & APIs
- **Sinda** : Tests & Documentation

---

## 📄 Licence

Ce projet est développé dans le cadre d'un projet académique.
© 2025 Play to Help - Tous droits réservés.

---

*Transformons ensemble la passion du gaming en actions solidaires ! 🎮❤️*
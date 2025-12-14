# Configuration - Play to Help

## Fichiers de Configuration

### config.php
Fichier de configuration de la base de données :

#### Classe Config
- **getConnexion()** : Connexion PDO à la base de données MySQL
- Configuration : `playtohelp_merged` sur `localhost`
- Options PDO : `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES = false`

### discord.php
Fichier de configuration Discord :

#### Classe DiscordConfig
- **Webhooks Discord** : URLs pour les notifications
  - `WEBHOOK_EVENTS` : Notifications d'événements
  - `WEBHOOK_STREAMS` : Notifications de streams
  - `WEBHOOK_DONATIONS` : Notifications de dons
  - `WEBHOOK_GENERAL` : Notifications générales

- **Couleurs Discord** : Constantes hexadécimales pour les embeds
  - `COLOR_EVENT` : Rose (#EC6090)
  - `COLOR_STREAM` : Violet Twitch (#9146FF)
  - `COLOR_DONATION` : Vert (#28A745)
  - `COLOR_SUCCESS` : Vert clair (#00FF00)
  - `COLOR_WARNING` : Orange (#FFA500)
  - `COLOR_ERROR` : Rouge (#FF0000)

### paths.php
Gestion dynamique des chemins de l'application :
- **PathConfig::getBasePath()** : Détection automatique du chemin de base
- **PathConfig::getBaseUrl()** : URL complète de l'application
- **PathConfig::toAbsoluteUrl()** : Conversion de chemins relatifs en URLs absolues

### discord.php
Configuration spécifique Discord (si utilisé séparément de config.php)

## Utilisation

```php
// Connexion à la base de données
require_once 'config/config.php';
$db = Config::getConnexion();

// Utilisation des webhooks Discord
require_once 'config/discord.php';
$webhook = DiscordConfig::WEBHOOK_EVENTS;
$color = DiscordConfig::COLOR_EVENT;

// Gestion des chemins
require_once 'config/paths.php';
$basePath = PathConfig::getBasePath();
$imageUrl = PathConfig::toAbsoluteUrl('/uploads/image.jpg');
```

## Migration depuis db.php

Le fichier `db.php` a été renommé en `config.php` pour une meilleure organisation. Tous les fichiers du projet ont été mis à jour automatiquement pour utiliser le nouveau nom.
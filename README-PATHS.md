# Play to Help - Documentation Technique

## Structure du Projet

```
├── api/                    # APIs REST
│   ├── clip_actions.php
│   ├── event_actions.php
│   ├── stream_actions.php
│   └── theme_actions.php
├── config/                 # Configuration
│   ├── config.php          # Connexion base de données
│   ├── discord.php         # Webhooks Discord
│   └── paths.php           # Gestion des chemins
├── controllers/            # Contrôleurs MVC
├── models/                 # Modèles de données
└── views/
    ├── backoffice/         # Administration
    └── frontoffice/        # Site public
```

## Configuration

### Base de données (`config/config.php`)
```php
$db = Config::getConnexion();
```

### Discord (`config/discord.php`)
```php
DiscordConfig::WEBHOOK_EVENTS
DiscordConfig::COLOR_EVENT
```

### Chemins dynamiques (`config/paths.php`)
```php
PathConfig::getBasePath()
PathConfig::toAbsoluteUrl($path)
```

## Gestion des Chemins (Frontend)

Le fichier `views/frontoffice/assets/js/path-utils.js` gère les chemins côté client :

```javascript
window.PathUtils.getBasePath()
window.PathUtils.resolveApiUrl('event_actions.php')
window.PathUtils.toAbsolutePath('/uploads/image.jpg')
```

Cela permet au projet de fonctionner peu importe le nom du dossier.
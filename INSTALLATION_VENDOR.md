# Installation des bibliothèques (dossier vendor/)

Le dossier `vendor/` n'est pas versionné pour des raisons de sécurité et de taille.

## Installation manuelle

Téléchargez et placez les bibliothèques suivantes dans le dossier `vendor/` :

### 1. PHPMailer
- Télécharger depuis : https://github.com/PHPMailer/PHPMailer
- Placer dans : `vendor/PHPMailer/`

### 2. Stripe PHP SDK
- Télécharger depuis : https://github.com/stripe/stripe-php
- Placer dans : `vendor/stripe/stripe-php/`

### 3. TCPDF
- Télécharger depuis : https://github.com/tecnickcom/TCPDF
- Placer dans : `vendor/tcpdf/`

### 4. Bootstrap & jQuery (optionnel)
- Bootstrap 5 : https://getbootstrap.com/
- jQuery : https://jquery.com/
- Placer dans : `vendor/bootstrap/` et `vendor/jquery/`

## Structure attendue

```
vendor/
├── PHPMailer/
│   └── src/
│       ├── PHPMailer.php
│       ├── SMTP.php
│       └── Exception.php
├── stripe/
│   └── stripe-php/
│       ├── init.php
│       └── lib/
├── tcpdf/
│   ├── tcpdf.php
│   └── config/
├── bootstrap/
│   ├── css/
│   └── js/
└── jquery/
    └── jquery.min.js
```

## Alternative : Composer (recommandé)

Si vous avez Composer installé :

```bash
composer require phpmailer/phpmailer
composer require stripe/stripe-php
composer require tecnickcom/tcpdf
```

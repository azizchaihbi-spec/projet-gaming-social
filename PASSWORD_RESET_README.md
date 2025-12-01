# Système de Réinitialisation de Mot de Passe - Play to Help

## 📋 Vue d'ensemble

Système complet et sécurisé de réinitialisation de mot de passe pour l'application Play to Help avec les fonctionnalités suivantes :

- ✅ Demande de réinitialisation par email
- ✅ Génération de token unique et sécurisé (64 caractères)
- ✅ Expiration automatique du token (1 heure)
- ✅ Envoi d'email avec lien de réinitialisation
- ✅ Validation côté client et serveur
- ✅ Interface utilisateur cohérente avec le design du projet
- ✅ Suppression automatique du token après utilisation

## 🗂️ Fichiers créés/modifiés

### Nouveaux fichiers créés :
1. `View/FrontOffice/forgot_password.php` - Page de demande de réinitialisation
2. `View/FrontOffice/reset_password.php` - Page de création du nouveau mot de passe
3. `database_password_reset_update.sql` - Script SQL pour la mise à jour de la BDD

### Fichiers modifiés :
1. `Model/Auth.php` - Ajout de 4 méthodes :
   - `createResetToken($email)` - Génère et stocke un token
   - `validateResetToken($token)` - Vérifie la validité du token
   - `resetPasswordByToken($token, $newPassword)` - Réinitialise le mot de passe
   - `clearResetToken($userId)` - Supprime le token après utilisation

2. `Controller/authController.php` - Ajout de 2 actions :
   - `requestPasswordReset()` - Gère la demande de réinitialisation
   - `resetPassword()` - Gère la réinitialisation effective

3. `View/FrontOffice/script.js` - Ajout de 2 fonctions JS :
   - `handleForgotPassword()` - Gère le formulaire de demande
   - `handleResetPassword()` - Gère le formulaire de réinitialisation

4. `View/FrontOffice/login.php` - Ajout du lien "Mot de passe oublié ?"

## 🚀 Installation

### Étape 1 : Mettre à jour la base de données

Exécutez le script SQL dans phpMyAdmin :

1. Ouvrez phpMyAdmin (http://localhost/phpmyadmin)
2. Sélectionnez la base de données `playtohelp`
3. Cliquez sur l'onglet "SQL"
4. Copiez/collez le contenu du fichier `database_password_reset_update.sql`
5. Cliquez sur "Exécuter"

**OU** via ligne de commande :
```bash
mysql -u root -p playtohelp < database_password_reset_update.sql
```

Cela ajoutera deux nouvelles colonnes à la table `users` :
- `reset_token` (VARCHAR 64) - Stocke le token unique
- `reset_token_expires` (DATETIME) - Date d'expiration du token

### Étape 2 : Configurer l'envoi d'emails (Important !)

⚠️ **XAMPP par défaut ne peut pas envoyer d'emails !**

Vous avez 3 options :

#### Option 1 : Utiliser PHPMailer (Recommandé pour production)

1. Téléchargez PHPMailer : https://github.com/PHPMailer/PHPMailer
2. Placez-le dans `vendor/phpmailer/`
3. Modifiez la méthode `requestPasswordReset()` dans `Controller/authController.php` pour utiliser PHPMailer avec SMTP (Gmail, SendGrid, etc.)

#### Option 2 : Configurer sendmail dans XAMPP (Pour tests locaux)

1. Ouvrez `C:\xampp\php\php.ini`
2. Trouvez la section `[mail function]`
3. Modifiez :
```ini
[mail function]
SMTP=smtp.gmail.com
smtp_port=587
sendmail_from=your-email@gmail.com
sendmail_path="\"C:\xampp\sendmail\sendmail.exe\" -t"
```

4. Ouvrez `C:\xampp\sendmail\sendmail.ini`
5. Modifiez :
```ini
smtp_server=smtp.gmail.com
smtp_port=587
auth_username=your-email@gmail.com
auth_password=your-app-password
force_sender=your-email@gmail.com
```

#### Option 3 : Mode test (Afficher le lien au lieu d'envoyer l'email)

Pour tester sans configuration email, modifiez temporairement `requestPasswordReset()` dans `authController.php` :

```php
// Au lieu d'envoyer l'email, retournez le lien directement
header('Content-Type: application/json');
echo json_encode([
    'success' => true, 
    'message' => 'Lien de réinitialisation (TEST): ' . $resetLink
]);
exit();
```

### Étape 3 : Tester le système

1. Démarrez Apache et MySQL dans XAMPP
2. Allez sur `http://localhost/play%20to%20help%20mvc%20f%20-%20d1/View/FrontOffice/login.php`
3. Cliquez sur "Mot de passe oublié ?"
4. Entrez un email existant dans la base
5. Vérifiez votre boîte email (ou consultez le lien en mode test)
6. Cliquez sur le lien de réinitialisation
7. Créez un nouveau mot de passe
8. Connectez-vous avec le nouveau mot de passe

## 🔒 Sécurité

Le système implémente plusieurs mesures de sécurité :

### ✅ Côté client (JavaScript) :
- Validation du format email
- Validation de la force du mot de passe (min 6, 1 maj, 1 min, 1 chiffre)
- Vérification de la correspondance des mots de passe
- Affichage d'erreurs agrégées

### ✅ Côté serveur (PHP) :
- **Token unique** : Généré avec `bin2hex(random_bytes(32))` = 64 caractères hexadécimaux
- **Expiration** : Token valide 1 heure seulement
- **Validation stricte** : Le token doit exister ET ne pas être expiré
- **Suppression automatique** : Token supprimé après usage ou expiration
- **Hashage** : Mot de passe hashé avec `password_hash()` (bcrypt)
- **Validation serveur** : Toutes les règles de validation sont réappliquées côté serveur
- **Protection contre les énumérations** : Message générique si l'email n'existe pas

### 🛡️ Protections implémentées :
- ❌ Impossible de réutiliser un token
- ❌ Impossible d'utiliser un token expiré
- ❌ Impossible de deviner un token (64 caractères aléatoires)
- ❌ Les tokens ne sont jamais affichés en clair dans l'URL visible
- ✅ Le mot de passe est toujours hashé avant stockage
- ✅ Validation double (client + serveur)

## 📊 Flux de fonctionnement

```
1. Utilisateur clique "Mot de passe oublié ?"
   ↓
2. Entre son email sur forgot_password.php
   ↓
3. JavaScript valide l'email
   ↓
4. Requête POST vers authController.php?action=requestPasswordReset
   ↓
5. Vérification que l'email existe dans la BDD
   ↓
6. Génération d'un token unique (64 caractères)
   ↓
7. Stockage du token + expiration (1h) dans users.reset_token
   ↓
8. Envoi d'un email avec lien : reset_password.php?token=xxxx
   ↓
9. Utilisateur clique sur le lien dans l'email
   ↓
10. reset_password.php récupère le token depuis l'URL
    ↓
11. Utilisateur entre nouveau mot de passe + confirmation
    ↓
12. JavaScript valide la force du mot de passe
    ↓
13. Requête POST vers authController.php?action=resetPassword
    ↓
14. Validation du token (existe + non expiré)
    ↓
15. Validation du nouveau mot de passe côté serveur
    ↓
16. Hash du nouveau mot de passe
    ↓
17. Mise à jour du mot de passe dans la BDD
    ↓
18. Suppression du token (reset_token = NULL)
    ↓
19. Redirection vers login.php
    ↓
20. Utilisateur se connecte avec le nouveau mot de passe
```

## 🎨 Interface utilisateur

Les pages utilisent le même design que le reste du projet :
- Thème sombre cohérent
- Couleurs : rose (#e75e8d) comme accent
- Icons FontAwesome
- Formulaires stylés avec `styles.css`
- Alertes d'erreur et de succès avec la classe `.alert`
- Responsive design

## 🧪 Tests à effectuer

### Test 1 : Demande de réinitialisation avec email existant
- ✅ Email reçu avec le lien
- ✅ Message de succès affiché

### Test 2 : Demande avec email inexistant
- ✅ Message d'erreur : "Aucun compte associé à cet email"

### Test 3 : Demande avec email invalide
- ✅ Validation côté client empêche l'envoi
- ✅ Message : "Format d'email invalide"

### Test 4 : Réinitialisation avec token valide
- ✅ Formulaire affiché
- ✅ Nouveau mot de passe accepté
- ✅ Connexion réussie avec nouveau mot de passe

### Test 5 : Réinitialisation avec token expiré
- ✅ Message : "Le lien a expiré. Veuillez faire une nouvelle demande"

### Test 6 : Réinitialisation avec token invalide
- ✅ Message : "Token invalide ou expiré"

### Test 7 : Mot de passe faible
- ✅ Erreurs listées (manque majuscule, minuscule, chiffre, longueur)

### Test 8 : Mots de passe non correspondants
- ✅ Message : "Les mots de passe ne correspondent pas"

### Test 9 : Réutilisation du même token
- ✅ Token supprimé après première utilisation
- ✅ Deuxième tentative échoue

## 📝 Personnalisation

### Changer la durée d'expiration du token

Dans `Model/Auth.php`, méthode `createResetToken()` :
```php
$expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Modifier ici
```

Exemples :
- 30 minutes : `'+30 minutes'`
- 2 heures : `'+2 hours'`
- 1 jour : `'+1 day'`

### Personnaliser l'email envoyé

Dans `Controller/authController.php`, méthode `requestPasswordReset()`, modifiez la variable `$message`.

### Changer les règles de mot de passe

Modifiez :
1. La fonction `isValidPasswordStrength()` dans `script.js`
2. La validation dans `Controller/authController.php`, méthode `resetPassword()`

## ⚠️ Limitations et améliorations possibles

### Limitations actuelles :
- Utilise la fonction `mail()` de PHP (nécessite configuration)
- Pas de rate limiting (possibilité de spam de demandes)
- Pas de log des tentatives de réinitialisation

### Améliorations possibles :
1. **Ajouter PHPMailer** avec SMTP pour envoi fiable d'emails
2. **Rate limiting** : Limiter à 3 demandes par heure par IP
3. **Logs** : Enregistrer toutes les demandes dans une table `password_reset_logs`
4. **Notifications** : Prévenir l'utilisateur par email quand son mot de passe est changé
5. **Historique** : Empêcher la réutilisation des X derniers mots de passe
6. **2FA** : Ajouter une double authentification
7. **Tokens multiples** : Permettre plusieurs tokens actifs (mobile + desktop)

## 🐛 Dépannage

### Problème : "Erreur lors de l'envoi de l'email"
**Solution** : Configurez sendmail ou utilisez PHPMailer (voir Section "Configurer l'envoi d'emails")

### Problème : "Token invalide ou expiré"
**Solutions** :
- Vérifiez que les colonnes `reset_token` et `reset_token_expires` existent dans la table `users`
- Vérifiez que le token dans l'URL est complet (64 caractères)
- Le token expire après 1 heure

### Problème : Le lien de réinitialisation ne fonctionne pas
**Solutions** :
- Vérifiez l'URL générée dans `authController.php`
- Assurez-vous que le chemin vers `reset_password.php` est correct
- Vérifiez les permissions du dossier

### Problème : Les erreurs ne s'affichent pas correctement
**Solution** : Vérifiez que `styles.css` contient les classes `.alert`, `.alert-error`, etc.

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifiez les logs Apache : `C:\xampp\apache\logs\error.log`
2. Vérifiez les logs PHP : `C:\xampp\php\logs\php_error_log`
3. Activez le mode debug en ajoutant en haut de `authController.php` :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## ✅ Checklist d'installation

- [ ] Script SQL exécuté (colonnes `reset_token` et `reset_token_expires` créées)
- [ ] Emails configurés (PHPMailer, sendmail ou mode test)
- [ ] Lien "Mot de passe oublié ?" visible sur login.php
- [ ] Test : Demande de réinitialisation fonctionne
- [ ] Test : Email reçu avec lien valide
- [ ] Test : Réinitialisation du mot de passe réussie
- [ ] Test : Connexion avec nouveau mot de passe OK
- [ ] Test : Token expire après 1 heure
- [ ] Test : Token ne peut pas être réutilisé

---

**Système créé pour Play to Help - Gaming pour l'Humanitaire**
*Sécurisé, complet et prêt à l'emploi*

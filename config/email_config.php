<?php
/**
 * Configuration Email pour PHPMailer
 * 
 * INSTRUCTIONS :
 * 1. Choisissez votre fournisseur de messagerie (Gmail, Outlook, autre)
 * 2. Remplissez les informations de connexion
 * 3. Pour Gmail : utilisez un "Mot de passe d'application" (pas votre mot de passe normal)
 *    - Allez sur https://myaccount.google.com/apppasswords
 *    - Créez un mot de passe d'application
 *    - Utilisez ce mot de passe ici
 */

// Configuration Email
define('SMTP_HOST', 'smtp.gmail.com');          // Gmail: smtp.gmail.com | Outlook: smtp-mail.outlook.com
define('SMTP_PORT', 587);                        // Port TLS: 587 | Port SSL: 465
define('SMTP_SECURE', 'tls');                    // tls ou ssl
define('SMTP_USERNAME', 'playtohelp1@gmail.com'); // ⚠️ VOTRE EMAIL ICI
define('SMTP_PASSWORD', 'ebtisuidbmpddxjb'); // ⚠️ MOT DE PASSE D'APPLICATION ICI
define('SMTP_FROM_EMAIL', 'noreply@playtohelp.com');
define('SMTP_FROM_NAME', 'Play to Help');

/**
 * EXEMPLES DE CONFIGURATION PAR FOURNISSEUR :
 * ebti suid bmpd dxjb

 * === GMAIL ===
 * SMTP_HOST: smtp.gmail.com
 * SMTP_PORT: 587
 * SMTP_SECURE: tls
 * SMTP_USERNAME: votre-email@gmail.com
 * SMTP_PASSWORD: mot-de-passe-app-16-caracteres (créé sur https://myaccount.google.com/apppasswords)
 * 
 * === OUTLOOK / HOTMAIL ===
 * SMTP_HOST: smtp-mail.outlook.com
 * SMTP_PORT: 587
 * SMTP_SECURE: tls
 * SMTP_USERNAME: votre-email@outlook.com
 * SMTP_PASSWORD: votre-mot-de-passe-outlook
 * 
 * === YAHOO ===
 * SMTP_HOST: smtp.mail.yahoo.com
 * SMTP_PORT: 587
 * SMTP_SECURE: tls
 * SMTP_USERNAME: votre-email@yahoo.com
 * SMTP_PASSWORD: mot-de-passe-app-yahoo
 * 
 * === SERVEUR PERSONNALISÉ ===
 * SMTP_HOST: mail.votredomaine.com
 * SMTP_PORT: 587 ou 465
 * SMTP_SECURE: tls ou ssl
 * SMTP_USERNAME: contact@votredomaine.com
 * SMTP_PASSWORD: votre-mot-de-passe
 */

// Mode debug (mettre à false en production)
define('SMTP_DEBUG', false);

/**
 * COMMENT OBTENIR UN MOT DE PASSE D'APPLICATION GMAIL :
 * 
 * 1. Activez la validation en 2 étapes sur votre compte Google
 *    https://myaccount.google.com/security
 * 
 * 2. Créez un mot de passe d'application
 *    https://myaccount.google.com/apppasswords
 *    - Sélectionnez "Application" : Autre (nom personnalisé)
 *    - Nommez-la "Play to Help"
 *    - Cliquez sur "Générer"
 *    - Copiez le mot de passe de 16 caractères
 * 
 * 3. Utilisez ce mot de passe dans SMTP_PASSWORD ci-dessus
 * 
 * 4. IMPORTANT : Ne partagez jamais ce fichier avec vos mots de passe !
 *    Ajoutez-le à .gitignore si vous utilisez Git
 */

/**
 * TEST DE CONFIGURATION :
 * 
 * Pour tester votre configuration email, créez un fichier test_email.php :
 * 
 * <?php
 * require_once 'config/email_config.php';
 * require_once 'View/FrontOffice/vendor/PHPMailer-master/src/PHPMailer.php';
 * require_once 'View/FrontOffice/vendor/PHPMailer-master/src/SMTP.php';
 * require_once 'View/FrontOffice/vendor/PHPMailer-master/src/Exception.php';
 * 
 * $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
 * try {
 *     $mail->isSMTP();
 *     $mail->Host = SMTP_HOST;
 *     $mail->SMTPAuth = true;
 *     $mail->Username = SMTP_USERNAME;
 *     $mail->Password = SMTP_PASSWORD;
 *     $mail->SMTPSecure = SMTP_SECURE;
 *     $mail->Port = SMTP_PORT;
 *     $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
 *     $mail->addAddress('votre-email-test@example.com');
 *     $mail->Subject = 'Test PHPMailer';
 *     $mail->Body = 'Si vous recevez cet email, la configuration fonctionne !';
 *     $mail->send();
 *     echo 'Email envoyé avec succès !';
 * } catch (Exception $e) {
 *     echo 'Erreur : ' . $mail->ErrorInfo;
 * }
 */
?>

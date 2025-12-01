<?php
/**
 * Script de test pour vérifier la configuration PHPMailer
 * 
 * INSTRUCTIONS :
 * 1. Configurez d'abord config/email_config.php avec vos identifiants
 * 2. Modifiez l'email de test ci-dessous (ligne 18)
 * 3. Accédez à ce fichier dans votre navigateur : http://localhost/play%20to%20help%20mvc%20f%20-%20d1/test_email.php
 * 4. Vérifiez votre boîte de réception
 */

// Charger la configuration
require_once __DIR__ . '/config/email_config.php';
require_once __DIR__ . '/View/FrontOffice/vendor/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/View/FrontOffice/vendor/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/View/FrontOffice/vendor/PHPMailer-master/src/Exception.php';

// ⚠️ MODIFIEZ CET EMAIL POUR VOTRE TEST
$emailTest = 'votre-email-test@example.com';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test PHPMailer - Play to Help</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #e75e8d;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #bee5eb;
            margin: 20px 0;
        }
        .config {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #e75e8d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #c74375;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎮 Test Configuration PHPMailer</h1>
        
        <?php
        // Vérifier la configuration
        if (SMTP_USERNAME === 'votre-email@gmail.com' || SMTP_PASSWORD === 'votre-mot-de-passe-app') {
            echo '<div class="error">';
            echo '<strong>⚠️ Configuration incomplète !</strong><br>';
            echo 'Veuillez d\'abord configurer vos identifiants dans <code>config/email_config.php</code>';
            echo '</div>';
            echo '<div class="info">';
            echo '<strong>Instructions :</strong><br>';
            echo '1. Ouvrez le fichier <code>config/email_config.php</code><br>';
            echo '2. Modifiez SMTP_USERNAME avec votre email<br>';
            echo '3. Modifiez SMTP_PASSWORD avec votre mot de passe d\'application<br>';
            echo '4. Rechargez cette page';
            echo '</div>';
        } else {
            echo '<div class="config">';
            echo '<strong>Configuration actuelle :</strong><br>';
            echo 'SMTP Host: ' . SMTP_HOST . '<br>';
            echo 'SMTP Port: ' . SMTP_PORT . '<br>';
            echo 'SMTP Secure: ' . SMTP_SECURE . '<br>';
            echo 'SMTP Username: ' . SMTP_USERNAME . '<br>';
            echo 'SMTP Password: ' . str_repeat('*', strlen(SMTP_PASSWORD)) . '<br>';
            echo 'From Email: ' . SMTP_FROM_EMAIL . '<br>';
            echo 'From Name: ' . SMTP_FROM_NAME . '<br>';
            echo '</div>';

            // Tester l'envoi d'email si le bouton est cliqué
            if (isset($_POST['test_email'])) {
                try {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    
                    // Configuration SMTP
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = SMTP_SECURE;
                    $mail->Port = SMTP_PORT;
                    $mail->CharSet = 'UTF-8';
                    
                    // Mode debug
                    $mail->SMTPDebug = 0;
                    
                    // Expéditeur et destinataire
                    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                    $mail->addAddress($emailTest);
                    
                    // Contenu
                    $mail->isHTML(true);
                    $mail->Subject = 'Test PHPMailer - Play to Help';
                    $mail->Body = '
                        <html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                                .header { background: linear-gradient(135deg, #e75e8d 0%, #c74375 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                                .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
                                .success-icon { font-size: 48px; text-align: center; margin: 20px 0; }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="header">
                                    <h2>🎮 Play to Help</h2>
                                </div>
                                <div class="content">
                                    <div class="success-icon">✅</div>
                                    <h3>Test PHPMailer réussi !</h3>
                                    <p>Si vous recevez cet email, votre configuration PHPMailer est correcte et fonctionnelle.</p>
                                    <p><strong>Configuration utilisée :</strong></p>
                                    <ul>
                                        <li>Serveur SMTP : ' . SMTP_HOST . '</li>
                                        <li>Port : ' . SMTP_PORT . '</li>
                                        <li>Sécurité : ' . SMTP_SECURE . '</li>
                                        <li>Email : ' . SMTP_USERNAME . '</li>
                                    </ul>
                                    <p>Vous pouvez maintenant utiliser le système de réinitialisation de mot de passe.</p>
                                </div>
                                <div style="text-align: center; padding: 20px; font-size: 12px; color: #777;">
                                    <p>© 2025 Play to Help - Gaming pour l\'Humanitaire</p>
                                </div>
                            </div>
                        </body>
                        </html>
                    ';
                    $mail->AltBody = 'Test PHPMailer réussi ! Votre configuration fonctionne correctement.';
                    
                    $mail->send();
                    
                    echo '<div class="success">';
                    echo '<strong>✅ Email envoyé avec succès !</strong><br>';
                    echo 'Un email de test a été envoyé à : <strong>' . htmlspecialchars($emailTest) . '</strong><br>';
                    echo 'Vérifiez votre boîte de réception (et vos spams si vous ne le voyez pas).<br><br>';
                    echo '<strong>Configuration validée ✓</strong><br>';
                    echo 'Votre système de réinitialisation de mot de passe est opérationnel !';
                    echo '</div>';
                    
                } catch (\PHPMailer\PHPMailer\Exception $e) {
                    echo '<div class="error">';
                    echo '<strong>❌ Erreur lors de l\'envoi !</strong><br>';
                    echo 'Message d\'erreur : ' . htmlspecialchars($mail->ErrorInfo) . '<br><br>';
                    echo '<strong>Solutions possibles :</strong><br>';
                    echo '1. Vérifiez vos identifiants dans config/email_config.php<br>';
                    echo '2. Pour Gmail : utilisez un "Mot de passe d\'application"<br>';
                    echo '   → https://myaccount.google.com/apppasswords<br>';
                    echo '3. Vérifiez que la validation en 2 étapes est activée (Gmail)<br>';
                    echo '4. Vérifiez votre connexion Internet<br>';
                    echo '5. Essayez avec un autre serveur SMTP (Outlook, Yahoo, etc.)';
                    echo '</div>';
                }
            } else {
                // Afficher le formulaire de test
                echo '<div class="info">';
                echo '<strong>Prêt à tester !</strong><br>';
                echo 'Cliquez sur le bouton ci-dessous pour envoyer un email de test à : <br>';
                echo '<strong>' . htmlspecialchars($emailTest) . '</strong><br><br>';
                echo '<small>💡 Vous pouvez modifier l\'email de test en éditant ce fichier (ligne 18)</small>';
                echo '</div>';
                
                echo '<form method="POST">';
                echo '<button type="submit" name="test_email" class="btn">📧 Envoyer un email de test</button>';
                echo '</form>';
            }
        }
        ?>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
        
        <h3>📚 Documentation</h3>
        <p><strong>Pour Gmail :</strong></p>
        <ol>
            <li>Activez la validation en 2 étapes : <a href="https://myaccount.google.com/security" target="_blank">https://myaccount.google.com/security</a></li>
            <li>Créez un mot de passe d'application : <a href="https://myaccount.google.com/apppasswords" target="_blank">https://myaccount.google.com/apppasswords</a></li>
            <li>Utilisez ce mot de passe dans config/email_config.php</li>
        </ol>
        
        <p><strong>Fichiers concernés :</strong></p>
        <ul>
            <li><code>config/email_config.php</code> - Configuration des paramètres email</li>
            <li><code>Controller/authController.php</code> - Utilisation de PHPMailer</li>
            <li><code>test_email.php</code> - Ce fichier de test</li>
        </ul>
    </div>
</body>
</html>

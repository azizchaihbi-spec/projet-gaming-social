<?php
/**
 * Email template for password reset
 * Variables available: $resetLink
 */
?>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #e75e8d 0%, #c74375 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .button { display: inline-block; padding: 12px 30px; background: #e75e8d; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>🎮 Play to Help</h2>
        </div>
        <div class='content'>
            <h3>Réinitialisation de votre mot de passe</h3>
            <p>Vous avez demandé à réinitialiser votre mot de passe.</p>
            <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
            <p style='text-align: center;'>
                <a href='<?php echo $resetLink; ?>' class='button'>Réinitialiser mon mot de passe</a>
            </p>
            <p><strong>Ce lien est valide pendant 1 heure.</strong></p>
            <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
            <p style='font-size: 12px; color: #666; margin-top: 20px;'>
                Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                <a href='<?php echo $resetLink; ?>'><?php echo $resetLink; ?></a>
            </p>
        </div>
        <div class='footer'>
            <p>© 2025 Play to Help - Gaming pour l'Humanitaire</p>
        </div>
    </div>
</body>
</html>

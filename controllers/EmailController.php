<?php
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController {
    
    public static function sendDonationReceipt($donData, $pdfPath = null) {
        $config = require __DIR__ . '/../config/email_configsinda.php';
        
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = $config['smtp_auth'];
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->Port = $config['smtp_port'];
            $mail->CharSet = $config['charset'];
            
            // Expéditeur et destinataire
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($donData['email'], $donData['nom']);
            
            // Attacher le PDF si disponible
            if ($pdfPath && file_exists($pdfPath)) {
                $mail->addAttachment($pdfPath, 'Recu_Don_' . $donData['id'] . '.pdf');
            }
            
            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Reçu de votre don - Play to Help';
            
            $mail->Body = self::getEmailTemplate($donData);
            
            $mail->send();
            return ['success' => true, 'message' => 'Email envoyé avec succès'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => "Erreur d'envoi: {$mail->ErrorInfo}"];
        }
    }
    
    public static function sendDonationReceiptToPlayToHelp($donData) {
        $config = require __DIR__ . '/../config/email_configsinda.php';
        
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = $config['smtp_host'];
            $mail->SMTPAuth = $config['smtp_auth'];
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->Port = $config['smtp_port'];
            $mail->CharSet = $config['charset'];
            
            // Expéditeur et destinataire
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress('playtohelp1@gmail.com', 'Play to Help Admin');
            
            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Nouveau don reçu - ' . $donData['nom'];
            
            $mail->Body = self::getAdminEmailTemplate($donData);
            
            $mail->send();
            return ['success' => true, 'message' => 'Email admin envoyé'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => "Erreur: {$mail->ErrorInfo}"];
        }
    }
    
    private static function getEmailTemplate($donData) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .amount { font-size: 32px; font-weight: bold; color: #667eea; text-align: center; margin: 20px 0; }
                .info-box { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎮 Merci pour votre don !</h1>
                    <p>Play to Help</p>
                </div>
                <div class="content">
                    <p>Bonjour <strong>' . htmlspecialchars($donData['nom']) . '</strong>,</p>
                    
                    <p>Nous vous remercions chaleureusement pour votre généreuse contribution !</p>
                    
                    <div class="amount">' . number_format($donData['montant'], 2) . ' €</div>
                    
                    <div class="info-box">
                        <h3>📋 Détails de votre don</h3>
                        <p><strong>Montant :</strong> ' . number_format($donData['montant'], 2) . ' €</p>
                        <p><strong>Date :</strong> ' . date('d/m/Y à H:i', strtotime($donData['date_don'])) . '</p>
                        <p><strong>Association :</strong> ' . htmlspecialchars($donData['association_nom']) . '</p>
                        <p><strong>Email :</strong> ' . htmlspecialchars($donData['email']) . '</p>
                    </div>
                    
                    <p style="text-align: center;">
                        <a href="' . BASE_URL . '/views/frontoffice/download_pdf.php?id=' . $donData['id'] . '" class="button">
                            📄 Télécharger le reçu PDF
                        </a>
                    </p>
                    
                    <p>Votre don contribue directement à soutenir les actions de l\'association et à faire une différence positive.</p>
                    
                    <div class="footer">
                        <p>Cet email a été envoyé automatiquement par Play to Help</p>
                        <p>© 2024 Play to Help - Tous droits réservés</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
    }
    
    private static function getAdminEmailTemplate($donData) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { background: #f9f9f9; padding: 20px; }
                .info-box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>💰 Nouveau don reçu !</h2>
                </div>
                <div class="content">
                    <div class="info-box">
                        <h3>Informations du donateur</h3>
                        <p><strong>Nom :</strong> ' . htmlspecialchars($donData['nom']) . '</p>
                        <p><strong>Email :</strong> ' . htmlspecialchars($donData['email']) . '</p>
                        <p><strong>Montant :</strong> ' . number_format($donData['montant'], 2) . ' €</p>
                        <p><strong>Date :</strong> ' . date('d/m/Y à H:i', strtotime($donData['date_don'])) . '</p>
                        <p><strong>Association :</strong> ' . htmlspecialchars($donData['association_nom']) . '</p>
                    </div>
                    
                    <p style="text-align: center; margin-top: 20px;">
                        <a href="' . BASE_URL . '/views/backoffice/indexsinda.php" style="display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">
                            Voir dans le dashboard
                        </a>
                    </p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
}

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Model/Auth.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new Auth();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true) ?: [];

            $user = new User();
            $user->setFirstName(trim($data['firstName'] ?? ''));
            $user->setLastName(trim($data['lastName'] ?? ''));
            $user->setUsername(trim($data['username'] ?? ''));
            $user->setEmail(trim($data['email'] ?? ''));
            $user->setBirthdate(trim($data['birthdate'] ?? ''));
            $user->setGender(trim($data['gender'] ?? ''));
            $user->setCountry(trim($data['country'] ?? ''));
            $user->setCity(trim($data['city'] ?? ''));
            $user->setRole(trim($data['role'] ?? ''));
            $user->setStreamLink(trim($data['streamLink'] ?? ''));
            $user->setStreamDescription(trim($data['streamDescription'] ?? ''));
            $user->setStreamPlatform(trim($data['streamPlatform'] ?? ''));
            $user->setPassword((string)($data['password'] ?? ''));
            $user->setProfileImage('assets/images/profile.jpg');

            $result = $this->authModel->register($user);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $email = $data['email'];
            $password = $data['password'];

            $result = $this->authModel->login($email, $password);
            
            if ($result['success']) {
                session_start();
                $_SESSION['user'] = $result['user'];
            }
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            if (!isset($_SESSION['user'])) {
                echo json_encode(['success' => false, 'message' => 'Non authentifié']);
                return;
            }

            $userId = $_SESSION['user']['id'];
            $profileImage = $_POST['profile_image'];

            $result = $this->authModel->updateProfile($userId, $profileImage);
            
            if ($result['success']) {
                $_SESSION['user'] = $result['user'];
            }
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }

    public function updateStreamerInfo() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            if (!isset($_SESSION['user'])) {
                echo json_encode(['success' => false, 'message' => 'Non authentifié']);
                return;
            }

            $userId = $_SESSION['user']['id'];
            $streamLink = $_POST['stream_link'];
            $streamDescription = $_POST['stream_description'];
            $streamPlatform = $_POST['stream_platform'];

            $result = $this->authModel->updateStreamerInfo($userId, $streamLink, $streamDescription, $streamPlatform);
            
            if ($result['success']) {
                $_SESSION['user'] = $result['user'];
            }
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../View/FrontOffice/login.php');
        exit();
    }

    public function checkSession() {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
        exit();
    }

    public function validateAdminAccess() {
        if (!isset($_SESSION['user'])) {
            $_SESSION['errors'] = ['Accès refusé : vous n\'êtes pas connecté'];
            return false;
        }
        if ($_SESSION['user']['role'] !== 'admin') {
            $_SESSION['errors'] = ['Accès refusé : droits admin requis'];
            return false;
        }
        return true;
    }

    public function requestPasswordReset() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true) ?: [];

            $email = trim($data['email'] ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Email invalide']);
                exit();
            }

            $result = $this->authModel->createResetToken($email);

            if ($result['success']) {
                // Charger PHPMailer
                require_once __DIR__ . '/../View/FrontOffice/vendor/PHPMailer-master/src/PHPMailer.php';
                require_once __DIR__ . '/../View/FrontOffice/vendor/PHPMailer-master/src/SMTP.php';
                require_once __DIR__ . '/../View/FrontOffice/vendor/PHPMailer-master/src/Exception.php';
                require_once __DIR__ . '/../config/email_config.php';

                $token = $result['token'];
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . "/View/FrontOffice/reset_password.php?token=" . $token;
                
                try {
                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    
                    // Configuration SMTP depuis email_config.php
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = SMTP_SECURE;
                    $mail->Port = SMTP_PORT;
                    $mail->CharSet = 'UTF-8';
                    
                    // Debug (désactiver en production)
                    if (defined('SMTP_DEBUG') && SMTP_DEBUG) {
                        $mail->SMTPDebug = 2;
                        $mail->Debugoutput = 'error_log';
                    }

                    // Expéditeur et destinataire
                    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
                    $mail->addAddress($email);

                    // Contenu de l'email
                    $mail->isHTML(true);
                    $mail->Subject = 'Play to Help - Réinitialisation de mot de passe';
                    $mail->Body = "
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
                                        <a href='" . $resetLink . "' class='button'>Réinitialiser mon mot de passe</a>
                                    </p>
                                    <p><strong>Ce lien est valide pendant 1 heure.</strong></p>
                                    <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
                                    <p style='font-size: 12px; color: #666; margin-top: 20px;'>
                                        Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                                        <a href='" . $resetLink . "'>" . $resetLink . "</a>
                                    </p>
                                </div>
                                <div class='footer'>
                                    <p>© 2025 Play to Help - Gaming pour l'Humanitaire</p>
                                </div>
                            </div>
                        </body>
                        </html>
                    ";
                    $mail->AltBody = "Réinitialisez votre mot de passe en cliquant sur ce lien : " . $resetLink . " (valide 1 heure)";

                    $mail->send();
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Un email de réinitialisation a été envoyé à votre adresse'
                    ]);
                } catch (\PHPMailer\PHPMailer\Exception $e) {
                    error_log('Erreur PHPMailer: ' . $mail->ErrorInfo);
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Erreur lors de l\'envoi de l\'email. Réessayez plus tard'
                    ]);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode($result);
            }
            exit();
        }
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true) ?: [];

            $token = trim($data['token'] ?? '');
            $newPassword = (string)($data['newPassword'] ?? '');
            $confirmPassword = (string)($data['confirmPassword'] ?? '');

            // Validation côté serveur
            $errors = [];

            if (empty($token)) {
                $errors[] = 'Token manquant';
            }

            if (empty($newPassword)) {
                $errors[] = 'Nouveau mot de passe requis';
            } elseif (!(strlen($newPassword) >= 6 && preg_match('/[a-z]/', $newPassword) && preg_match('/[A-Z]/', $newPassword) && preg_match('/[0-9]/', $newPassword))) {
                $errors[] = 'Mot de passe faible (min 6, 1 maj, 1 min, 1 chiffre)';
            }

            if (empty($confirmPassword)) {
                $errors[] = 'Confirmation requise';
            } elseif ($newPassword !== $confirmPassword) {
                $errors[] = 'Les mots de passe ne correspondent pas';
            }

            if (!empty($errors)) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => implode(' | ', $errors)]);
                exit();
            }

            $result = $this->authModel->resetPasswordByToken($token, $newPassword);
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }
}

// Gestion des actions
if (isset($_GET['action'])) {
    $controller = new AuthController();
    switch ($_GET['action']) {
        case 'register':
            $controller->register();
            break;
        case 'login':
            $controller->login();
            break;
        case 'checkSession':
            $controller->checkSession();
            break;
        case 'updateProfile':
            $controller->updateProfile();
            break;
        case 'updateStreamerInfo':
            $controller->updateStreamerInfo();
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'requestPasswordReset':
            $controller->requestPasswordReset();
            break;
        case 'resetPassword':
            $controller->resetPassword();
            break;
    }
}
?>
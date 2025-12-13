<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/recaptcha.php';
require_once __DIR__ . '/../models/Auth.php';

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

            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            $recaptchaToken = $data['recaptchaToken'] ?? null;

            // reCAPTCHA v2 verification (if enabled)
            if (defined('RECAPTCHA_ENABLED') && RECAPTCHA_ENABLED) {
                header('Content-Type: application/json');
                if (!$recaptchaToken) {
                    echo json_encode(['success' => false, 'message' => 'Veuillez cocher "Je ne suis pas un robot".']);
                    exit();
                }
                $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
                $params = http_build_query([
                    'secret' => RECAPTCHA_SECRET_KEY,
                    'response' => $recaptchaToken,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
                ]);
                $context = stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                        'content' => $params,
                        'timeout' => 5
                    ]
                ]);
                $verifyResponse = @file_get_contents($verifyUrl, false, $context);
                $verifyData = $verifyResponse ? json_decode($verifyResponse, true) : null;
                if (!$verifyData || empty($verifyData['success'])) {
                    echo json_encode(['success' => false, 'message' => 'Échec de la vérification reCAPTCHA. Veuillez réessayer.']);
                    exit();
                }
            }

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
            $profileImage = null;

            // Vérifier si un fichier est uploadé
            if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['avatar_file'];
                
                // Validations
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($file['type'], $allowedMimes)) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Format d\'image non supporté (JPEG, PNG, GIF, WebP)']);
                    exit();
                }

                // Taille max 5MB
                if ($file['size'] > 5 * 1024 * 1024) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'La taille de l\'image ne doit pas dépasser 5MB']);
                    exit();
                }

                // Créer le dossier s'il n'existe pas
                $uploadDir = __DIR__ . '/../views/frontoffice/assets/images/avatars/uploaded/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Générer un nom de fichier unique
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                $uploadPath = $uploadDir . $filename;

                // Déplacer le fichier
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Chemin relatif pour la base de données
                    $profileImage = 'assets/images/avatars/uploaded/' . $filename;

                    // Supprimer l'ancien avatar uploadé s'il existe
                    if (!empty($_SESSION['user']['profile_image']) && strpos($_SESSION['user']['profile_image'], 'uploaded/') !== false) {
                        $oldPath = __DIR__ . '/../views/frontoffice/' . $_SESSION['user']['profile_image'];
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload du fichier']);
                    exit();
                }
            } elseif (isset($_POST['profile_image'])) {
                // Avatar prédéfini
                $profileImage = $_POST['profile_image'];
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Aucun avatar fourni']);
                exit();
            }

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
        header('Location: ../views/frontoffice/login.php');
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
                require_once __DIR__ . '/../views/frontoffice/vendor/PHPMailer-master/src/PHPMailer.php';
                require_once __DIR__ . '/../views/frontoffice/vendor/PHPMailer-master/src/SMTP.php';
                require_once __DIR__ . '/../views/frontoffice/vendor/PHPMailer-master/src/Exception.php';
                require_once __DIR__ . '/../config/email_config.php';

                $token = $result['token'];
                $resetLink = "http://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . "/views/frontoffice/reset_password.php?token=" . $token;
                
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
                    ob_start();
                    include __DIR__ . '/../views/emailtemplates/resetPasswordEmail.php';
                    $mail->Body = ob_get_clean();
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
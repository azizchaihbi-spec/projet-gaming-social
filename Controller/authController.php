<?php
require_once __DIR__ . '../../config/config.php';
require_once __DIR__ . '../../Model/Auth.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new Auth();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // Validation des données
            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                return;
            }

            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setBirthdate($data['birthdate']);
            $user->setGender($data['gender']);
            $user->setCountry($data['country']);
            $user->setCity($data['city']);
            $user->setRole($data['role']);
            $user->setStreamLink($data['streamLink'] ?? '');
            $user->setStreamDescription($data['streamDescription'] ?? '');
            $user->setStreamPlatform($data['streamPlatform'] ?? '');
            $user->setPassword($data['password']);
            $user->setProfileImage('assets/images/avatars/avatar1.png'); // Valeur par défaut

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

            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                return;
            }

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

            // Récupérer les données JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data || !isset($data['profile_image'])) {
                echo json_encode(['success' => false, 'message' => 'Données manquantes']);
                return;
            }

            $userId = $_SESSION['user']['id'];
            $profileImage = $data['profile_image'];

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

            // Récupérer les données JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                echo json_encode(['success' => false, 'message' => 'Données invalides']);
                return;
            }

            $userId = $_SESSION['user']['id'];
            $streamLink = $data['stream_link'] ?? '';
            $streamDescription = $data['stream_description'] ?? '';
            $streamPlatform = $data['stream_platform'] ?? '';

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
        header('Location: ../login.php');
        exit();
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
        case 'updateProfile':
            $controller->updateProfile();
            break;
        case 'updateStreamerInfo':
            $controller->updateStreamerInfo();
            break;
        case 'logout':
            $controller->logout();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Aucune action spécifiée']);
}
?>
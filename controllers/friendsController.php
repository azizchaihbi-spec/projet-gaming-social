<?php
require_once __DIR__ . '/../models/Friends.php';

class FriendsController {
    private $friendsModel;

    public function __construct() {
        $this->friendsModel = new Friends();
    }

    /**
     * Envoyer une demande d'ami
     */
    public function sendRequest() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $friendId = $_POST['friend_id'] ?? null;

        if (!$friendId) {
            echo json_encode(['success' => false, 'message' => 'ID ami manquant']);
            exit();
        }

        $result = $this->friendsModel->sendFriendRequest($userId, $friendId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Accepter une demande d'ami
     */
    public function acceptRequest() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $friendId = $_POST['friend_id'] ?? null;

        if (!$friendId) {
            echo json_encode(['success' => false, 'message' => 'ID ami manquant']);
            exit();
        }

        $result = $this->friendsModel->acceptFriendRequest($userId, $friendId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Refuser une demande d'ami
     */
    public function rejectRequest() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $friendId = $_POST['friend_id'] ?? null;

        if (!$friendId) {
            echo json_encode(['success' => false, 'message' => 'ID ami manquant']);
            exit();
        }

        $result = $this->friendsModel->rejectFriendRequest($userId, $friendId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Supprimer un ami
     */
    public function removeFriend() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $friendId = $_POST['friend_id'] ?? null;

        if (!$friendId) {
            echo json_encode(['success' => false, 'message' => 'ID ami manquant']);
            exit();
        }

        $result = $this->friendsModel->removeFriend($userId, $friendId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Obtenir la liste des amis
     */
    public function getFriends() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $result = $this->friendsModel->getFriends($userId, true);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Obtenir les amis en ligne
     */
    public function getOnlineFriends() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $result = $this->friendsModel->getOnlineFriends($userId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Obtenir les demandes d'ami en attente
     */
    public function getPendingRequests() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $result = $this->friendsModel->getPendingRequests($userId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Rechercher des utilisateurs
     */
    public function searchUsers() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $query = $_GET['q'] ?? '';

        if (strlen($query) < 2) {
            echo json_encode(['success' => false, 'message' => 'Recherche trop courte (min 2 caractères)']);
            exit();
        }

        $result = $this->friendsModel->searchUsers($userId, $query);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Mettre à jour le statut
     */
    public function updateStatus() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $status = $_POST['status'] ?? 'online';
        $statusMessage = $_POST['status_message'] ?? null;

        $allowedStatuses = ['online', 'offline', 'away', 'busy'];
        if (!in_array($status, $allowedStatuses)) {
            echo json_encode(['success' => false, 'message' => 'Statut invalide']);
            exit();
        }

        $result = $this->friendsModel->updateUserStatus($userId, $status, $statusMessage);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Obtenir les notifications
     */
    public function getNotifications() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $result = $this->friendsModel->getUnreadNotifications($userId);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }

    /**
     * Marquer les notifications comme lues
     */
    public function markNotificationsRead() {
        session_start();
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'message' => 'Non authentifié']);
            exit();
        }

        $userId = $_SESSION['user']['id'];
        $notificationIds = $_POST['notification_ids'] ?? null;

        $result = $this->friendsModel->markNotificationsRead($userId, $notificationIds);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    }
}

// Gestion des actions
if (isset($_GET['action'])) {
    $controller = new FriendsController();
    switch ($_GET['action']) {
        case 'sendRequest':
            $controller->sendRequest();
            break;
        case 'acceptRequest':
            $controller->acceptRequest();
            break;
        case 'rejectRequest':
            $controller->rejectRequest();
            break;
        case 'removeFriend':
            $controller->removeFriend();
            break;
        case 'getFriends':
            $controller->getFriends();
            break;
        case 'getOnlineFriends':
            $controller->getOnlineFriends();
            break;
        case 'getPendingRequests':
            $controller->getPendingRequests();
            break;
        case 'searchUsers':
            $controller->searchUsers();
            break;
        case 'updateStatus':
            $controller->updateStatus();
            break;
        case 'getNotifications':
            $controller->getNotifications();
            break;
        case 'markNotificationsRead':
            $controller->markNotificationsRead();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Action inconnue']);
    }
}

<?php
require_once __DIR__ . '/../config/config.php';

class Friends {
    private $conn;

    public function __construct() {
        $this->conn = config::getConnexion();
    }

    /**
     * Envoyer une demande d'ami
     */
    public function sendFriendRequest($userId, $friendId) {
        try {
            // Vérifier que l'utilisateur n'envoie pas une demande à lui-même
            if ($userId == $friendId) {
                return ['success' => false, 'message' => 'Vous ne pouvez pas vous ajouter vous-même'];
            }

            // Vérifier si une relation existe déjà
            $stmt = $this->conn->prepare("
                SELECT * FROM friendships 
                WHERE (user_id = ? AND friend_id = ?) 
                   OR (user_id = ? AND friend_id = ?)
            ");
            $stmt->execute([$userId, $friendId, $friendId, $userId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                if ($existing['status'] === 'pending') {
                    return ['success' => false, 'message' => 'Demande déjà envoyée'];
                } elseif ($existing['status'] === 'accepted') {
                    return ['success' => false, 'message' => 'Vous êtes déjà amis'];
                } elseif ($existing['status'] === 'blocked') {
                    return ['success' => false, 'message' => 'Impossible d\'envoyer cette demande'];
                }
            }

            // Créer la demande d'ami
            $stmt = $this->conn->prepare("
                INSERT INTO friendships (user_id, friend_id, status) 
                VALUES (?, ?, 'pending')
            ");
            $stmt->execute([$userId, $friendId]);

            // Créer une notification
            $this->createNotification($friendId, $userId, 'friend_request');

            return ['success' => true, 'message' => 'Demande d\'ami envoyée'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Accepter une demande d'ami
     */
    public function acceptFriendRequest($userId, $friendId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE friendships 
                SET status = 'accepted', updated_at = NOW()
                WHERE friend_id = ? AND user_id = ? AND status = 'pending'
            ");
            $stmt->execute([$userId, $friendId]);

            if ($stmt->rowCount() > 0) {
                // Créer notification d'acceptation
                $this->createNotification($friendId, $userId, 'friend_accepted');
                return ['success' => true, 'message' => 'Demande acceptée'];
            }

            return ['success' => false, 'message' => 'Demande introuvable'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Refuser une demande d'ami
     */
    public function rejectFriendRequest($userId, $friendId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE friendships 
                SET status = 'rejected', updated_at = NOW()
                WHERE friend_id = ? AND user_id = ? AND status = 'pending'
            ");
            $stmt->execute([$userId, $friendId]);

            return ['success' => true, 'message' => 'Demande refusée'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Supprimer un ami
     */
    public function removeFriend($userId, $friendId) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM friendships 
                WHERE (user_id = ? AND friend_id = ?) 
                   OR (user_id = ? AND friend_id = ?)
            ");
            $stmt->execute([$userId, $friendId, $friendId, $userId]);

            return ['success' => true, 'message' => 'Ami supprimé'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Obtenir la liste des amis
     */
    public function getFriends($userId, $includeStatus = true) {
        try {
            $query = "
                SELECT 
                    u.id,
                    u.username,
                    u.first_name,
                    u.last_name,
                    u.profile_image,
                    u.role,
                    f.created_at as friends_since
            ";
            
            if ($includeStatus) {
                $query .= ", us.status, us.last_activity, us.status_message";
            }
            
            $query .= "
                FROM friendships f
                JOIN users u ON (
                    (f.user_id = ? AND f.friend_id = u.id) OR 
                    (f.friend_id = ? AND f.user_id = u.id)
                )
            ";
            
            if ($includeStatus) {
                $query .= " LEFT JOIN user_status us ON us.user_id = u.id";
            }
            
            $query .= " WHERE f.status = 'accepted' AND u.id != ? ORDER BY u.username";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId, $userId, $userId]);
            $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['success' => true, 'friends' => $friends];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Obtenir les amis en ligne
     */
    public function getOnlineFriends($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    u.id,
                    u.username,
                    u.first_name,
                    u.last_name,
                    u.profile_image,
                    us.status,
                    us.status_message,
                    us.last_activity
                FROM friendships f
                JOIN users u ON (
                    (f.user_id = ? AND f.friend_id = u.id) OR 
                    (f.friend_id = ? AND f.user_id = u.id)
                )
                JOIN user_status us ON us.user_id = u.id
                WHERE f.status = 'accepted' 
                  AND u.id != ?
                  AND us.status IN ('online', 'away', 'busy')
                  AND us.last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                ORDER BY us.last_activity DESC
            ");
            $stmt->execute([$userId, $userId, $userId]);
            $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['success' => true, 'friends' => $friends, 'count' => count($friends)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Obtenir les demandes d'ami en attente (reçues)
     */
    public function getPendingRequests($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    f.id as request_id,
                    u.id,
                    u.username,
                    u.first_name,
                    u.last_name,
                    u.profile_image,
                    u.role,
                    f.created_at
                FROM friendships f
                JOIN users u ON f.user_id = u.id
                WHERE f.friend_id = ? AND f.status = 'pending'
                ORDER BY f.created_at DESC
            ");
            $stmt->execute([$userId]);
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['success' => true, 'requests' => $requests, 'count' => count($requests)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Rechercher des utilisateurs
     */
    public function searchUsers($userId, $query) {
        try {
            $searchTerm = '%' . $query . '%';
            $stmt = $this->conn->prepare("
                SELECT 
                    u.id,
                    u.username,
                    u.first_name,
                    u.last_name,
                    u.profile_image,
                    u.role,
                    CASE
                        WHEN f.status = 'accepted' THEN 'friend'
                        WHEN f.status = 'pending' AND f.user_id = ? THEN 'request_sent'
                        WHEN f.status = 'pending' AND f.friend_id = ? THEN 'request_received'
                        ELSE 'none'
                    END as friendship_status
                FROM users u
                LEFT JOIN friendships f ON (
                    (f.user_id = ? AND f.friend_id = u.id) OR 
                    (f.friend_id = ? AND f.user_id = u.id)
                )
                WHERE u.id != ?
                  AND (u.username LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)
                  AND u.is_banned = 0
                LIMIT 20
            ");
            $stmt->execute([
                $userId, $userId, $userId, $userId, $userId, 
                $searchTerm, $searchTerm, $searchTerm
            ]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['success' => true, 'users' => $users];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Mettre à jour le statut de l'utilisateur
     */
    public function updateUserStatus($userId, $status, $statusMessage = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO user_status (user_id, status, status_message, last_activity)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE 
                    status = VALUES(status),
                    status_message = VALUES(status_message),
                    last_activity = NOW()
            ");
            $stmt->execute([$userId, $status, $statusMessage]);

            // Notifier les amis si passage en ligne
            if ($status === 'online') {
                $this->notifyFriendsOnline($userId);
            }

            return ['success' => true, 'message' => 'Statut mis à jour'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Créer une notification
     */
    private function createNotification($userId, $fromUserId, $type) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO friend_notifications (user_id, from_user_id, type)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, $fromUserId, $type]);
        } catch (PDOException $e) {
            // Log l'erreur mais ne pas bloquer le flux
            error_log("Erreur notification: " . $e->getMessage());
        }
    }

    /**
     * Notifier les amis qu'on est en ligne
     */
    private function notifyFriendsOnline($userId) {
        try {
            // Récupérer les amis
            $stmt = $this->conn->prepare("
                SELECT DISTINCT 
                    CASE 
                        WHEN f.user_id = ? THEN f.friend_id 
                        ELSE f.user_id 
                    END as friend_id
                FROM friendships f
                WHERE (f.user_id = ? OR f.friend_id = ?) 
                  AND f.status = 'accepted'
            ");
            $stmt->execute([$userId, $userId, $userId]);
            $friends = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // Créer notifications
            foreach ($friends as $friendId) {
                $this->createNotification($friendId, $userId, 'friend_online');
            }
        } catch (PDOException $e) {
            error_log("Erreur notification amis: " . $e->getMessage());
        }
    }

    /**
     * Obtenir les notifications non lues
     */
    public function getUnreadNotifications($userId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    n.id,
                    n.type,
                    n.created_at,
                    u.id as from_user_id,
                    u.username,
                    u.first_name,
                    u.last_name,
                    u.profile_image
                FROM friend_notifications n
                JOIN users u ON n.from_user_id = u.id
                WHERE n.user_id = ? AND n.is_read = 0
                ORDER BY n.created_at DESC
                LIMIT 50
            ");
            $stmt->execute([$userId]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['success' => true, 'notifications' => $notifications, 'count' => count($notifications)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Marquer les notifications comme lues
     */
    public function markNotificationsRead($userId, $notificationIds = null) {
        try {
            if ($notificationIds) {
                $placeholders = str_repeat('?,', count($notificationIds) - 1) . '?';
                $stmt = $this->conn->prepare("
                    UPDATE friend_notifications 
                    SET is_read = 1 
                    WHERE user_id = ? AND id IN ($placeholders)
                ");
                $stmt->execute(array_merge([$userId], $notificationIds));
            } else {
                // Marquer toutes comme lues
                $stmt = $this->conn->prepare("
                    UPDATE friend_notifications 
                    SET is_read = 1 
                    WHERE user_id = ?
                ");
                $stmt->execute([$userId]);
            }

            return ['success' => true, 'message' => 'Notifications marquées comme lues'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
}

<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les dépendances
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../models/User.php');

class UserController {
    
    public function index() {
        // Récupérer les statistiques
        $stats = $this->getUserStats();
        
        // Récupérer tous les utilisateurs
        $users = $this->listUsers();
        
        // Inscriptions mensuelles pour le graphique
        $monthlySubscriptions = $this->getMonthlySubscriptions();
        
        // Extraire les variables pour le template
        $totalUsers = $stats['totalUsers'];
        $streamersCount = $stats['streamersCount'];
        $newUsersThisMonth = $stats['newUsersThisMonth'];
        $activeUsers = $stats['activeUsers'];
        
        // Inclure la vue
        include(__DIR__ . '/../views/backoffice/index.php');
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $this->hydrateUserFromPost($user);
            
            if ($this->validateUser($user)) {
                if ($this->addUser($user)) {
                    $_SESSION['message'] = "Utilisateur créé avec succès";
                    $_SESSION['message_type'] = "success";
                    header('Location: index.php');
                    exit;
                }
            }
        }
        
        // Afficher le formulaire de création
        include(__DIR__ . '/../views/backoffice/createuser.php');
    }

    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();
            $this->hydrateUserFromPost($user);
            
            if ($this->validateUser($user, $id)) {
                if ($this->updateUser($user, $id)) {
                    $_SESSION['message'] = "Utilisateur modifié avec succès";
                    $_SESSION['message_type'] = "success";
                    header('Location: index.php');
                    exit;
                }
            }
            
            // Si erreur, réafficher le formulaire avec les données
            $user = $this->showUser($id);
        } else {
            $user = $this->showUser($id);
        }
        
        if ($user) {
            include(__DIR__ . '/../views/backoffice/modifuser.php');
        } else {
            header('Location: index.php');
            exit;
        }
    }

    public function delete($id) {
        if ($this->deleteUser($id)) {
            $_SESSION['message'] = "Utilisateur supprimé avec succès";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression";
            $_SESSION['message_type'] = "error";
        }
        header('Location: index.php');
        exit;
    }

    public function view($id) {
        $user = $this->showUser($id);
        if ($user) {
            include(__DIR__ . '/../views/backoffice/viewuser.php');
        } else {
            header('Location: index.php');
            exit;
        }
    }

    private function hydrateUserFromPost(User $user) {
        $user->setFirstName($_POST['first_name'] ?? '');
        $user->setLastName($_POST['last_name'] ?? '');
        $user->setUsername($_POST['username'] ?? '');
        $user->setEmail($_POST['email'] ?? '');
        $user->setBirthdate($_POST['birthdate'] ?? '');
        $user->setGender($_POST['gender'] ?? '');
        $user->setCountry($_POST['country'] ?? '');
        $user->setCity($_POST['city'] ?? '');
        $user->setRole($_POST['role'] ?? 'viewer');
        $user->setStreamLink($_POST['stream_link'] ?? '');
        $user->setStreamDescription($_POST['stream_description'] ?? '');
        $user->setStreamPlatform($_POST['stream_platform'] ?? '');
        
        if (!empty($_POST['password'])) {
            $user->setPassword($_POST['password']);
        }
    }

    private function validateUser(User $user, $excludeId = null) {
        $errors = [];

        // Required fields
        $required = ['first_name', 'last_name', 'username', 'email', 'role'];
        foreach ($required as $field) {
            $getter = 'get' . str_replace('_', '', ucwords($field, '_'));
            if (empty($user->$getter())) {
                $errors[] = "Le champ " . str_replace('_', ' ', $field) . " est requis";
            }
        }

        // Name validation: only letters (including accents), spaces, hyphens, apostrophes; 2-50 chars
        $nameRe = '/^[\p{L} \'-]{2,50}$/u';
        if (!empty($user->getFirstName()) && !preg_match($nameRe, $user->getFirstName())) {
            $errors[] = "Prénom invalide (lettres, espaces, -, ' ; 2-50 caractères)";
        }
        if (!empty($user->getLastName()) && !preg_match($nameRe, $user->getLastName())) {
            $errors[] = "Nom invalide (lettres, espaces, -, ' ; 2-50 caractères)";
        }

        // Username validation
        if (!empty($user->getUsername()) && !preg_match('/^[a-zA-Z0-9_-]{3,30}$/', $user->getUsername())) {
            $errors[] = "Nom d'utilisateur invalide (3-30 caractères, lettres, chiffres, - et _ autorisés)";
        }

        // Email validation
        if (!empty($user->getEmail()) && !filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide";
        }

        // Birthdate -> must be older than 13 years
        $birth = $user->getBirthdate();
        if (empty($birth)) {
            $errors[] = "Date de naissance requise";
        } else {
            $birthDate = null;
            // Accept DD/MM/YYYY or ISO YYYY-MM-DD
            if (strpos($birth, '/') !== false) {
                $parts = explode('/', $birth);
                if (count($parts) === 3) {
                    $d = intval($parts[0]);
                    $m = intval($parts[1]);
                    $y = intval($parts[2]);
                    if (checkdate($m, $d, $y)) {
                        $birthDate = new DateTime("$y-$m-$d");
                    }
                }
            } else {
                try {
                    $birthDate = new DateTime($birth);
                } catch (Exception $e) {
                    $birthDate = null;
                }
            }

            if (!$birthDate) {
                $errors[] = "Format de date de naissance invalide";
            } else {
                $today = new DateTime();
                $age = $today->diff($birthDate)->y;
                if ($age <= 13) {
                    $errors[] = "Vous devez avoir plus de 13 ans";
                }
            }
        }

        // Password validation: required on create, optional on update but must meet strength if provided
        $password = $user->getPassword();
        if ($excludeId === null) {
            // create
            if (empty($password)) {
                $errors[] = "Mot de passe requis";
            }
        }
        if (!empty($password)) {
            $pwOk = strlen($password) >= 6 && preg_match('/[A-Z]/', $password) && preg_match('/[a-z]/', $password) && preg_match('/\d/', $password);
            if (!$pwOk) {
                $errors[] = "Mot de passe trop faible (min 6 caractères, 1 majuscule, 1 minuscule, 1 chiffre)";
            }
        }

        // Vérification unicité email
        if (!empty($user->getEmail()) && $this->emailExists($user->getEmail(), $excludeId)) {
            $errors[] = "Cet email est déjà utilisé";
        }

        // Vérification unicité username
        if (!empty($user->getUsername()) && $this->usernameExists($user->getUsername(), $excludeId)) {
            $errors[] = "Ce nom d'utilisateur est déjà utilisé";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            return false;
        }

        return true;
    }

    // Méthodes d'accès aux données
    public function listUsers() {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list->fetchAll();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            return $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addUser(User $user) {
        $sql = "INSERT INTO users VALUES (NULL, :first_name, :last_name, :username, :email, :birthdate, :gender, :country, :city, :role, :stream_link, :stream_description, :stream_platform, :password, :profile_image, NOW(), NOW(), NOW())";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            return $query->execute([
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'birthdate' => $user->getBirthdate(),
                'gender' => $user->getGender(),
                'country' => $user->getCountry(),
                'city' => $user->getCity(),
                'role' => $user->getRole(),
                'stream_link' => $user->getStreamLink(),
                'stream_description' => $user->getStreamDescription(),
                'stream_platform' => $user->getStreamPlatform(),
                'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
                'profile_image' => $user->getProfileImage() ?? ''
            ]);
        } catch (Exception $e) {
            $_SESSION['errors'] = ['Erreur lors de la création: ' . $e->getMessage()];
            return false;
        }
    }

    public function updateUser(User $user, $id) {
        try {
            $db = config::getConnexion();
            
            $sql = "UPDATE users SET 
                    first_name = :first_name,
                    last_name = :last_name,
                    username = :username,
                    email = :email,
                    birthdate = :birthdate,
                    gender = :gender,
                    country = :country,
                    city = :city,
                    role = :role,
                    stream_link = :stream_link,
                    stream_description = :stream_description,
                    stream_platform = :stream_platform,
                    updated_at = NOW()";
            
            $params = [
                'id' => $id,
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'birthdate' => $user->getBirthdate(),
                'gender' => $user->getGender(),
                'country' => $user->getCountry(),
                'city' => $user->getCity(),
                'role' => $user->getRole(),
                'stream_link' => $user->getStreamLink(),
                'stream_description' => $user->getStreamDescription(),
                'stream_platform' => $user->getStreamPlatform()
            ];
            
            if (!empty($user->getPassword())) {
                $sql .= ", password = :password";
                $params['password'] = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            }
            
            $sql .= " WHERE id = :id";
            
            $query = $db->prepare($sql);
            return $query->execute($params);
            
        } catch (PDOException $e) {
            $_SESSION['errors'] = ['Erreur lors de la modification: ' . $e->getMessage()];
            return false;
        }
    }

    public function showUser($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id);

        try {
            $query->execute();
            $userData = $query->fetch();
            
            if ($userData) {
                $user = new User();
                $user->setId($userData['id'])
                     ->setFirstName($userData['first_name'])
                     ->setLastName($userData['last_name'])
                     ->setUsername($userData['username'])
                     ->setEmail($userData['email'])
                     ->setBirthdate($userData['birthdate'])
                     ->setGender($userData['gender'])
                     ->setCountry($userData['country'])
                     ->setCity($userData['city'])
                     ->setRole($userData['role'])
                     ->setStreamLink($userData['stream_link'])
                     ->setStreamDescription($userData['stream_description'])
                     ->setStreamPlatform($userData['stream_platform'])
                     ->setJoinDate($userData['join_date'])
                     ->setIsBanned($userData['is_banned'] ?? 0)
                     ->setBanType($userData['ban_type'] ?? null)
                     ->setBanReason($userData['ban_reason'] ?? null)
                     ->setBannedAt($userData['banned_at'] ?? null)
                     ->setBannedUntil($userData['banned_until'] ?? null)
                     ->setBannedBy($userData['banned_by'] ?? null);
                return $user;
            }
            return null;
            
        } catch (Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }

    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        $params = ['email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        
        try {
            $query->execute($params);
            return $query->fetchColumn() > 0;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $params = ['username' => $username];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        
        try {
            $query->execute($params);
            return $query->fetchColumn() > 0;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getUserStats() {
        $stats = [
            'totalUsers' => 0,
            'streamersCount' => 0,
            'newUsersThisMonth' => 0,
            'activeUsers' => 0
        ];

        try {
            $db = config::getConnexion();
            
            // Total users
            $query = $db->query("SELECT COUNT(*) as total FROM users");
            $stats['totalUsers'] = $query->fetchColumn();
            
            // Streamers count
            $query = $db->query("SELECT COUNT(*) as streamers FROM users WHERE role = 'streamer'");
            $stats['streamersCount'] = $query->fetchColumn();
            
            // New users this month
            $query = $db->query("SELECT COUNT(*) as new_users FROM users WHERE MONTH(join_date) = MONTH(CURRENT_DATE()) AND YEAR(join_date) = YEAR(CURRENT_DATE())");
            $stats['newUsersThisMonth'] = $query->fetchColumn();
            
            // Active users (tous pour l'instant)
            $stats['activeUsers'] = $stats['totalUsers'];
            
            return $stats;
            
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function getMonthlySubscriptions() {
        // Données simulées - à remplacer par une vraie requête
        return [12, 19, 15, 25, 22, 30, 28, 32, 30, 35, 40, 45];
    }

    /**
     * Bannir un utilisateur (soft ou permanent)
     */
    public function banUser() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $userId = $_POST['user_id'] ?? null;
        $banType = $_POST['ban_type'] ?? 'permanent'; // 'soft' ou 'permanent'
        $banReason = $_POST['ban_reason'] ?? '';
        $bannedUntil = $_POST['banned_until'] ?? null; // Pour soft ban
        // Identifier correctement l'admin connecté
        // Selon l'application, l'ID peut être stocké sous différentes clés de session.
        // On tente plusieurs clés courantes pour éviter qu'il reste à NULL.
        $adminId = null;
        if (isset($_SESSION['user']) && is_array($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $adminId = $_SESSION['user']['id'];
        } elseif (isset($_SESSION['id'])) {
            $adminId = $_SESSION['id'];
        } elseif (isset($_SESSION['admin_id'])) {
            $adminId = $_SESSION['admin_id'];
        } elseif (isset($_SESSION['user_id'])) {
            $adminId = $_SESSION['user_id'];
        }

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
            exit;
        }

        // Validation
        if ($banType === 'soft' && empty($bannedUntil)) {
            echo json_encode(['success' => false, 'message' => 'Date d\'expiration requise pour un bannissement temporaire']);
            exit;
        }

        if ($banType === 'soft') {
            // Valider que la date est dans le futur
            $until = new DateTime($bannedUntil);
            $now = new DateTime();
            if ($until <= $now) {
                echo json_encode(['success' => false, 'message' => 'La date d\'expiration doit être dans le futur']);
                exit;
            }
        }

        try {
            $db = config::getConnexion();
            
            $sql = "UPDATE users SET 
                    is_banned = 1,
                    ban_type = :ban_type,
                    ban_reason = :ban_reason,
                    banned_at = NOW(),
                    banned_until = :banned_until,
                    banned_by = :banned_by
                    WHERE id = :user_id";
            
            $query = $db->prepare($sql);
            $success = $query->execute([
                'ban_type' => $banType,
                'ban_reason' => $banReason,
                'banned_until' => $banType === 'soft' ? $bannedUntil : null,
                'banned_by' => $adminId,
                'user_id' => $userId
            ]);

            if ($success) {
                $statusMsg = $banType === 'permanent' ? 'définitivement' : 'temporairement';
                echo json_encode([
                    'success' => true, 
                    'message' => "Utilisateur banni $statusMsg avec succès"
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors du bannissement']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Débannir un utilisateur
     */
    public function unbanUser() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        $userId = $_POST['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
            exit;
        }

        try {
            $db = config::getConnexion();
            
            $sql = "UPDATE users SET 
                    is_banned = 0,
                    ban_type = NULL,
                    ban_reason = NULL,
                    banned_at = NULL,
                    banned_until = NULL,
                    banned_by = NULL
                    WHERE id = :user_id";
            
            $query = $db->prepare($sql);
            $success = $query->execute(['user_id' => $userId]);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Utilisateur débanni avec succès']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors du débannissement']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Récupérer le statut de bannissement d'un utilisateur
     */
    public function getBanStatus() {
        header('Content-Type: application/json');
        
        $userId = $_GET['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'ID utilisateur manquant']);
            exit;
        }

        try {
            $db = config::getConnexion();
            $sql = "SELECT is_banned, ban_type, ban_reason, banned_at, banned_until, banned_by 
                    FROM users WHERE id = :user_id";
            
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $userId]);
            $banData = $query->fetch(PDO::FETCH_ASSOC);

            if ($banData) {
                echo json_encode(['success' => true, 'data' => $banData]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * API pour la pagination, le tri et le filtrage côté serveur
     * Requête AJAX depuis le dashboard
     */
    public function getTableData() {
        header('Content-Type: application/json');

        try {
            // Récupérer les paramètres
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;
            $sortColumn = isset($_GET['sortColumn']) ? $_GET['sortColumn'] : 'created_at';
            $sortOrder = isset($_GET['sortOrder']) && strtoupper($_GET['sortOrder']) === 'ASC' ? 'ASC' : 'DESC';
            $searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
            $roleFilter = isset($_GET['role']) ? $_GET['role'] : '';
            $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

            // Mapping des colonnes pour éviter les injections SQL
            $columnMap = [
                'id' => 'u.id',
                'user' => "CONCAT(u.first_name, ' ', u.last_name)",
                'email' => 'u.email',
                'role' => 'u.role',
                'date' => 'u.join_date',
                'status' => 'u.is_banned',
                'created_at' => 'u.created_at'
            ];

            $validSortColumn = isset($columnMap[$sortColumn]) ? $columnMap[$sortColumn] : 'u.created_at';

            // Construire la requête de base
            $db = config::getConnexion();
            $sql = "SELECT u.*, 
                    CONCAT(u.first_name, ' ', u.last_name) as full_name 
                    FROM users u 
                    WHERE 1=1";

            $params = [];

            // Filtrer par recherche (nom complet uniquement)
            if (!empty($searchQuery)) {
                $sql .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE :search";
                $params['search'] = '%' . $searchQuery . '%';
            }

            // Filtrer par rôle
            if (!empty($roleFilter) && in_array($roleFilter, ['viewer', 'streamer', 'admin'])) {
                $sql .= " AND u.role = :role";
                $params['role'] = $roleFilter;
            }

            // Filtrer par statut
            if (!empty($statusFilter)) {
                if ($statusFilter === 'active') {
                    $sql .= " AND u.is_banned = 0";
                } elseif ($statusFilter === 'banned') {
                    $sql .= " AND u.is_banned = 1 AND u.ban_type = 'permanent'";
                } elseif ($statusFilter === 'suspended') {
                    $sql .= " AND u.is_banned = 1 AND u.ban_type = 'soft' AND u.banned_until > NOW()";
                }
            }

            // Compter le total des enregistrements filtrés
            $countSql = "SELECT COUNT(*) as total FROM users u WHERE 1=1";
            if (!empty($searchQuery)) {
                $countSql .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE :search";
            }
            if (!empty($roleFilter) && in_array($roleFilter, ['viewer', 'streamer', 'admin'])) {
                $countSql .= " AND u.role = :role";
            }
            if (!empty($statusFilter)) {
                if ($statusFilter === 'active') {
                    $countSql .= " AND u.is_banned = 0";
                } elseif ($statusFilter === 'banned') {
                    $countSql .= " AND u.is_banned = 1 AND u.ban_type = 'permanent'";
                } elseif ($statusFilter === 'suspended') {
                    $countSql .= " AND u.is_banned = 1 AND u.ban_type = 'soft' AND u.banned_until > NOW()";
                }
            }

            $countQuery = $db->prepare($countSql);
            $countQuery->execute($params);
            $totalRows = $countQuery->fetchColumn();
            $totalPages = ceil($totalRows / $rowsPerPage);

            // Valider la page
            if ($page < 1) $page = 1;
            if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

            // Calculer l'offset
            $offset = ($page - 1) * $rowsPerPage;

            // Ajouter le tri et la pagination
            $sql .= " ORDER BY " . $validSortColumn . " " . $sortOrder;
            $sql .= " LIMIT :limit OFFSET :offset";

            $params['limit'] = $rowsPerPage;
            $params['offset'] = $offset;

            // Exécuter la requête
            $query = $db->prepare($sql);
            
            // Binder les paramètres avec les bons types
            foreach ($params as $key => $value) {
                if ($key === 'limit' || $key === 'offset') {
                    $query->bindValue(':' . $key, $value, PDO::PARAM_INT);
                } else {
                    $query->bindValue(':' . $key, $value);
                }
            }

            $query->execute();
            $users = $query->fetchAll(PDO::FETCH_ASSOC);

            // Formater les données pour l'affichage
            $formattedUsers = [];
            foreach ($users as $user) {
                $formattedUsers[] = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'country' => $user['country'],
                    'role' => $user['role'],
                    'join_date' => date('d/m/Y', strtotime($user['join_date'])),
                    'is_banned' => $user['is_banned'],
                    'ban_type' => $user['ban_type'],
                    'banned_until' => $user['banned_until']
                ];
            }

            echo json_encode([
                'success' => true,
                'data' => $formattedUsers,
                'pagination' => [
                    'currentPage' => (int)$page,
                    'totalPages' => (int)$totalPages,
                    'totalRows' => (int)$totalRows,
                    'rowsPerPage' => (int)$rowsPerPage,
                    'startRow' => ($totalRows > 0) ? $offset + 1 : 0,
                    'endRow' => min($offset + $rowsPerPage, $totalRows)
                ]
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * API - Répartition des utilisateurs par pays (pour la carte Leaflet)
     * Paramètres :
     *  - status : all|active|banned (filtre sur le statut)
     *  - period : all|7d|30d|90d (filtre temporel sur created_at)
     */
    public function getUsersByCountry() {
        header('Content-Type: application/json');

        $status = isset($_GET['status']) ? strtolower($_GET['status']) : 'all';
        $period = isset($_GET['period']) ? strtolower($_GET['period']) : 'all';

        $allowedStatus = ['all', 'active', 'banned'];
        if (!in_array($status, $allowedStatus, true)) {
            $status = 'all';
        }

        $periodDays = match ($period) {
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            default => 0,
        };

        // Normalisation simple pour matcher des pays saisis différemment (accents, minuscules)
        $normalizeCountry = function ($name) {
            $name = trim((string)$name);
            if ($name === '') {
                return '';
            }
            $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
            if ($ascii === false) {
                $ascii = $name;
            }
            $ascii = strtolower($ascii);
            // Retirer tout ce qui n'est pas lettre ou espace
            $ascii = preg_replace('/[^a-z\s]/', ' ', $ascii);
            // Compact spaces
            $ascii = preg_replace('/\s+/', ' ', $ascii);
            return trim($ascii);
        };

        // Centroides simplifiés (français + anglais + variantes courantes + codes ISO2)
        $countryCoords = [
            // Codes ISO2 directs
            'fr' => ['lat' => 46.2276, 'lng' => 2.2137, 'code' => 'FR'],
            'us' => ['lat' => 37.0902, 'lng' => -95.7129, 'code' => 'US'],
            'ca' => ['lat' => 56.1304, 'lng' => -106.3468, 'code' => 'CA'],
            'gb' => ['lat' => 55.3781, 'lng' => -3.4360, 'code' => 'GB'],
            'uk' => ['lat' => 55.3781, 'lng' => -3.4360, 'code' => 'GB'],
            'de' => ['lat' => 51.1657, 'lng' => 10.4515, 'code' => 'DE'],
            'es' => ['lat' => 40.4637, 'lng' => -3.7492, 'code' => 'ES'],
            'it' => ['lat' => 41.8719, 'lng' => 12.5674, 'code' => 'IT'],
            'be' => ['lat' => 50.5039, 'lng' => 4.4699, 'code' => 'BE'],
            'nl' => ['lat' => 52.1326, 'lng' => 5.2913, 'code' => 'NL'],
            'ch' => ['lat' => 46.8182, 'lng' => 8.2275, 'code' => 'CH'],
            'pt' => ['lat' => 39.3999, 'lng' => -8.2245, 'code' => 'PT'],
            'ma' => ['lat' => 31.7917, 'lng' => -7.0926, 'code' => 'MA'],
            'dz' => ['lat' => 28.0339, 'lng' => 1.6596, 'code' => 'DZ'],
            'tn' => ['lat' => 33.8869, 'lng' => 9.5375, 'code' => 'TN'],
            'sn' => ['lat' => 14.4974, 'lng' => -14.4524, 'code' => 'SN'],
            'ci' => ['lat' => 7.5400, 'lng' => -5.5471, 'code' => 'CI'],
            'ml' => ['lat' => 17.5707, 'lng' => -3.9962, 'code' => 'ML'],
            'ne' => ['lat' => 17.6078, 'lng' => 8.0817, 'code' => 'NE'],
            'cm' => ['lat' => 5.6037, 'lng' => 12.3081, 'code' => 'CM'],
            'cd' => ['lat' => -4.0383, 'lng' => 21.7587, 'code' => 'CD'],
            'br' => ['lat' => -14.2350, 'lng' => -51.9253, 'code' => 'BR'],
            'mx' => ['lat' => 23.6345, 'lng' => -102.5528, 'code' => 'MX'],
            'ar' => ['lat' => -38.4161, 'lng' => -63.6167, 'code' => 'AR'],
            'co' => ['lat' => 4.5709, 'lng' => -74.2973, 'code' => 'CO'],
            'cl' => ['lat' => -35.6751, 'lng' => -71.5430, 'code' => 'CL'],
            'pe' => ['lat' => -9.1900, 'lng' => -75.0152, 'code' => 'PE'],
            'jp' => ['lat' => 36.2048, 'lng' => 138.2529, 'code' => 'JP'],
            'cn' => ['lat' => 35.8617, 'lng' => 104.1954, 'code' => 'CN'],
            'in' => ['lat' => 20.5937, 'lng' => 78.9629, 'code' => 'IN'],
            'ru' => ['lat' => 61.5240, 'lng' => 105.3188, 'code' => 'RU'],
            'au' => ['lat' => -25.2744, 'lng' => 133.7751, 'code' => 'AU'],
            'za' => ['lat' => -30.5595, 'lng' => 22.9375, 'code' => 'ZA'],
            'tr' => ['lat' => 38.9637, 'lng' => 35.2433, 'code' => 'TR'],
            'pl' => ['lat' => 51.9194, 'lng' => 19.1451, 'code' => 'PL'],
            'se' => ['lat' => 60.1282, 'lng' => 18.6435, 'code' => 'SE'],
            'no' => ['lat' => 60.4720, 'lng' => 8.4689, 'code' => 'NO'],
            'fi' => ['lat' => 61.9241, 'lng' => 25.7482, 'code' => 'FI'],
            'dk' => ['lat' => 56.2639, 'lng' => 9.5018, 'code' => 'DK'],
            'ie' => ['lat' => 53.1424, 'lng' => -7.6921, 'code' => 'IE'],

            // Noms FR/EN
            'france' => ['lat' => 46.2276, 'lng' => 2.2137, 'code' => 'FR'],
            'etats unis' => ['lat' => 37.0902, 'lng' => -95.7129, 'code' => 'US'],
            'united states' => ['lat' => 37.0902, 'lng' => -95.7129, 'code' => 'US'],
            'usa' => ['lat' => 37.0902, 'lng' => -95.7129, 'code' => 'US'],
            'canada' => ['lat' => 56.1304, 'lng' => -106.3468, 'code' => 'CA'],
            'royaume uni' => ['lat' => 55.3781, 'lng' => -3.4360, 'code' => 'GB'],
            'united kingdom' => ['lat' => 55.3781, 'lng' => -3.4360, 'code' => 'GB'],
            'uk' => ['lat' => 55.3781, 'lng' => -3.4360, 'code' => 'GB'],
            'allemagne' => ['lat' => 51.1657, 'lng' => 10.4515, 'code' => 'DE'],
            'germany' => ['lat' => 51.1657, 'lng' => 10.4515, 'code' => 'DE'],
            'espagne' => ['lat' => 40.4637, 'lng' => -3.7492, 'code' => 'ES'],
            'spain' => ['lat' => 40.4637, 'lng' => -3.7492, 'code' => 'ES'],
            'italie' => ['lat' => 41.8719, 'lng' => 12.5674, 'code' => 'IT'],
            'italy' => ['lat' => 41.8719, 'lng' => 12.5674, 'code' => 'IT'],
            'belgique' => ['lat' => 50.5039, 'lng' => 4.4699, 'code' => 'BE'],
            'belgium' => ['lat' => 50.5039, 'lng' => 4.4699, 'code' => 'BE'],
            'pays bas' => ['lat' => 52.1326, 'lng' => 5.2913, 'code' => 'NL'],
            'netherlands' => ['lat' => 52.1326, 'lng' => 5.2913, 'code' => 'NL'],
            'hollande' => ['lat' => 52.1326, 'lng' => 5.2913, 'code' => 'NL'],
            'suisse' => ['lat' => 46.8182, 'lng' => 8.2275, 'code' => 'CH'],
            'switzerland' => ['lat' => 46.8182, 'lng' => 8.2275, 'code' => 'CH'],
            'portugal' => ['lat' => 39.3999, 'lng' => -8.2245, 'code' => 'PT'],
            'maroc' => ['lat' => 31.7917, 'lng' => -7.0926, 'code' => 'MA'],
            'morocco' => ['lat' => 31.7917, 'lng' => -7.0926, 'code' => 'MA'],
            'algerie' => ['lat' => 28.0339, 'lng' => 1.6596, 'code' => 'DZ'],
            'algeria' => ['lat' => 28.0339, 'lng' => 1.6596, 'code' => 'DZ'],
            'tunisie' => ['lat' => 33.8869, 'lng' => 9.5375, 'code' => 'TN'],
            'tunisia' => ['lat' => 33.8869, 'lng' => 9.5375, 'code' => 'TN'],
            'senegal' => ['lat' => 14.4974, 'lng' => -14.4524, 'code' => 'SN'],
            'cote divoire' => ['lat' => 7.5400, 'lng' => -5.5471, 'code' => 'CI'],
            'ivory coast' => ['lat' => 7.5400, 'lng' => -5.5471, 'code' => 'CI'],
            'mali' => ['lat' => 17.5707, 'lng' => -3.9962, 'code' => 'ML'],
            'niger' => ['lat' => 17.6078, 'lng' => 8.0817, 'code' => 'NE'],
            'cameroon' => ['lat' => 5.6037, 'lng' => 12.3081, 'code' => 'CM'],
            'cameroun' => ['lat' => 5.6037, 'lng' => 12.3081, 'code' => 'CM'],
            'rdc' => ['lat' => -4.0383, 'lng' => 21.7587, 'code' => 'CD'],
            'congo' => ['lat' => -4.0383, 'lng' => 21.7587, 'code' => 'CD'],
            'brazil' => ['lat' => -14.2350, 'lng' => -51.9253, 'code' => 'BR'],
            'bresil' => ['lat' => -14.2350, 'lng' => -51.9253, 'code' => 'BR'],
            'argentina' => ['lat' => -38.4161, 'lng' => -63.6167, 'code' => 'AR'],
            'argentine' => ['lat' => -38.4161, 'lng' => -63.6167, 'code' => 'AR'],
            'mexico' => ['lat' => 23.6345, 'lng' => -102.5528, 'code' => 'MX'],
            'mexique' => ['lat' => 23.6345, 'lng' => -102.5528, 'code' => 'MX'],
            'colombie' => ['lat' => 4.5709, 'lng' => -74.2973, 'code' => 'CO'],
            'colombia' => ['lat' => 4.5709, 'lng' => -74.2973, 'code' => 'CO'],
            'chile' => ['lat' => -35.6751, 'lng' => -71.5430, 'code' => 'CL'],
            'chili' => ['lat' => -35.6751, 'lng' => -71.5430, 'code' => 'CL'],
            'peru' => ['lat' => -9.1900, 'lng' => -75.0152, 'code' => 'PE'],
            'perou' => ['lat' => -9.1900, 'lng' => -75.0152, 'code' => 'PE'],
            'japan' => ['lat' => 36.2048, 'lng' => 138.2529, 'code' => 'JP'],
            'japon' => ['lat' => 36.2048, 'lng' => 138.2529, 'code' => 'JP'],
            'china' => ['lat' => 35.8617, 'lng' => 104.1954, 'code' => 'CN'],
            'chine' => ['lat' => 35.8617, 'lng' => 104.1954, 'code' => 'CN'],
            'india' => ['lat' => 20.5937, 'lng' => 78.9629, 'code' => 'IN'],
            'inde' => ['lat' => 20.5937, 'lng' => 78.9629, 'code' => 'IN'],
            'russia' => ['lat' => 61.5240, 'lng' => 105.3188, 'code' => 'RU'],
            'russie' => ['lat' => 61.5240, 'lng' => 105.3188, 'code' => 'RU'],
            'australia' => ['lat' => -25.2744, 'lng' => 133.7751, 'code' => 'AU'],
            'australie' => ['lat' => -25.2744, 'lng' => 133.7751, 'code' => 'AU'],
            'south africa' => ['lat' => -30.5595, 'lng' => 22.9375, 'code' => 'ZA'],
            'afrique du sud' => ['lat' => -30.5595, 'lng' => 22.9375, 'code' => 'ZA'],
            'turkey' => ['lat' => 38.9637, 'lng' => 35.2433, 'code' => 'TR'],
            'turquie' => ['lat' => 38.9637, 'lng' => 35.2433, 'code' => 'TR'],
            'poland' => ['lat' => 51.9194, 'lng' => 19.1451, 'code' => 'PL'],
            'pologne' => ['lat' => 51.9194, 'lng' => 19.1451, 'code' => 'PL'],
            'sweden' => ['lat' => 60.1282, 'lng' => 18.6435, 'code' => 'SE'],
            'suede' => ['lat' => 60.1282, 'lng' => 18.6435, 'code' => 'SE'],
            'norway' => ['lat' => 60.4720, 'lng' => 8.4689, 'code' => 'NO'],
            'norvege' => ['lat' => 60.4720, 'lng' => 8.4689, 'code' => 'NO'],
            'finland' => ['lat' => 61.9241, 'lng' => 25.7482, 'code' => 'FI'],
            'finlande' => ['lat' => 61.9241, 'lng' => 25.7482, 'code' => 'FI'],
            'denmark' => ['lat' => 56.2639, 'lng' => 9.5018, 'code' => 'DK'],
            'danemark' => ['lat' => 56.2639, 'lng' => 9.5018, 'code' => 'DK'],
            'ireland' => ['lat' => 53.1424, 'lng' => -7.6921, 'code' => 'IE'],
            'irlande' => ['lat' => 53.1424, 'lng' => -7.6921, 'code' => 'IE'],
        ];

        try {
            $db = config::getConnexion();

            $sql = "SELECT country,
                           COUNT(*) AS total_users,
                           SUM(CASE WHEN is_banned = 0 THEN 1 ELSE 0 END) AS active_users,
                           SUM(CASE WHEN is_banned = 1 THEN 1 ELSE 0 END) AS banned_users,
                           SUM(CASE WHEN created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) AS new_30d
                    FROM users
                    WHERE country IS NOT NULL AND country <> ''";

            $params = [];

            if ($status === 'active') {
                $sql .= " AND is_banned = 0";
            } elseif ($status === 'banned') {
                $sql .= " AND is_banned = 1";
            }

            if ($periodDays > 0) {
                $sql .= " AND created_at >= DATE_SUB(CURDATE(), INTERVAL :periodDays DAY)";
                $params['periodDays'] = $periodDays;
            }

            $sql .= " GROUP BY country ORDER BY total_users DESC";

            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value, PDO::PARAM_INT);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];
            $totalUsers = 0;

            foreach ($rows as $row) {
                $countryName = $row['country'];
                $normalized = $normalizeCountry($countryName);

                // Si la valeur est un code ISO2 (ex: TN, FR), normaliser en minuscule et tenter la correspondance
                if (strlen($countryName) === 2 && ctype_alpha($countryName)) {
                    $normalized = strtolower($countryName);
                }

                $coords = $countryCoords[$normalized] ?? null;

                $result[] = [
                    'country' => $countryName,
                    'countryName' => $countryName,
                    'countryCode' => $coords['code'] ?? strtoupper(substr($normalized !== '' ? $normalized : 'UN', 0, 2)),
                    'users' => (int)$row['total_users'],
                    'active' => (int)$row['active_users'],
                    'banned' => (int)$row['banned_users'],
                    'new30d' => (int)$row['new_30d'],
                    'lat' => $coords['lat'] ?? 0,
                    'lng' => $coords['lng'] ?? 0,
                ];

                $totalUsers += (int)$row['total_users'];
            }

            echo json_encode([
                'success' => true,
                'data' => $result,
                'total_countries' => count($result),
                'total_users' => $totalUsers,
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ]);
        }
        exit;
    }
}

// Gestion des routes - seulement si ce fichier est appelé directement
// Pour l'instant, nous allons commenter cette partie et gérer le routing différemment
?>
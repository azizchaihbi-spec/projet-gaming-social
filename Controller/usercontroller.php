<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les dépendances
include_once(__DIR__ . '/../config/config.php');
include_once(__DIR__ . '/../Model/User.php');

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
        include(__DIR__ . '/../View/BackOffice/index.php');
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
        include(__DIR__ . '/../View/BackOffice/createuser.php');
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
            include(__DIR__ . '/../View/BackOffice/modifuser.php');
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
            include(__DIR__ . '/../View/BackOffice/viewuser.php');
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
                     ->setJoinDate($userData['join_date']);
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
}

// Gestion des routes - seulement si ce fichier est appelé directement
// Pour l'instant, nous allons commenter cette partie et gérer le routing différemment
?>
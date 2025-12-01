<?php
class User {
    private $id;
    private $first_name;
    private $last_name;
    private $username;
    private $email;
    private $birthdate;
    private $gender;
    private $country;
    private $city;
    private $role;
    private $stream_link;
    private $stream_description;
    private $stream_platform;
    private $password;
    private $profile_image;
    private $join_date;
    private $created_at;
    private $updated_at;

    // Getters
    public function getId() { return $this->id; }
    public function getFirstName() { return $this->first_name; }
    public function getLastName() { return $this->last_name; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getBirthdate() { return $this->birthdate; }
    public function getGender() { return $this->gender; }
    public function getCountry() { return $this->country; }
    public function getCity() { return $this->city; }
    public function getRole() { return $this->role; }
    public function getStreamLink() { return $this->stream_link; }
    public function getStreamDescription() { return $this->stream_description; }
    public function getStreamPlatform() { return $this->stream_platform; }
    public function getPassword() { return $this->password; }
    public function getProfileImage() { return $this->profile_image; }
    public function getJoinDate() { return $this->join_date; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setFirstName($first_name) { $this->first_name = $first_name; }
    public function setLastName($last_name) { $this->last_name = $last_name; }
    public function setUsername($username) { $this->username = $username; }
    public function setEmail($email) { $this->email = $email; }
    public function setBirthdate($birthdate) { $this->birthdate = $birthdate; }
    public function setGender($gender) { $this->gender = $gender; }
    public function setCountry($country) { $this->country = $country; }
    public function setCity($city) { $this->city = $city; }
    public function setRole($role) { $this->role = $role; }
    public function setStreamLink($stream_link) { $this->stream_link = $stream_link; }
    public function setStreamDescription($stream_description) { $this->stream_description = $stream_description; }
    public function setStreamPlatform($stream_platform) { $this->stream_platform = $stream_platform; }
    public function setPassword($password) { $this->password = $password; }
    public function setProfileImage($profile_image) { $this->profile_image = $profile_image; }
    public function setJoinDate($join_date) { $this->join_date = $join_date; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
    public function setUpdatedAt($updated_at) { $this->updated_at = $updated_at; }
}

class Auth {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    private function validateUser(User $user) {
        $errors = [];

        $first = trim((string)$user->getFirstName());
        $last = trim((string)$user->getLastName());
        $username = trim((string)$user->getUsername());
        $email = trim((string)$user->getEmail());
        $birthdate = trim((string)$user->getBirthdate());
        $country = trim((string)$user->getCountry());
        $role = trim((string)$user->getRole());
        $password = (string)$user->getPassword();
        $streamLink = trim((string)$user->getStreamLink());

        // Noms alphabetiques 2-50 (accents, espaces, tirets, apostrophes)
        if ($first === '' || !preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$/u", $first)) {
            $errors[] = "Prénom invalide (lettres uniquement, 2-50)";
        }
        if ($last === '' || !preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$/u", $last)) {
            $errors[] = "Nom invalide (lettres uniquement, 2-50)";
        }

        // Username 3-30 alphanum _ -
        if ($username === '' || !preg_match('/^[A-Za-z0-9_-]{3,30}$/', $username)) {
            $errors[] = "Nom d'utilisateur invalide (3-30, lettres/chiffres/_/-)";
        }

        // Email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }

        // Age >= 13
        if ($birthdate === '') {
            $errors[] = "Date de naissance obligatoire";
        } else {
            try {
                $bd = new DateTime($birthdate);
                $today = new DateTime();
                $age = $today->diff($bd)->y;
                if ($age < 13) {
                    $errors[] = "Âge minimum 13 ans";
                }
            } catch (Exception $e) {
                $errors[] = "Date de naissance invalide";
            }
        }

        // Pays
        if ($country === '') {
            $errors[] = "Pays obligatoire";
        }

        // Rôle (viewer/streamer)
        if ($role === '' || !in_array($role, ['viewer','streamer'], true)) {
            $errors[] = "Rôle invalide";
        }

        // Mot de passe
        if (!(strlen($password) >= 6 && preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password) && preg_match('/[0-9]/', $password))) {
            $errors[] = "Mot de passe faible (min 6, 1 maj, 1 min, 1 chiffre)";
        }

        // Lien de stream optionnel mais valide si fourni
        if ($streamLink !== '') {
            if (!filter_var($streamLink, FILTER_VALIDATE_URL)) {
                $errors[] = "Lien de stream invalide";
            } else {
                $scheme = parse_url($streamLink, PHP_URL_SCHEME);
                if (!in_array($scheme, ['http','https'], true)) {
                    $errors[] = "Lien de stream doit utiliser http/https";
                }
            }
        }

        return $errors;
    }

    public function register(User $user) {
        try {
            // Validation côté serveur
            $errors = $this->validateUser($user);
            if (!empty($errors)) {
                return ['success' => false, 'message' => implode(' | ', $errors)];
            }

            // Vérifier si l'email existe déjà
            $checkStmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$user->getEmail()]);
            if ($checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }

            // Vérifier si le username existe déjà
            $checkStmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
            $checkStmt->execute([$user->getUsername()]);
            if ($checkStmt->fetch()) {
                return ['success' => false, 'message' => 'Ce nom d\'utilisateur est déjà pris'];
            }

            // Hasher le mot de passe
            $hashedPassword = password_hash($user->getPassword(), PASSWORD_DEFAULT);

            $stmt = $this->pdo->prepare("
                INSERT INTO users 
                (first_name, last_name, username, email, birthdate, gender, country, city, role, 
                 stream_link, stream_description, stream_platform, password, profile_image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $user->getFirstName(),
                $user->getLastName(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getBirthdate(),
                $user->getGender(),
                $user->getCountry(),
                $user->getCity(),
                $user->getRole(),
                $user->getStreamLink(),
                $user->getStreamDescription(),
                $user->getStreamPlatform(),
                $hashedPassword,
                $user->getProfileImage()
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Inscription réussie!'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, first_name, last_name, username, email, birthdate, gender, country, city, 
                       role, stream_link, stream_description, stream_platform, password, 
                       profile_image, join_date, created_at 
                FROM users WHERE email = ?
            ");
            $stmt->execute([$email]);
            $userData = $stmt->fetch();

            if ($userData && password_verify($password, $userData['password'])) {
                // Ne pas renvoyer le mot de passe
                unset($userData['password']);
                return ['success' => true, 'user' => $userData, 'message' => 'Connexion réussie!'];
            } else {
                return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    public function updateProfile($userId, $profileImage) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $result = $stmt->execute([$profileImage, $userId]);

            if ($result) {
                // Récupérer les nouvelles données utilisateur
                $stmt = $this->pdo->prepare("
                    SELECT id, first_name, last_name, username, email, birthdate, gender, country, city, 
                           role, stream_link, stream_description, stream_platform, 
                           profile_image, join_date, created_at 
                    FROM users WHERE id = ?
                ");
                $stmt->execute([$userId]);
                $userData = $stmt->fetch();

                return ['success' => true, 'user' => $userData, 'message' => 'Profil mis à jour avec succès!'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    public function updateStreamerInfo($userId, $streamLink, $streamDescription, $streamPlatform) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users SET stream_link = ?, stream_description = ?, stream_platform = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([$streamLink, $streamDescription, $streamPlatform, $userId]);

            if ($result) {
                // Récupérer les nouvelles données utilisateur
                $stmt = $this->pdo->prepare("
                    SELECT id, first_name, last_name, username, email, birthdate, gender, country, city, 
                           role, stream_link, stream_description, stream_platform, 
                           profile_image, join_date, created_at 
                    FROM users WHERE id = ?
                ");
                $stmt->execute([$userId]);
                $userData = $stmt->fetch();

                return ['success' => true, 'user' => $userData, 'message' => 'Informations streamer mises à jour!'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    // Créer un token de réinitialisation de mot de passe
    public function createResetToken($email) {
        try {
            // Vérifier si l'email existe
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Aucun compte associé à cet email'];
            }

            // Générer un token unique et sécurisé
            $token = bin2hex(random_bytes(32)); // 64 caractères hexadécimaux
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expire dans 1 heure

            // Stocker le token dans la base de données
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET reset_token = ?, reset_token_expires = ? 
                WHERE email = ?
            ");
            $result = $stmt->execute([$token, $expires, $email]);

            if ($result) {
                return [
                    'success' => true, 
                    'token' => $token,
                    'email' => $email,
                    'message' => 'Token créé avec succès'
                ];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la création du token'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    // Valider un token de réinitialisation
    public function validateResetToken($token) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, reset_token_expires 
                FROM users 
                WHERE reset_token = ? 
                AND reset_token IS NOT NULL
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Token invalide ou expiré'];
            }

            // Vérifier si le token n'a pas expiré
            $now = new DateTime();
            $expires = new DateTime($user['reset_token_expires']);

            if ($now > $expires) {
                // Token expiré, le supprimer
                $this->clearResetToken($user['id']);
                return ['success' => false, 'message' => 'Le lien a expiré. Veuillez faire une nouvelle demande'];
            }

            return [
                'success' => true, 
                'userId' => $user['id'],
                'email' => $user['email']
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la validation: ' . $e->getMessage()];
        }
    }

    // Réinitialiser le mot de passe avec un token valide
    public function resetPasswordByToken($token, $newPassword) {
        try {
            // Valider le token
            $validation = $this->validateResetToken($token);
            if (!$validation['success']) {
                return $validation;
            }

            $userId = $validation['userId'];

            // Hasher le nouveau mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Mettre à jour le mot de passe
            $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $result = $stmt->execute([$hashedPassword, $userId]);

            if ($result) {
                // Supprimer le token après utilisation
                $this->clearResetToken($userId);
                return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès!'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    // Supprimer le token de réinitialisation
    private function clearResetToken($userId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET reset_token = NULL, reset_token_expires = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            // Log l'erreur mais ne pas bloquer le processus
            error_log('Erreur clearResetToken: ' . $e->getMessage());
        }
    }
}
?>
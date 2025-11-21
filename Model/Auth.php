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

    public function register(User $user) {
        try {
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
}
?>
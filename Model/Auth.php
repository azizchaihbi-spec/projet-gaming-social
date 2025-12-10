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
    
    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("\n                SELECT id, first_name, last_name, username, email, birthdate, gender, country, city, \n                       role, stream_link, stream_description, stream_platform, password, \n                       profile_image, join_date, created_at, is_banned, ban_type, banned_until, \n                       ban_reason, banned_by\n                FROM users WHERE email = ?\n            ");
            $stmt->execute([$email]);
            $userData = $stmt->fetch();

            if ($userData && password_verify($password, $userData['password'])) {
                $isBanned = isset($userData['is_banned']) && (int)$userData['is_banned'] === 1;
                if ($isBanned) {
                    $banType = $userData['ban_type'] ?? null;
                    $bannedUntil = $userData['banned_until'] ?? null;
                    $banReason = $userData['ban_reason'] ?? null;

                    if ($banType === 'permanent') {
                        $reasonText = $banReason ? (" Raison: " . $banReason) : "";
                        return ['success' => false, 'message' => 'Votre compte a été banni définitivement.' . $reasonText];
                    }

                    if ($banType === 'soft' && $bannedUntil) {
                        $until = new DateTime($bannedUntil);
                        $now = new DateTime();
                        if ($now < $until) {
                            $remaining = $now->diff($until);
                            $timeMsg = '';
                            if ($remaining->d > 0) {
                                $timeMsg = $remaining->d . ' jour(s)';
                            } elseif ($remaining->h > 0) {
                                $timeMsg = $remaining->h . ' heure(s)';
                            } else {
                                $timeMsg = $remaining->i . ' minute(s)';
                            }
                            $reasonText = $banReason ? (" Raison: " . $banReason) : "";
                            return ['success' => false, 'message' => "Votre compte est suspendu pour encore $timeMsg. Expiration: " . $until->format('d/m/Y H:i') . ($reasonText ? (". " . $reasonText) : "")];
                        } else {
                            $this->clearBan($userData['id']);
                        }
                    }
                }

                unset($userData['password'], $userData['is_banned'], $userData['ban_type'], $userData['banned_until'], $userData['ban_reason'], $userData['banned_by']);
                return ['success' => true, 'user' => $userData, 'message' => 'Connexion réussie!'];
            }

            return ['success' => false, 'message' => 'Email ou mot de passe incorrect'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
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
                            

    /**
     * Nettoyer le bannissement (débannir automatiquement)
     */
    private function clearBan($userId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET is_banned = 0, ban_type = NULL, ban_reason = NULL, 
                    banned_at = NULL, banned_until = NULL, banned_by = NULL 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            // Ignorer l'erreur silencieusement
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

    // Créer un token de réinitialisation de mot de passe (SÉCURISÉ)
    public function createResetToken($email) {
        try {
            // Vérifier si l'email existe
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Aucun compte associé à cet email'];
            }

            // 1. Générer un token unique et aléatoire (envoyé par email)
            $tokenRaw = bin2hex(random_bytes(32)); // 64 caractères hexadécimaux
            
            // 2. Hasher le token avant stockage (double-hashing pour sécurité)
            $tokenHash = hash('sha256', $tokenRaw);
            
            // 3. Expiration : 1 heure
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // 4. Stocker le hash du token + expiration + used=false
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET reset_token = ?, reset_token_expires = ?, reset_token_used = 0
                WHERE email = ?
            ");
            $result = $stmt->execute([$tokenHash, $expires, $email]);

            if ($result) {
                return [
                    'success' => true, 
                    'token' => $tokenRaw,  // Envoyer le token RAW par email
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

    // Valider un token de réinitialisation (SÉCURISÉ)
    public function validateResetToken($token) {
        try {
            // 1. Hasher le token reçu
            $tokenHash = hash('sha256', $token);
            
            // 2. Chercher le hash en base de données
            $stmt = $this->pdo->prepare("
                SELECT id, email, reset_token_expires, reset_token_used
                FROM users 
                WHERE reset_token = ? 
                AND reset_token IS NOT NULL
            ");
            $stmt->execute([$tokenHash]);
            $user = $stmt->fetch();

            // 3. Token introuvable
            if (!$user) {
                return ['success' => false, 'message' => 'Token invalide ou expiré'];
            }

            // 4. Vérifier si le token a déjà été utilisé
            if ((int)$user['reset_token_used'] === 1) {
                return ['success' => false, 'message' => 'Ce token a déjà été utilisé. Faites une nouvelle demande'];
            }

            // 5. Vérifier l'expiration
            $now = new DateTime();
            $expires = new DateTime($user['reset_token_expires']);

            if ($now > $expires) {
                // Token expiré, le marquer comme utilisé pour éviter la réutilisation
                $this->markResetTokenUsed($user['id']);
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

    // Réinitialiser le mot de passe avec un token valide (SÉCURISÉ)
    public function resetPasswordByToken($token, $newPassword) {
        try {
            // 1. Valider le token
            $validation = $this->validateResetToken($token);
            if (!$validation['success']) {
                return $validation;
            }

            $userId = $validation['userId'];

            // 2. Hasher le nouveau mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // 3. Mettre à jour le mot de passe et marquer le token comme utilisé
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password = ?, reset_token_used = 1
                WHERE id = ?
            ");
            $result = $stmt->execute([$hashedPassword, $userId]);

            if ($result) {
                return ['success' => true, 'message' => 'Mot de passe réinitialisé avec succès!'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()];
        }
    }

    // Marquer un token comme utilisé (pour audit trail)
    private function markResetTokenUsed($userId) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET reset_token_used = 1
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log('Erreur markResetTokenUsed: ' . $e->getMessage());
        }
    }
}
?>
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
    private $is_banned;
    private $ban_type;
    private $ban_reason;
    private $banned_at;
    private $banned_until;
    private $banned_by;

    // GETTERS
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
    public function getIsBanned() { return $this->is_banned; }
    public function getBanType() { return $this->ban_type; }
    public function getBanReason() { return $this->ban_reason; }
    public function getBannedAt() { return $this->banned_at; }
    public function getBannedUntil() { return $this->banned_until; }
    public function getBannedBy() { return $this->banned_by; }

    // SETTERS
    public function setId($id) { $this->id = $id; return $this; }
    public function setFirstName($first_name) { $this->first_name = $first_name; return $this; }
    public function setLastName($last_name) { $this->last_name = $last_name; return $this; }
    public function setUsername($username) { $this->username = $username; return $this; }
    public function setEmail($email) { $this->email = $email; return $this; }
    public function setBirthdate($birthdate) { $this->birthdate = $birthdate; return $this; }
    public function setGender($gender) { $this->gender = $gender; return $this; }
    public function setCountry($country) { $this->country = $country; return $this; }
    public function setCity($city) { $this->city = $city; return $this; }
    public function setRole($role) { $this->role = $role; return $this; }
    public function setStreamLink($stream_link) { $this->stream_link = $stream_link; return $this; }
    public function setStreamDescription($stream_description) { $this->stream_description = $stream_description; return $this; }
    public function setStreamPlatform($stream_platform) { $this->stream_platform = $stream_platform; return $this; }
    public function setPassword($password) { $this->password = $password; return $this; }
    public function setProfileImage($profile_image) { $this->profile_image = $profile_image; return $this; }
    public function setJoinDate($join_date) { $this->join_date = $join_date; return $this; }
    public function setIsBanned($is_banned) { $this->is_banned = $is_banned; return $this; }
    public function setBanType($ban_type) { $this->ban_type = $ban_type; return $this; }
    public function setBanReason($ban_reason) { $this->ban_reason = $ban_reason; return $this; }
    public function setBannedAt($banned_at) { $this->banned_at = $banned_at; return $this; }
    public function setBannedUntil($banned_until) { $this->banned_until = $banned_until; return $this; }
    public function setBannedBy($banned_by) { $this->banned_by = $banned_by; return $this; }

    // Méthodes utilitaires
    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAge() {
        if ($this->birthdate) {
            $birthdate = new DateTime($this->birthdate);
            $today = new DateTime();
            return $today->diff($birthdate)->y;
        }
        return null;
    }

    public function isStreamer() {
        return $this->role === 'streamer';
    }

    public function getFormattedJoinDate() {
        return $this->join_date ? date('d/m/Y', strtotime($this->join_date)) : 'N/A';
    }

    /**
     * Vérifie si l'utilisateur est actuellement banni
     * @return bool True si banni, False sinon
     */
    public function isBanned() {
        if (!$this->is_banned) {
            return false;
        }

        // Si bannissement permanent, toujours banni
        if ($this->ban_type === 'permanent') {
            return true;
        }

        // Si soft ban, vérifier la date d'expiration
        if ($this->ban_type === 'soft' && $this->banned_until) {
            $now = new DateTime();
            $until = new DateTime($this->banned_until);
            return $now < $until;
        }

        return false;
    }

    /**
     * Obtient le statut du bannissement formaté
     * @return string Statut: "Actif", "Banni définitivement", "Suspendu jusqu'au XX/XX/XXXX"
     */
    public function getBanStatus() {
        if (!$this->isBanned()) {
            return "Actif";
        }

        if ($this->ban_type === 'permanent') {
            return "Banni définitivement";
        }

        if ($this->ban_type === 'soft' && $this->banned_until) {
            return "Suspendu jusqu'au " . date('d/m/Y H:i', strtotime($this->banned_until));
        }

        return "Statut inconnu";
    }
}
?>
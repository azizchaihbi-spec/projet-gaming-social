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
}
?>
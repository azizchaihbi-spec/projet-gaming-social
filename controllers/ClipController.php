<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/clip.php';

class ClipController {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    /**
     * Get all clips
     */
    public function listClips() {
        $query = "SELECT c.*, s.titre as stream_titre, st.pseudo as streamer_pseudo 
                  FROM clip c 
                  LEFT JOIN stream s ON c.id_stream = s.id_stream 
                  LEFT JOIN streamer st ON s.id_streamer = st.id_user 
                  ORDER BY c.date_creation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get clips for a specific stream
     */
    public function getClipsByStream($id_stream) {
        $query = "SELECT * FROM clip WHERE id_stream = ? ORDER BY date_creation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_stream]);
        return $stmt->fetchAll();
    }

    /**
     * Get top clips (most viewed/liked)
     */
    public function getTopClips($limit = 6) {
        $limit = (int)$limit;
        $query = "SELECT c.*, s.titre as stream_titre, st.pseudo as streamer_pseudo 
                  FROM clip c 
                  LEFT JOIN stream s ON c.id_stream = s.id_stream 
                  LEFT JOIN streamer st ON s.id_streamer = st.id_user 
                  ORDER BY (c.nb_vues + c.nb_likes * 5) DESC 
                  LIMIT " . $limit;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get clip by ID
     */
    public function getClipById($id_clip) {
        $query = "SELECT c.*, s.titre as stream_titre, st.pseudo as streamer_pseudo 
                  FROM clip c 
                  LEFT JOIN stream s ON c.id_stream = s.id_stream 
                  LEFT JOIN streamer st ON s.id_streamer = st.id_user 
                  WHERE c.id_clip = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_clip]);
        return $stmt->fetch();
    }

    /**
     * Add a new clip
     */
    public function addClip(Clip $clip) {
        $query = "INSERT INTO clip (id_stream, titre, description, url_video, date_creation, nb_vues, nb_likes) 
                  VALUES (?, ?, ?, ?, NOW(), 0, 0)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $clip->getIdStream(),
            $clip->getTitre(),
            $clip->getDescription(),
            $clip->getUrlVideo()
        ]);
    }

    /**
     * Update a clip
     */
    public function updateClip(Clip $clip, $id_clip) {
        $query = "UPDATE clip SET titre = ?, description = ?, url_video = ? WHERE id_clip = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $clip->getTitre(),
            $clip->getDescription(),
            $clip->getUrlVideo(),
            $id_clip
        ]);
    }

    /**
     * Delete a clip
     */
    public function deleteClip($id_clip) {
        $query = "DELETE FROM clip WHERE id_clip = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_clip]);
    }

    /**
     * Increment views for a clip
     */
    public function incrementViews($id_clip) {
        $query = "UPDATE clip SET nb_vues = nb_vues + 1 WHERE id_clip = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_clip]);
    }

    /**
     * Increment likes for a clip
     */
    public function incrementLikes($id_clip) {
        $query = "UPDATE clip SET nb_likes = nb_likes + 1 WHERE id_clip = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_clip]);
    }
}

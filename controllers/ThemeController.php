<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/theme.php';

class ThemeController {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    /**
     * Get all themes
     */
    public function listThemes() {
        $query = "SELECT * FROM theme ORDER BY nom_theme ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get theme by ID
     */
    public function getThemeById($id_theme) {
        $query = "SELECT * FROM theme WHERE id_theme = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_theme]);
        return $stmt->fetch();
    }

    /**
     * Get theme by name
     */
    public function getThemeByName($nom_theme) {
        $query = "SELECT * FROM theme WHERE nom_theme = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$nom_theme]);
        return $stmt->fetch();
    }

    /**
     * Add a new theme
     */
    public function addTheme(Theme $theme) {
        $query = "INSERT INTO theme (nom_theme, description, image_url, icon_url, couleur) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $theme->getNomTheme(),
            $theme->getDescription(),
            $theme->getImageUrl(),
            $theme->getIconUrl(),
            $theme->getCouleur()
        ]);
    }

    /**
     * Update a theme
     */
    public function updateTheme(Theme $theme, $id_theme) {
        $query = "UPDATE theme SET nom_theme = ?, description = ?, image_url = ?, icon_url = ?, couleur = ? 
                  WHERE id_theme = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $theme->getNomTheme(),
            $theme->getDescription(),
            $theme->getImageUrl(),
            $theme->getIconUrl(),
            $theme->getCouleur(),
            $id_theme
        ]);
    }

    /**
     * Delete a theme
     */
    public function deleteTheme($id_theme) {
        $query = "DELETE FROM theme WHERE id_theme = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id_theme]);
    }

    /**
     * Get streams by theme
     */
    public function getStreamsByTheme($id_theme) {
        $query = "SELECT s.*, t.nom_theme, st.pseudo as streamer_pseudo 
                  FROM stream s 
                  LEFT JOIN theme t ON s.id_theme = t.id_theme 
                  LEFT JOIN streamer st ON s.id_streamer = st.id_user 
                  WHERE s.id_theme = ? 
                  ORDER BY s.date_creation DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_theme]);
        return $stmt->fetchAll();
    }

    /**
     * Count streams by theme
     */
    public function countStreamsByTheme($id_theme) {
        $query = "SELECT COUNT(*) as total FROM stream WHERE id_theme = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id_theme]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}

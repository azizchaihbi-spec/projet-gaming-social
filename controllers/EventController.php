<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/event.php';

class EventController {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function listThemes(): array {
        try {
            $stmt = $this->db->query("SELECT nom_theme AS nom_theme FROM theme ORDER BY nom_theme ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Fallback for schemas that use `nom` instead of `nom_theme`
            $stmt = $this->db->query("SELECT nom AS nom_theme FROM theme ORDER BY nom ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function listEvents(): array {
        $stmt = $this->db->query("SELECT * FROM evenement ORDER BY date_debut DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM evenement WHERE id_evenement = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function addEvent(Event $event): bool {
        $sql = "INSERT INTO evenement (titre, theme, banner_url, description, date_debut, date_fin, objectif) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $event->getTitre(),
            $event->getTheme(),
            $event->getBannerUrl(),
            $event->getDescription(),
            $event->getDateDebut()?->format('Y-m-d'),
            $event->getDateFin()?->format('Y-m-d'),
            $event->getObjectif()
        ]);
        
        // Notification Discord
        if ($result) {
            require_once __DIR__ . '/DiscordController.php';
            $eventData = [
                'titre' => $event->getTitre(),
                'theme' => $event->getTheme(),
                'date_debut' => $event->getDateDebut()?->format('Y-m-d H:i:s'),
                'objectif' => $event->getObjectif()
            ];
            DiscordController::notifyNewEvent($eventData);
        }
        
        return $result;
    }

    public function updateEvent(Event $event, int $id): bool {
        $sql = "UPDATE evenement SET titre = ?, theme = ?, banner_url = ?, description = ?, date_debut = ?, date_fin = ?, objectif = ? 
                WHERE id_evenement = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $event->getTitre(),
            $event->getTheme(),
            $event->getBannerUrl(),
            $event->getDescription(),
            $event->getDateDebut()?->format('Y-m-d'),
            $event->getDateFin()?->format('Y-m-d'),
            $event->getObjectif(),
            $id
        ]);
    }

    public function deleteEvent(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM evenement WHERE id_evenement = ?");
        return $stmt->execute([$id]);   
    }
}
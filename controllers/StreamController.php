<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/stream.php';
require_once __DIR__ . '/../models/clip.php';

class StreamController {
    private $db;

    public function __construct() {
        $this->db = Config::getConnexion();
    }

    public function listStreams(): array {
        $sql = "SELECT s.*, st.pseudo as streamer_pseudo, st.plateforme as streamer_platform, u.email as streamer_email,
                (SELECT COUNT(*) FROM clip c WHERE c.id_stream = s.id_stream) as clip_count
                FROM stream s
                LEFT JOIN streamer st ON s.id_streamer = st.id_user
                LEFT JOIN utilisateur u ON st.id_user = u.id_user
                ORDER BY s.date_debut DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStreamById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM stream WHERE id_stream = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function addStream(Stream $stream, ?int $id_streamer = null): bool {
        $sql = "INSERT INTO stream (id_streamer, id_association, titre, plateforme, url, date_debut, date_fin, statut, don_total, nb_commentaires, nb_likes, nb_dislikes, nb_vues, nb_notification) 
                VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $id_streamer,
            $stream->getTitre(),
            $stream->getPlateforme(),
            $stream->getUrl(),
            $stream->getDateDebut()?->format('Y-m-d H:i:s'),
            $stream->getDateFin()?->format('Y-m-d H:i:s'),
            $stream->getStatut(),
            $stream->getDonTotal(),
            $stream->getNbCommentaires() ?? 0,
            $stream->getNbLikes() ?? 0,
            $stream->getNbDislikes() ?? 0,
            $stream->getNbVues() ?? 0,
            $stream->getNbNotification() ?? 0
        ]);
    }

    public function updateStream(Stream $stream, int $id, ?int $id_streamer = null): bool {
        $sql = "UPDATE stream SET id_streamer = ?, titre = ?, plateforme = ?, url = ?, date_debut = ?, date_fin = ?, statut = ?, don_total = ?, nb_commentaires = ?, nb_likes = ?, nb_dislikes = ?, nb_vues = ?, nb_notification = ?
                WHERE id_stream = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $id_streamer,
            $stream->getTitre(),
            $stream->getPlateforme(),
            $stream->getUrl(),
            $stream->getDateDebut()?->format('Y-m-d H:i:s'),
            $stream->getDateFin()?->format('Y-m-d H:i:s'),
            $stream->getStatut(),
            $stream->getDonTotal(),
            $stream->getNbCommentaires() ?? 0,
            $stream->getNbLikes() ?? 0,
            $stream->getNbDislikes() ?? 0,
            $stream->getNbVues() ?? 0,
            $stream->getNbNotification() ?? 0,
            $id
        ]);
    }

    public function deleteStream(int $id): bool {
        // First delete all associated clips
        $stmt = $this->db->prepare("DELETE FROM clip WHERE id_stream = ?");
        $stmt->execute([$id]);
        
        // Then delete the stream
        $stmt = $this->db->prepare("DELETE FROM stream WHERE id_stream = ?");
        return $stmt->execute([$id]);
    }

    public function updateStatus(int $id, string $statut): bool {
        $stmt = $this->db->prepare("UPDATE stream SET statut = ? WHERE id_stream = ?");
        $result = $stmt->execute([$statut, $id]);
        
        // Notification Discord si stream passe en "live"
        if ($result && $statut === 'live') {
            require_once __DIR__ . '/DiscordController.php';
            $stream = $this->getStreamById($id);
            if ($stream) {
                DiscordController::notifyStreamLive($stream);
            }
        }
        
        return $result;
    }

    public function incrementDonation(int $id, int $amount): bool {
        $stmt = $this->db->prepare("UPDATE stream SET don_total = don_total + ? WHERE id_stream = ?");
        return $stmt->execute([$amount, $id]);
    }

    public function getStatusOptions(): array {
        return [
            'planifie' => 'Planifié',
            'en_cours' => 'En cours',
            'termine'  => 'Terminé',
            'annule'   => 'Annulé'
        ];
    }

    public function saveStreamerThumb(int $id_streamer, array $file): bool {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        if (!$id_streamer) {
            throw new RuntimeException('Veuillez choisir un streamer avant de téléverser une vignette.');
        }
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            throw new RuntimeException('Format d\'image non supporté. Utilisez JPG, PNG ou WEBP.');
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException('Image trop volumineuse (max 2MB).');
        }
        $ext = $allowed[$mime];
        $dir = realpath(__DIR__ . '/../views/frontoffice/assets/images/streamers');
        if ($dir === false) {
            $dir = __DIR__ . '/../views/frontoffice/assets/images/streamers';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }
        $dest = $dir . '/' . $id_streamer . '.' . $ext;
        foreach (['jpg','png','webp'] as $oldExt) {
            $old = $dir . '/' . $id_streamer . '.' . $oldExt;
            if (file_exists($old) && $old !== $dest) {
                @unlink($old);
            }
        }
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Échec de l\'upload de la vignette.');
        }
        return true;
    }

    public function saveStreamThumb(int $id_stream, array $file): bool {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }
        if (!$id_stream) {
            throw new RuntimeException('ID du stream invalide.');
        }
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            throw new RuntimeException('Format d\'image non supporté. Utilisez JPG, PNG ou WEBP.');
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new RuntimeException('Vignette trop volumineuse (max 2MB).');
        }
        $ext = $allowed[$mime];
        $dir = realpath(__DIR__ . '/../views/frontoffice/assets/images/streams');
        if ($dir === false) {
            $dir = __DIR__ . '/../views/frontoffice/assets/images/streams';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }
        $dest = $dir . '/' . $id_stream . '.' . $ext;
        foreach (['jpg','png','webp'] as $oldExt) {
            $old = $dir . '/' . $id_stream . '.' . $oldExt;
            if (file_exists($old) && $old !== $dest) {
                @unlink($old);
            }
        }
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Échec de l\'upload de la vignette du stream.');
        }
        return true;
    }

    public function ajouterCommentaire(int $id_stream): bool {
        $stmt = $this->db->prepare("UPDATE stream SET nb_commentaires = nb_commentaires + 1 WHERE id_stream = ?");
        return $stmt->execute([$id_stream]);
    }

    public function ajouterLike(int $id_stream): bool {
        $stmt = $this->db->prepare("UPDATE stream SET nb_likes = nb_likes + 1 WHERE id_stream = ?");
        return $stmt->execute([$id_stream]);
    }

    public function ajouterDislike(int $id_stream): bool {
        $stmt = $this->db->prepare("UPDATE stream SET nb_dislikes = nb_dislikes + 1 WHERE id_stream = ?");
        return $stmt->execute([$id_stream]);
    }

    public function incrementerVues(int $id_stream, int $nb = 1): bool {
        $stmt = $this->db->prepare("UPDATE stream SET nb_vues = nb_vues + ? WHERE id_stream = ?");
        return $stmt->execute([$nb, $id_stream]);
    }

    public function ajouterNotification(int $id_stream): bool {
        $stmt = $this->db->prepare("UPDATE stream SET nb_notification = nb_notification + 1 WHERE id_stream = ?");
        return $stmt->execute([$id_stream]);
    }

    public function getStatistiques(int $id_stream): ?array {
        $stmt = $this->db->prepare("
            SELECT nb_commentaires, nb_likes, nb_dislikes, nb_vues, nb_notification,
                   (nb_likes + nb_commentaires - nb_dislikes) as engagement_total,
                   CASE WHEN nb_vues > 0 THEN ROUND((nb_likes + nb_commentaires) / nb_vues * 100, 2) ELSE 0 END as taux_engagement
            FROM stream WHERE id_stream = ?
        ");
        $stmt->execute([$id_stream]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getStreamerStreams(): array {
        $sql = "SELECT st.id_user,
                       st.pseudo as streamer_pseudo,
                       st.plateforme as streamer_plateforme,
                       u.email as streamer_email,
                       COUNT(s.id_stream) as nb_streams,
                       SUM(s.don_total) as total_dons,
                       SUM(s.nb_vues) as total_vues,
                       SUM(s.nb_likes) as total_likes,
                       SUM(s.nb_commentaires) as total_commentaires
                FROM streamer st
                LEFT JOIN utilisateur u ON st.id_user = u.id_user
                LEFT JOIN stream s ON st.id_user = s.id_streamer
                GROUP BY st.id_user
                ORDER BY nb_streams DESC, total_dons DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStreamsByStreamer(int $id_streamer): array {
        $sql = "SELECT s.*,
                       st.pseudo as streamer_pseudo,
                       u.email as streamer_email
                FROM stream s
                LEFT JOIN streamer st ON s.id_streamer = st.id_user
                LEFT JOIN utilisateur u ON st.id_user = u.id_user
                WHERE s.id_streamer = ?
                ORDER BY s.date_debut DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_streamer]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---- Clip methods merged from ClipController ----
    public function listClips(): array {
        $sql = "SELECT c.*, s.titre as stream_titre, st.pseudo as streamer_pseudo
                FROM clip c
                LEFT JOIN stream s ON c.id_stream = s.id_stream
                LEFT JOIN streamer st ON s.id_streamer = st.id_user
                ORDER BY c.date_creation DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClipsByStream(int $id_stream): array {
        $stmt = $this->db->prepare("SELECT * FROM clip WHERE id_stream = ? ORDER BY date_creation DESC");
        $stmt->execute([$id_stream]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopClips(int $limit = 6): array {
        $limit = max(1, (int)$limit);
        $sql = "SELECT c.*, s.titre as stream_titre, st.pseudo as streamer_pseudo
                FROM clip c
                LEFT JOIN stream s ON c.id_stream = s.id_stream
                LEFT JOIN streamer st ON s.id_streamer = st.id_user
                ORDER BY (c.nb_vues + c.nb_likes * 5) DESC
                LIMIT $limit";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClipById(int $id_clip): ?array {
        $stmt = $this->db->prepare("SELECT c.*, s.titre as stream_titre, st.pseudo as streamer_pseudo
                                     FROM clip c
                                     LEFT JOIN stream s ON c.id_stream = s.id_stream
                                     LEFT JOIN streamer st ON s.id_streamer = st.id_user
                                     WHERE c.id_clip = ?");
        $stmt->execute([$id_clip]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function addClip(Clip $clip): bool {
        $stmt = $this->db->prepare("INSERT INTO clip (id_stream, titre, description, url_video, date_creation, nb_vues, nb_likes)
                                     VALUES (?, ?, ?, ?, NOW(), 0, 0)");
        return $stmt->execute([
            $clip->getIdStream(),
            $clip->getTitre(),
            $clip->getDescription(),
            $clip->getUrlVideo()
        ]);
    }

    public function updateClip(Clip $clip, int $id_clip): bool {
        $stmt = $this->db->prepare("UPDATE clip SET titre = ?, description = ?, url_video = ? WHERE id_clip = ?");
        return $stmt->execute([
            $clip->getTitre(),
            $clip->getDescription(),
            $clip->getUrlVideo(),
            $id_clip
        ]);
    }

    public function deleteClip(int $id_clip): bool {
        $stmt = $this->db->prepare("DELETE FROM clip WHERE id_clip = ?");
        return $stmt->execute([$id_clip]);
    }

    public function incrementViews(int $id_clip): bool {
        $stmt = $this->db->prepare("UPDATE clip SET nb_vues = nb_vues + 1 WHERE id_clip = ?");
        return $stmt->execute([$id_clip]);
    }

    public function incrementLikes(int $id_clip): bool {
        $stmt = $this->db->prepare("UPDATE clip SET nb_likes = nb_likes + 1 WHERE id_clip = ?");
        return $stmt->execute([$id_clip]);
    }
}

<?php
class PublicationModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllPublications($filter = 'all') {
        try {
            if ($filter === 'all') {
                $sql = "SELECT p.*, f.nom as forum_nom, u.prenom 
                        FROM publication p 
                        JOIN forum f ON p.id_forum = f.id_forum 
                        JOIN utilisateur u ON p.id_auteur = u.id_user 
                        WHERE p.supprimee = 0 
                        ORDER BY p.date_publication DESC";
                $stmt = $this->pdo->query($sql);
            } else {
                $sql = "SELECT p.*, f.nom as forum_nom, u.prenom 
                        FROM publication p 
                        JOIN forum f ON p.id_forum = f.id_forum 
                        JOIN utilisateur u ON p.id_auteur = u.id_user 
                        WHERE f.nom = ? AND p.supprimee = 0 
                        ORDER BY p.date_publication DESC";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$filter]);
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur PublicationModel: " . $e->getMessage());
            return [];
        }
    }

    public function getForums() {
        try {
            $sql = "SELECT * FROM forum ORDER BY nom";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getForums: " . $e->getMessage());
            return [];
        }
    }

    public function createPublication($idAuteur, $titre, $contenu, $idForum, $image = null) {
        try {
            $sql = "INSERT INTO publication (id_auteur, id_forum, titre, contenu, image, date_publication) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idAuteur, $idForum, $titre, $contenu, $image]);
        } catch (PDOException $e) {
            error_log("Erreur création publication: " . $e->getMessage());
            return false;
        }
    }

    public function likePublication($idPublication) {
        try {
            $sql = "UPDATE publication SET likes = likes + 1 WHERE id_publication = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idPublication]);
        } catch (PDOException $e) {
            error_log("Erreur like: " . $e->getMessage());
            return false;
        }
    }

    public function dislikePublication($idPublication) {
        try {
            $sql = "UPDATE publication SET dislikes = dislikes + 1 WHERE id_publication = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idPublication]);
        } catch (PDOException $e) {
            error_log("Erreur dislike: " . $e->getMessage());
            return false;
        }
    }

    public function updatePublication($idPublication, $titre, $contenu) {
    try {
        $sql = "UPDATE publication SET titre = ?, contenu = ? WHERE id_publication = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titre, $contenu, $idPublication]);
    } catch (PDOException $e) {
        error_log("Erreur update publication: " . $e->getMessage());
        return false;
    }
}
public function deletePublication($idPublication) {
    try {
        // Supprime d'abord les réponses associées
        $sql1 = "DELETE FROM reponse WHERE id_publication = ?";
        $stmt1 = $this->pdo->prepare($sql1);
        $stmt1->execute([$idPublication]);
        
        // Puis supprime la publication
        $sql2 = "DELETE FROM publication WHERE id_publication = ?";
        $stmt2 = $this->pdo->prepare($sql2);
        return $stmt2->execute([$idPublication]);
    } catch (PDOException $e) {
        error_log("Erreur delete publication: " . $e->getMessage());
        return false;
    }

}
// Ajoutez ces méthodes à la classe PublicationModel existante

public function getTotalPublications() {
    try {
        $sql = "SELECT COUNT(*) as total FROM publication WHERE supprimee = 0";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log("Erreur count publications: " . $e->getMessage());
        return 0;
    }
}

public function getAllPublicationsForAdmin($search = '') {
    try {
        $sql = "SELECT p.*, f.nom as forum_nom, u.prenom, u.nom as auteur_nom,
                       (SELECT COUNT(*) FROM reponse r WHERE r.id_publication = p.id_publication AND r.supprimee = 0) as nb_reponses
                FROM publication p 
                JOIN forum f ON p.id_forum = f.id_forum 
                JOIN utilisateur u ON p.id_auteur = u.id_user 
                WHERE p.supprimee = 0";
        
        if (!empty($search)) {
            $sql .= " AND (p.titre LIKE ? OR p.contenu LIKE ? OR u.prenom LIKE ? OR u.nom LIKE ?)";
            $searchTerm = "%$search%";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        } else {
            $stmt = $this->pdo->query($sql);
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur publications admin: " . $e->getMessage());
        return [];
    }
}
public function afficherPublicationsParForum($idForum) {
    try {
        $sql = "SELECT p.id_publication,
                       p.titre,
                       p.contenu,
                       p.image,
                       p.date_publication,
                       p.likes,
                       f.nom as forum_nom,
                       f.description as forum_description,
                       f.couleur as forum_couleur,
                       u.prenom,
                       u.nom as auteur_nom,
                       (SELECT COUNT(*) 
                        FROM reponse r 
                        WHERE r.id_publication = p.id_publication 
                          AND r.supprimee = 0) as nb_reponses
                FROM publication p
                INNER JOIN forum f ON p.id_forum = f.id_forum
                INNER JOIN utilisateur u ON p.id_auteur = u.id_user
                WHERE p.id_forum = ? 
                  AND p.supprimee = 0
                ORDER BY p.date_publication DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idForum]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur afficherPublicationsParForum: " . $e->getMessage());
        return [];
    }
}

/**
 * BONUS : Statistiques par forum avec JOINTURE et GROUP BY
 */
public function getStatistiquesParForum() {
    try {
        $sql = "SELECT f.id_forum,
                       f.nom as forum_nom,
                       f.couleur,
                       f.description,
                       COUNT(p.id_publication) as nb_publications,
                       IFNULL(SUM(p.likes), 0) as total_likes
                FROM forum f
                LEFT JOIN publication p ON f.id_forum = p.id_forum 
                                        AND p.supprimee = 0
                GROUP BY f.id_forum, f.nom, f.couleur, f.description
                ORDER BY nb_publications DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur getStatistiquesParForum: " . $e->getMessage());
        return [];
    }
}
}
?>
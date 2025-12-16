<?php
class ReponseModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getReponsesByPublication($idPublication) {
        try {
            $sql = "SELECT r.*, u.first_name as prenom 
                    FROM reponse r 
                    JOIN users u ON r.id_auteur = u.id 
                    WHERE r.id_publication = ? AND r.supprimee = 0 
                    ORDER BY r.date_reponse ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$idPublication]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur récupération réponses: " . $e->getMessage());
            return [];
        }
    }

    public function createReponse($idPublication, $idAuteur, $contenu) {
        try {
            $sql = "INSERT INTO reponse (id_publication, id_auteur, contenu, date_reponse) 
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$idPublication, $idAuteur, $contenu]);
        } catch (PDOException $e) {
            error_log("Erreur création réponse: " . $e->getMessage());
            return false;
        }
    }
    // Ajoutez cette méthode à la classe ReponseModel existante

public function getTotalReponses() {
    try {
        $sql = "SELECT COUNT(*) as total FROM reponse WHERE supprimee = 0";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log("Erreur count réponses: " . $e->getMessage());
        return 0;
    }
}
}
?>
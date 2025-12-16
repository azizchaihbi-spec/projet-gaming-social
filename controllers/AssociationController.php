<?php
require_once '../../config/config.php';

class AssociationController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = config::getConnexion();
    }
    
    public function list() {
        try {
            $stmt = $this->pdo->query("
                SELECT a.*, 
                       COALESCE(SUM(d.montant), 0) as total_dons_reel,
                       COUNT(d.id_don) as nombre_donateurs
                FROM association a 
                LEFT JOIN don d ON a.id_association = d.id_association 
                GROUP BY a.id_association 
                ORDER BY a.name ASC
            ");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des associations: " . $e->getMessage());
            return [];
        }
    }
    
    public function add($name, $description) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO association (name, description, date_creation) 
                VALUES (?, ?, NOW())
            ");
            return $stmt->execute([$name, $description]);
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout de l'association: " . $e->getMessage());
            return false;
        }
    }
    
    public function update($id, $name, $description) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE association 
                SET name = ?, description = ? 
                WHERE id_association = ?
            ");
            return $stmt->execute([$name, $description, $id]);
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour de l'association: " . $e->getMessage());
            return false;
        }
    }
    
    public function delete($id) {
        try {
            // Vérifier s'il y a des dons liés à cette association
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM don WHERE id_association = ?");
            $stmt->execute([$id]);
            $donCount = $stmt->fetchColumn();
            
            if ($donCount > 0) {
                return ['success' => false, 'message' => 'Impossible de supprimer: cette association a des dons associés'];
            }
            
            // Vérifier s'il y a des challenges liés à cette association
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM challenge WHERE id_association = ?");
            $stmt->execute([$id]);
            $challengeCount = $stmt->fetchColumn();
            
            if ($challengeCount > 0) {
                return ['success' => false, 'message' => 'Impossible de supprimer: cette association a des challenges associés'];
            }
            
            // Supprimer l'association
            $stmt = $this->pdo->prepare("DELETE FROM association WHERE id_association = ?");
            $result = $stmt->execute([$id]);
            
            return ['success' => $result, 'message' => $result ? 'Association supprimée avec succès' : 'Erreur lors de la suppression'];
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression de l'association: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la suppression: ' . $e->getMessage()];
        }
    }
}
?>
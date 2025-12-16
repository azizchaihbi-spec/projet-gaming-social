<?php
require_once '../../config/config.php';        // Chemin correct depuis controllers/
require_once '../../models/Don.php';

class DonController {

// Dans controllers/DonController.php
public function list() {
    $query = "SELECT d.*, a.name AS association_nom 
              FROM don d 
              LEFT JOIN association a ON d.id_association = a.id_association 
              ORDER BY d.date_don DESC";
    $stmt = config::getConnexion()->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function add(Don $don) {
        $sql = "INSERT INTO don (id_association, prenom, nom, email, montant, date_don) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([
            $don->getIdAssociation(),
            $don->getPrenom(),
            $don->getNom(),
            $don->getEmail(),
            $don->getMontant()
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM don WHERE id_don = ?";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([$id]);
    }

    public function getOne($id) {
        $sql = "SELECT * FROM don WHERE id_don = ?";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([$id]);
        $data = $q->fetch();

        if ($data) {
            return new Don(
                $data['id_don'],
                $data['id_association'],
                $data['prenom'],
                $data['nom'],
                $data['email'],
                $data['montant'],
                new DateTime($data['date_don'])
            );
        }
        return null;
    }

    public function update(Don $don) {
        $sql = "UPDATE don 
                SET id_association = ?, prenom = ?, nom = ?, email = ?, montant = ? 
                WHERE id_don = ?";
        $db = config::getConnexion();
        $q = $db->prepare($sql);
        $q->execute([
            $don->getIdAssociation(),
            $don->getPrenom(),
            $don->getNom(),
            $don->getEmail(),
            $don->getMontant(),
            $don->getIdDon()
        ]);
    }
}
?>
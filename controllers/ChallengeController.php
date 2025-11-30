<?php
require_once '../../config/db.php';
require_once '../../models/Challenge.php';

class ChallengeController {
    public function list() {
        $sql = "SELECT c.*, a.name AS association_nom 
                FROM challenge c 
                LEFT JOIN association a ON c.id_association = a.id_association 
                ORDER BY c.id_challenge DESC";
        return config::getConnexion()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(Challenge $c) {
        $sql = "INSERT INTO challenge (id_association, name, objectif, recompense, progression) 
                VALUES (?, ?, ?, ?, 0.00)";
        $q = config::getConnexion()->prepare($sql);
        $q->execute([
            $c->getIdAssociation(),
            $c->getName(),
            $c->getObjectif(),
            $c->getRecompense()
        ]);
    }

    public function getOne(int $id): ?Challenge {
        $sql = "SELECT * FROM challenge WHERE id_challenge = ?";
        $q = config::getConnexion()->prepare($sql);
        $q->execute([$id]);
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if (!$data) return null;
        return new Challenge(
            $data['id_challenge'],
            $data['id_association'],
            $data['name'],
            $data['objectif'],
            $data['recompense'],
            $data['progression']
        );
    }

    public function update(Challenge $c) {
        $sql = "UPDATE challenge SET id_association=?, name=?, objectif=?, recompense=? WHERE id_challenge=?";
        $q = config::getConnexion()->prepare($sql);
        $q->execute([
            $c->getIdAssociation(),
            $c->getName(),
            $c->getObjectif(),
            $c->getRecompense(),
            $c->getIdChallenge()
        ]);
    }

    public function delete(int $id) {
        $sql = "DELETE FROM challenge WHERE id_challenge = ?";
        $q = config::getConnexion()->prepare($sql);
        $q->execute([$id]);
    }
}
?>
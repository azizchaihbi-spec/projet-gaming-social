<?php
require_once '../../config/db.php';
require_once '../../models/Challenge.php';

class ChallengeController {
    
    // Liste tous les challenges avec jointure
    public function list() {
        $sql = "SELECT c.*, a.name AS association_nom 
                FROM challenge c 
                LEFT JOIN association a ON c.id_association = a.id_association 
                ORDER BY c.id_challenge DESC";
        return config::getConnexion()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🆕 NOUVELLE MÉTHODE - Récupérer challenges par association (JOINTURE)
    public function getChallengesByAssociation(int $idAssociation) {
        $sql = "SELECT 
                    c.id_challenge,
                    c.id_association,
                    c.name,
                    c.objectif,
                    c.progression,
                    c.recompense,
                    a.name AS association_nom,
                    a.description AS association_description
                FROM challenge c
                INNER JOIN association a ON c.id_association = a.id_association
                WHERE c.id_association = ?
                ORDER BY c.id_challenge DESC";
        
        $stmt = config::getConnexion()->prepare($sql);
        $stmt->execute([$idAssociation]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🆕 NOUVELLE MÉTHODE - Statistiques globales par association
    public function getStatsByAssociation(int $idAssociation) {
        $sql = "SELECT 
                    COUNT(c.id_challenge) as nb_challenges,
                    SUM(c.objectif) as total_objectif,
                    SUM(c.progression) as total_progression,
                    AVG((c.progression / c.objectif) * 100) as pourcentage_moyen,
                    a.name as association_nom
                FROM challenge c
                INNER JOIN association a ON c.id_association = a.id_association
                WHERE c.id_association = ?
                GROUP BY c.id_association, a.name";
        
        $stmt = config::getConnexion()->prepare($sql);
        $stmt->execute([$idAssociation]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🆕 NOUVELLE MÉTHODE - Top 5 challenges les plus performants
    public function getTopChallenges(int $limit = 5) {
        $sql = "SELECT 
                    c.*,
                    a.name AS association_nom,
                    (c.progression / c.objectif * 100) AS pourcentage
                FROM challenge c
                INNER JOIN association a ON c.id_association = a.id_association
                ORDER BY pourcentage DESC
                LIMIT ?";
        
        $stmt = config::getConnexion()->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🆕 NOUVELLE MÉTHODE - Challenges par statut
    public function getChallengesByStatus(string $status = 'all') {
        $sql = "SELECT 
                    c.*,
                    a.name AS association_nom,
                    (c.progression / c.objectif * 100) AS pourcentage
                FROM challenge c
                INNER JOIN association a ON c.id_association = a.id_association";
        
        switch ($status) {
            case 'completed':
                $sql .= " WHERE (c.progression / c.objectif * 100) >= 100";
                break;
            case 'in_progress':
                $sql .= " WHERE (c.progression / c.objectif * 100) < 100 AND (c.progression / c.objectif * 100) > 0";
                break;
            case 'not_started':
                $sql .= " WHERE c.progression = 0";
                break;
        }
        
        $sql .= " ORDER BY c.id_challenge DESC";
        
        return config::getConnexion()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter un challenge
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

    // Récupérer un challenge
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

    // Mettre à jour un challenge
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

    // 🆕 NOUVELLE MÉTHODE - Mettre à jour la progression
    public function updateProgression(int $idChallenge, float $progression) {
        $sql = "UPDATE challenge SET progression = ? WHERE id_challenge = ?";
        $stmt = config::getConnexion()->prepare($sql);
        return $stmt->execute([$progression, $idChallenge]);
    }

    // Supprimer un challenge
    public function delete(int $id) {
        $sql = "DELETE FROM challenge WHERE id_challenge = ?";
        $q = config::getConnexion()->prepare($sql);
        $q->execute([$id]);
    }
}
?>
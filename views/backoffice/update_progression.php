<?php
require_once '../../config/db.php';
require_once '../../controllers/ChallengeController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $progression = isset($_POST['progression']) ? floatval($_POST['progression']) : 0;

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            exit;
        }

        if ($progression < 0) {
            echo json_encode(['success' => false, 'message' => 'La progression ne peut pas être négative']);
            exit;
        }

        $challengeC = new ChallengeController();
        
        // Récupérer le challenge pour vérifier l'objectif
        $challenge = $challengeC->getOne($id);
        
        if (!$challenge) {
            echo json_encode(['success' => false, 'message' => 'Challenge introuvable']);
            exit;
        }

        // Mettre à jour la progression
        $challengeC->updateProgression($id, $progression);

        echo json_encode([
            'success' => true,
            'message' => 'Progression mise à jour avec succès',
            'progression' => $progression,
            'objectif' => $challenge->getObjectif(),
            'pourcentage' => min(100, round(($progression / $challenge->getObjectif()) * 100, 2))
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>
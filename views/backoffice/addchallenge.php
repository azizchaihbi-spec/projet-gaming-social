<?php
require_once '../../config/db.php';
require_once '../../controllers/ChallengeController.php';
require_once '../../models/Challenge.php';

// Définir le header pour la réponse JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validation des données
        $id_association = isset($_POST['challenge-assoc']) ? intval($_POST['challenge-assoc']) : 0;
        $name = isset($_POST['defi']) ? trim($_POST['defi']) : '';
        $objectif = isset($_POST['objectif']) ? floatval($_POST['objectif']) : 0;
        $recompense = isset($_POST['recompense']) ? trim($_POST['recompense']) : '';

        // Vérifications
        if ($id_association <= 0) {
            echo json_encode(['success' => false, 'message' => 'Veuillez sélectionner une association']);
            exit;
        }

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Le défi est requis']);
            exit;
        }

        if ($objectif <= 0) {
            echo json_encode(['success' => false, 'message' => 'L\'objectif doit être supérieur à 0']);
            exit;
        }

        if (empty($recompense)) {
            echo json_encode(['success' => false, 'message' => 'La récompense est requise']);
            exit;
        }

        // Créer le challenge
        $challenge = new Challenge(
            null,
            $id_association,
            $name,
            $objectif,
            $recompense,
            0.00
        );

        $challengeC = new ChallengeController();
        $challengeC->add($challenge);

        echo json_encode([
            'success' => true, 
            'message' => "Challenge '{$name}' créé avec succès ! Objectif : {$objectif}€"
        ]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur serveur : ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>
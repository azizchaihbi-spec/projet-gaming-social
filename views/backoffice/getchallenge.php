<?php
// backoffice/components/getchallenge.php
require_once '../../config/db.php';
require_once '../../controllers/ChallengeController.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès interdit']);
    exit;
}

if (isset($_GET['id'])) {
    try {
        $challengeC = new ChallengeController();
        $challenge = $challengeC->getOne((int)$_GET['id']);
        
        if ($challenge) {
            $data = [
                'id_challenge' => $challenge->getIdChallenge(),
                'id_association' => $challenge->getIdAssociation(),
                'name' => $challenge->getName(),
                'objectif' => $challenge->getObjectif(),
                'progression' => $challenge->getProgression(),
                'recompense' => $challenge->getRecompense()
            ];
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Challenge non trouvé']);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
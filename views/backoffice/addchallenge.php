<?php
// backoffice/components/addchallenge.php
require_once '../../config/db.php';
require_once '../../controllers/ChallengeController.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /play to help/views/frontoffice/index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $challengeC = new ChallengeController();
        
        $challenge = new Challenge(
            null,
            $_POST['id_association'],
            $_POST['name'],
            (float)$_POST['objectif'],
            $_POST['recompense'] ?? '',
            (float)($_POST['progression'] ?? 0.00)
        );
        
        $id = $challengeC->add($challenge);
        
        header('Location: ../index.php?message=Challenge+créé+avec+succès');
        exit;
        
    } catch (Exception $e) {
        header('Location: ../index.php?error=' . urlencode('Erreur: ' . $e->getMessage()));
        exit;
    }
}
?>
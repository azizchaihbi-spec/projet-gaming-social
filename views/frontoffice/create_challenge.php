<?php
require_once 'config/db.php';
require_once 'models/Challenge.php';
require_once 'controllers/ChallengeController.php';

header('Content-Type: application/json');

if ($_POST && $_POST['name'] && $_POST['objectif'] && $_POST['id_association']) {
    $challenge = new Challenge(
        null,
        (int)$_POST['id_association'],
        $_POST['name'],
        (float)$_POST['objectif'],
        $_POST['recompense'] ?? 'Récompense surprise'
    );

    $controller = new ChallengeController();
    $controller->add($challenge);

    echo json_encode(['success' => true, 'name' => $_POST['name']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
}
?>
<?php
require_once '../../config/db.php';
require_once '../../controllers/ChallengeController.php';

$challengeC = new ChallengeController();

if (isset($_GET['id'])) {
    $challengeC->delete($_GET['id']);
}

header("Location: ../../views/backoffice/index.php?success=challenge_deleted");
exit;

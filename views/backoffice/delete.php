<?php
require_once '../../controllers/DonController.php';

if (!isset($_GET['id'])) {
    header('Location: indexsinda.php');
    exit;
}

$controller = new DonController();
$controller->delete($_GET['id']);

header('Location: indexsinda.php?deleted=1');
exit;
?>
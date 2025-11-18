<?php
require_once '../../controllers/DonController.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$controller = new DonController();
$controller->delete($_GET['id']);

header('Location: index.php?deleted=1');
exit;
?>
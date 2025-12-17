<?php
// Header commun BackOffice
// Calculer le chemin de base vers le dossier backoffice
$headerBasePath = dirname(__DIR__);
$currentDir = dirname($_SERVER['SCRIPT_FILENAME']);
$relativePath = '';

// Si on est dans un sous-dossier (events/, stream/, etc.), ajouter ../
if (strpos($currentDir, 'events') !== false || strpos($currentDir, 'stream') !== false) {
    $relativePath = '../';
}
?>
<nav class="bg-gray-900/80 backdrop-blur-lg border-b border-cyan-500/30 py-4 px-6">
    <div class="container mx-auto flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <span class="text-cyan-400 font-bold text-2xl font-orbitron">PLAY2HELP</span>
            <span class="text-gray-400">|</span>
            <a href="<?= $relativePath ?>index.php" class="px-4 py-2 rounded-lg font-semibold bg-cyan-700 hover:bg-cyan-600 text-white transition">Gestion Utilisateurs</a>
            <a href="<?= $relativePath ?>admin.php" class="px-4 py-2 rounded-lg font-semibold bg-purple-700 hover:bg-purple-600 text-white transition">Gestion Forum</a>
            <a href="<?= $relativePath ?>indexsinda.php" class="px-4 py-2 rounded-lg font-semibold bg-orange-700 hover:bg-orange-600 text-white transition">Gestion de Dons</a>
            <a href="<?= $relativePath ?>dashboard.php" class="px-4 py-2 rounded-lg font-semibold bg-emerald-700 hover:bg-emerald-600 text-white transition">Gestion Streams & Events</a>
        </div>
        <div class="flex items-center space-x-6">
            <a href="<?= $relativePath ?>../frontoffice/login.php" class="text-cyan-400 hover:text-cyan-300 transition">Site Principal</a>
        </div>
    </div>
</nav>

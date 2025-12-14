<?php
// Header commun FrontOffice
?>
<nav class="bg-gray-900/80 backdrop-blur-lg border-b border-cyan-500/30 py-4 px-6">
    <div class="container mx-auto flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <span class="text-cyan-400 font-bold text-2xl font-orbitron">PLAY2HELP</span>
            <span class="text-gray-400">|</span>
            <a href="index.php" class="px-4 py-2 rounded-lg font-semibold bg-cyan-700 hover:bg-cyan-600 text-white transition">Accueil</a>
            <a href="index.php?page=front" class="px-4 py-2 rounded-lg font-semibold bg-purple-700 hover:bg-purple-600 text-white transition">Forum Q&A</a>
        </div>
        <div class="flex items-center space-x-4">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="profile.php" class="px-4 py-2 rounded-lg font-semibold bg-emerald-700 hover:bg-emerald-600 text-white transition">Profil</a>
                <a href="logout.php" class="px-4 py-2 rounded-lg font-semibold bg-red-700 hover:bg-red-600 text-white transition">Déconnexion</a>
            <?php else: ?>
                <a href="register.php" class="px-4 py-2 rounded-lg font-semibold bg-emerald-700 hover:bg-emerald-600 text-white transition">Inscription</a>
                <a href="login.php" class="px-4 py-2 rounded-lg font-semibold bg-cyan-700 hover:bg-cyan-600 text-white transition">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

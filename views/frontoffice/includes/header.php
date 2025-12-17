<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour déterminer la classe active
function isActive($page) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return ($current_page === $page) ? 'active' : '';
}
?>

<!-- HEADER -->
<header id="mainHeader" class="header-area header-sticky">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <nav class="main-nav d-flex align-items-center justify-content-between">
                    <a href="Accueil.php" class="logo">
                        <img src="assets/images/logooo.png" alt="Play to Help - Manette Solidaire" height="50">
                    </a>
                    <div class="search-input" style="flex-grow: 1; max-width: 400px; margin-left: 20px;">
                        <form id="search" action="search.php" class="d-flex align-items-center">
                            <input type="text" class="form-control" placeholder="Rechercher association, don ou challenge..." name="q" />
                            <button type="submit" style="background:none; border:none; color:#666; font-size:1.2em; cursor:pointer;">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                <span class="sr-only">Rechercher</span>
                            </button>
                        </form>
                    </div>
                    <ul class="nav d-flex align-items-center mb-0">
                        <li><a href="Accueil.php" class="<?= isActive('Accueil.php') ?>">Accueil</a></li>
                        <li><a href="index.php" class="<?= isActive('index.php') ?>">Forum</a></li>
                        <li><a href="browse.php" class="<?= isActive('browse.php') ?>">Événements</a></li>
                        <li><a href="streams.php" class="<?= isActive('streams.php') ?>">Streams Solidaires</a></li>
                        <li><a href="association.php" class="<?= isActive('association.php') ?>">Associations</a></li>
                        <li><a href="don.php" class="<?= isActive('don.php') ?>">Dons & Challenges</a></li>
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="profile.php" class="<?= isActive('profile.php') ?>">Profil</a></li>
                            <li><a href="logout.php">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="login.php" class="<?= isActive('login.php') ?>">Connexion</a></li>
                            <li><a href="register.php" class="<?= isActive('register.php') ?>">Inscription</a></li>
                        <?php endif; ?>
                    </ul>
                    <a class="menu-trigger" role="button" aria-label="Menu toggle" tabindex="0"><span>Menu</span></a>
                </nav>
            </div>
        </div>
    </div>
</header>
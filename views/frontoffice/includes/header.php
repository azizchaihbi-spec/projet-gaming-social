<?php
// Header commun FrontOffice
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header id="mainHeader" class="header-area header-sticky">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <nav class="main-nav d-flex align-items-center justify-content-between">
                    <a href="Accueil.php" class="logo">
                        <img src="assets/images/logooo.png" alt="Play to Help - Manette Solidaire" height="50">
                    </a>
                    <div class="search-input" style="flex-grow: 1; max-width: 400px; margin-left: 20px;">
                        <form id="search" action="search.html" class="d-flex align-items-center">
                            <input type="text" class="form-control" placeholder="Rechercher association, don ou challenge..." name="q" />
                            <button type="submit" style="background:none; border:none; color:#666; font-size:1.2em; cursor:pointer;">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                    <ul class="nav d-flex align-items-center mb-0">
                        <li><a href="Accueil.php">Accueil</a></li>
                        <li><a href="index.php">Forum</a></li>
                        <li><a href="browse.php">Événements</a></li>
                        <li><a href="streams.php">Streams Solidaires</a></li>
                        <?php if (isset($_SESSION['user'])): ?>
                            <li><a href="profile.php">Profil</a></li>
                            <li><a href="logout.php">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="register.php">Inscription</a></li>
                            <li><a href="login.php">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                    <a class="menu-trigger" role="button" aria-label="Menu toggle" tabindex="0"><span>Menu</span></a>
                </nav>
            </div>
        </div>
    </div>
</header>

<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: profile.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Mot de passe oublié</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots"><span></span><span></span><span></span></div>
        </div>
    </div>

    <!-- HEADER -->
    <header id="mainHeader" class="header-area header-sticky">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <nav class="main-nav d-flex align-items-center justify-content-between">
                        <a href="index.html" class="logo">
                            <img src="assets/images/logooo.png" alt="Play to Help - Manette Solidaire" height="50">
                        </a>
                        <div class="search-input" style="flex-grow: 1; max-width: 400px; margin-left: 20px;">
                            <form id="search" action="search.html" class="d-flex align-items-center">
                                <input type="text" class="form-control" placeholder="Rechercher association, don ou challenge..." name="q" />
                                <button type="submit" style="background:none; border:none; color:#666; font-size:1.2em; cursor:pointer;">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    <span class="sr-only">Rechercher</span>
                                </button>
                            </form>
                        </div>
                        <ul class="nav d-flex align-items-center mb-0">
                            <li><a href="index.html">Accueil</a></li>
                            <li><a href="index.php">Forum</a></li>
                            <li><a href="browse.html">Événements</a></li>
                            <li><a href="streams.html">Streams Solidaires</a></li>
                            <li><a href="association.html">Associations</a></li>
                            <li><a href="don.html">Dons & Challenges</a></li>
                            <?php if (isset($_SESSION['user'])): ?>
                                <li><a href="profile.php">Profil</a></li>
                                <li><a href="logout.php">Déconnexion</a></li>
                            <?php else: ?>
                                <li><a href="login.php" class="active">Connexion</a></li>
                                <li><a href="register.php">Inscription</a></li>
                            <?php endif; ?>
                        </ul>
                        <a class="menu-trigger" role="button" aria-label="Menu toggle" tabindex="0"><span>Menu</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-content">
                    <div class="auth-container">
                        <div class="auth-header">
                            <h2><i class="fa fa-key"></i> Mot de passe oublié</h2>
                            <p>Entrez votre adresse email pour recevoir un lien de réinitialisation</p>
                        </div>
                        
                        <form id="forgotPasswordForm">
                            <div class="form-group">
                                <label>Adresse email <span class="required">*</span></label>
                                <input type="text" id="forgotEmail" placeholder="votre@email.com" autocomplete="email">
                            </div>
                            
                            <button type="button" class="submit-btn" onclick="handleForgotPassword()">
                                <i class="fa fa-paper-plane"></i> Envoyer le lien
                            </button>
                            
                            <div class="error-message" id="forgotError"></div>
                            <div class="success-message" id="forgotSuccess"></div>
                            
                            <div style="text-align: center; margin-top: 20px;">
                                <a href="login.php" class="back-to-login">
                                    <i class="fa fa-arrow-left"></i> Retour à la connexion
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>Copyright © 2025 <a href="#">Play to Help</a> - Gaming pour l'Humanitaire. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="script.js"></script>
</body>
</html>

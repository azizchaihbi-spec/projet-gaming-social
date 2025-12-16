<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Politique de confidentialité</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="assets/css/cookie-banner.css" />
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
                        <a href="Accueil.php" class="logo">
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
                            <li><a href="Accueil.php">Accueil</a></li>
                            <li><a href="index.php">Forum</a></li>
                            <li><a href="browse.php">Événements</a></li>
                            <li><a href="streams.php">Streams Solidaires</a></li>
                            <li><a href="association.html">Associations</a></li>
                            <li><a href="don.html">Dons & Challenges</a></li>
                            <?php if (isset($_SESSION['user'])): ?>
                                <li><a href="profile.php">Profil</a></li>
                                <li><a href="logout.php">Déconnexion</a></li>
                            <?php else: ?>
                                <li><a href="login.php">Connexion</a></li>
                                <li><a href="register.php">Inscription</a></li>
                            <?php endif; ?>
                        </ul>
                        <a class="menu-trigger" role="button" aria-label="Menu toggle" tabindex="0"><span>Menu</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="container" style="margin-top: 100px; margin-bottom: 50px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-content">
                    <div style="background: var(--card-bg, #1f2332); padding: 40px; border-radius: 16px;">
                        <h1 style="color: #e75e8d; margin-bottom: 20px;">
                            <i class="fa fa-shield-alt"></i>
                            Politique de confidentialité et cookies
                        </h1>
                        
                        <p style="color: #b8b8b8; margin-bottom: 30px;">
                            <strong>Dernière mise à jour :</strong> 2 décembre 2025
                        </p>

                        <section style="margin-bottom: 40px;">
                            <h2 style="color: #e75e8d; font-size: 1.5em; margin-bottom: 15px;">
                                1. Utilisation des cookies
                            </h2>
                            <p style="color: #d4d4d4; line-height: 1.8;">
                                Play to Help utilise des cookies pour améliorer votre expérience de navigation. 
                                Un cookie est un petit fichier texte stocké sur votre appareil lorsque vous visitez notre site.
                            </p>
                        </section>

                        <section style="margin-bottom: 40px;">
                            <h2 style="color: #e75e8d; font-size: 1.5em; margin-bottom: 15px;">
                                2. Types de cookies utilisés
                            </h2>
                            
                            <div style="margin-bottom: 20px;">
                                <h3 style="color: #fff; font-size: 1.2em; margin-bottom: 10px;">
                                    <i class="fa fa-check-circle" style="color: #e75e8d;"></i>
                                    Cookies essentiels
                                </h3>
                                <p style="color: #d4d4d4; line-height: 1.8; margin-left: 30px;">
                                    Ces cookies sont nécessaires au fonctionnement du site. Ils permettent l'authentification, 
                                    la sécurité et les fonctionnalités de base. Ils ne peuvent pas être désactivés.
                                </p>
                                <ul style="color: #b8b8b8; margin-left: 50px;">
                                    <li>Cookies de session (authentication)</li>
                                    <li>Cookies de sécurité (protection CSRF)</li>
                                    <li>Cookies de consentement (choix utilisateur)</li>
                                </ul>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <h3 style="color: #fff; font-size: 1.2em; margin-bottom: 10px;">
                                    <i class="fa fa-cog" style="color: #e75e8d;"></i>
                                    Cookies fonctionnels
                                </h3>
                                <p style="color: #d4d4d4; line-height: 1.8; margin-left: 30px;">
                                    Ces cookies permettent de mémoriser vos préférences et paramètres.
                                </p>
                                <ul style="color: #b8b8b8; margin-left: 50px;">
                                    <li>Langue préférée</li>
                                    <li>Thème (clair/sombre)</li>
                                    <li>Préférences d'affichage</li>
                                </ul>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <h3 style="color: #fff; font-size: 1.2em; margin-bottom: 10px;">
                                    <i class="fa fa-chart-line" style="color: #e75e8d;"></i>
                                    Cookies analytiques
                                </h3>
                                <p style="color: #d4d4d4; line-height: 1.8; margin-left: 30px;">
                                    Ces cookies nous aident à comprendre comment les visiteurs utilisent notre site.
                                </p>
                                <ul style="color: #b8b8b8; margin-left: 50px;">
                                    <li>Pages visitées</li>
                                    <li>Durée de visite</li>
                                    <li>Source de trafic</li>
                                </ul>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <h3 style="color: #fff; font-size: 1.2em; margin-bottom: 10px;">
                                    <i class="fa fa-bullhorn" style="color: #e75e8d;"></i>
                                    Cookies marketing
                                </h3>
                                <p style="color: #d4d4d4; line-height: 1.8; margin-left: 30px;">
                                    Ces cookies sont utilisés pour afficher des publicités pertinentes.
                                </p>
                                <ul style="color: #b8b8b8; margin-left: 50px;">
                                    <li>Publicités ciblées</li>
                                    <li>Remarketing</li>
                                    <li>Mesure d'efficacité</li>
                                </ul>
                            </div>
                        </section>

                        <section style="margin-bottom: 40px;">
                            <h2 style="color: #e75e8d; font-size: 1.5em; margin-bottom: 15px;">
                                3. Durée de conservation
                            </h2>
                            <p style="color: #d4d4d4; line-height: 1.8;">
                                Les cookies ont des durées de vie différentes selon leur fonction :
                            </p>
                            <ul style="color: #b8b8b8; margin-left: 20px;">
                                <li><strong>Cookies de consentement :</strong> 365 jours</li>
                                <li><strong>Cookies de préférences :</strong> 90 jours</li>
                                <li><strong>Cookies de session :</strong> 30 jours</li>
                            </ul>
                        </section>

                        <section style="margin-bottom: 40px;">
                            <h2 style="color: #e75e8d; font-size: 1.5em; margin-bottom: 15px;">
                                4. Gérer vos préférences
                            </h2>
                            <p style="color: #d4d4d4; line-height: 1.8; margin-bottom: 15px;">
                                Vous pouvez à tout moment modifier vos préférences de cookies en cliquant sur le bouton ci-dessous :
                            </p>
                            <button 
                                onclick="window.revokeCookieConsent()" 
                                style="padding: 12px 30px; background: linear-gradient(135deg, #e75e8d 0%, #c74375 100%); color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 1em; font-weight: 600;"
                            >
                                <i class="fa fa-cog"></i>
                                Gérer mes préférences de cookies
                            </button>
                        </section>

                        <section style="margin-bottom: 40px;">
                            <h2 style="color: #e75e8d; font-size: 1.5em; margin-bottom: 15px;">
                                5. Vos droits (RGPD)
                            </h2>
                            <p style="color: #d4d4d4; line-height: 1.8;">
                                Conformément au RGPD, vous disposez des droits suivants :
                            </p>
                            <ul style="color: #b8b8b8; margin-left: 20px;">
                                <li><strong>Droit d'accès :</strong> Consulter vos données personnelles</li>
                                <li><strong>Droit de rectification :</strong> Corriger vos données</li>
                                <li><strong>Droit à l'effacement :</strong> Supprimer vos données</li>
                                <li><strong>Droit d'opposition :</strong> Refuser le traitement de vos données</li>
                                <li><strong>Droit à la portabilité :</strong> Récupérer vos données</li>
                            </ul>
                        </section>

                        <section style="margin-bottom: 40px;">
                            <h2 style="color: #e75e8d; font-size: 1.5em; margin-bottom: 15px;">
                                6. Contact
                            </h2>
                            <p style="color: #d4d4d4; line-height: 1.8;">
                                Pour toute question concernant notre politique de confidentialité ou l'utilisation de vos données, 
                                vous pouvez nous contacter à :
                            </p>
                            <p style="color: #e75e8d; margin-left: 20px;">
                                <i class="fa fa-envelope"></i>
                                <strong>privacy@playtohelp.org</strong>
                            </p>
                        </section>

                        <div style="padding: 20px; background: rgba(231, 94, 141, 0.1); border-left: 4px solid #e75e8d; border-radius: 8px; margin-top: 40px;">
                            <p style="color: #d4d4d4; margin: 0; line-height: 1.8;">
                                <i class="fa fa-info-circle" style="color: #e75e8d;"></i>
                                <strong>Note importante :</strong> Cette page sera mise à jour régulièrement. 
                                Nous vous recommandons de la consulter périodiquement pour rester informé de nos pratiques.
                            </p>
                        </div>
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
    <script src="assets/js/cookie-consent.js"></script>
</body>
</html>

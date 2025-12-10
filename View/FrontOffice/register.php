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
    <title>Play to Help - Inscription</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
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
                            <li><a href="browse.html">Événements</a></li>
                            <li><a href="streams.html">Streams Solidaires</a></li>
                            <li><a href="association.html">Associations</a></li>
                            <li><a href="don.html">Dons & Challenges</a></li>
                            <li><a href="backoffice.html">Back-Office</a></li>
                            <li><a href="login.php">Connexion</a></li>
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
                            <h2>Créer un compte</h2>
                            <p>Rejoignez la communauté Play to Help</p>
                        </div>
                        
                        <div style="text-align: center; margin-bottom: 20px;">
                            <a href="login.php" class="btn-secondary" style="display: inline-block; padding: 10px 20px;">Déjà un compte ? Se connecter</a>
                        </div>
                        
                        <form id="signupForm">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Prénom <span class="required">*</span></label>
                                    <input type="text" id="signupFirstName" placeholder="Votre prénom">
                                </div>
                                
                                <div class="form-group">
                                    <label>Nom <span class="required">*</span></label>
                                    <input type="text" id="signupLastName" placeholder="Votre nom">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Nom d'utilisateur <span class="required">*</span></label>
                                <input type="text" id="signupUsername" placeholder="Votre pseudo gaming">
                            </div>
                            
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" id="signupEmail" placeholder="votre@email.com">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Date de naissance <span class="required">*</span></label>
                                    <input type="date" id="signupBirthdate">
                                </div>
                                
                                <div class="form-group">
                                    <label>Genre</label>
                                    <select id="signupGender">
                                        <option value="prefer-not">Préfère ne pas dire</option>
                                        <option value="male">Homme</option>
                                        <option value="female">Femme</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Pays <span class="required">*</span></label>
                                    <select id="signupCountry">
                                        <option value="">Sélectionner...</option>
                                        <option value="FR">France</option>
                                        <option value="TN">Tunisie</option>
                                        <option value="DZ">Algérie</option>
                                        <option value="MA">Maroc</option>
                                        <option value="BE">Belgique</option>
                                        <option value="CH">Suisse</option>
                                        <option value="CA">Canada</option>
                                        <option value="US">États-Unis</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Ville</label>
                                    <input type="text" id="signupCity" placeholder="Votre ville">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Rôle <span class="required">*</span></label>
                                <select id="signupRole" onchange="toggleStreamerFields()">
                                    <option value="">Sélectionner...</option>
                                    <option value="viewer">Viewer</option>
                                    <option value="streamer">Streamer</option>
                                </select>
                            </div>
                            
                            <div id="streamerFields" style="display: none;">
                                <div class="form-group">
                                    <label>Lien de stream</label>
                                    <input type="url" id="signupStreamLink" placeholder="https://twitch.tv/votrepseudo">
                                </div>
                                
                                <div class="form-group">
                                    <label>Description de streamer</label>
                                    <textarea id="signupStreamDescription" placeholder="Décrivez votre chaîne..." rows="3"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Plateforme</label>
                                    <select id="signupStreamPlatform">
                                        <option value="">Sélectionner...</option>
                                        <option value="twitch">Twitch</option>
                                        <option value="youtube">YouTube</option>
                                        <option value="kick">Kick</option>
                                        <option value="other">Autre</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Mot de passe <span class="required">*</span></label>
                                    <input type="password" id="signupPassword" placeholder="••••••••" oninput="checkPasswordStrength()">
                                    <div class="password-strength-container">
                                        <div class="password-strength-bar">
                                            <div class="password-strength-fill" id="passwordStrengthFill"></div>
                                        </div>
                                        <div class="password-strength-text" id="passwordStrengthText">
                                            <span id="strengthLabel">Saisissez un mot de passe</span>
                                            <span id="strengthScore"></span>
                                        </div>
                                        <div class="password-requirements">
                                            <div id="req-length">Au moins 8 caractères</div>
                                            <div id="req-uppercase">Une lettre majuscule</div>
                                            <div id="req-lowercase">Une lettre minuscule</div>
                                            <div id="req-number">Un chiffre</div>
                                            <div id="req-special">Un caractère spécial (!@#$%^&*)</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Confirmer <span class="required">*</span></label>
                                    <input type="password" id="signupConfirmPassword" placeholder="••••••••">
                                </div>
                            </div>
                            
                            <button type="button" class="submit-btn" onclick="handleSignup()">
                                <i class="fa fa-user-plus"></i> Créer mon compte
                            </button>
                            
                            <div class="error-message" id="signupError"></div>
                            <div class="success-message" id="signupSuccess"></div>
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

    <?php include 'includes/cookie-banner.php'; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="script.js"></script>
    <script src="assets/js/cookie-consent.js"></script>
</body>
</html>
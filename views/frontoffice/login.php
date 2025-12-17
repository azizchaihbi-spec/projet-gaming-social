 <?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: profile.php');
    exit();
}
require_once __DIR__ . '/../../config/recaptcha.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Connexion</title>
    <link rel="icon" type="image/png" href="assets/images/logooo.png">
    <link rel="apple-touch-icon" href="assets/images/logooo.png">
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

    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-content">
                    <div class="auth-container">
                        <div class="auth-header">
                            <h2>Connexion</h2>
                            <p>Accédez à votre compte Play to Help</p>
                        </div>
                        
                        <div style="text-align: center; margin-bottom: 20px;">
                            <a href="register.php" class="btn-secondary" style="display: inline-block; padding: 10px 20px;">Pas de compte ? S'inscrire</a>
                        </div>
                        
                        <form id="loginForm">
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" id="loginEmail" placeholder="votre@email.com">
                            </div>
                            
                            <div class="form-group">
                                <label>Mot de passe <span class="required">*</span></label>
                                <input type="password" id="loginPassword" placeholder="••••••••">
                            </div>
                            
                            <div style="text-align: right; margin-bottom: 15px;">
                                <a href="forgot_password.php" class="forgot-password">
                                    <i class="fa fa-key"></i> Mot de passe oublié ?
                                </a>
                            </div>

                            <div class="recaptcha-container">
                                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars(RECAPTCHA_SITE_KEY) ?>"></div>
                            </div>

                            <button type="button" class="submit-btn" onclick="handleLogin()">
                                <i class="fa fa-sign-in"></i> Se connecter
                            </button>
                            
                            <div class="error-message" id="loginError"></div>
                            <div class="success-message" id="loginSuccess"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <?php include 'includes/cookie-banner.php'; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="script.js"></script>
    <script src="assets/js/cookie-consent.js"></script>
</body>
</html>
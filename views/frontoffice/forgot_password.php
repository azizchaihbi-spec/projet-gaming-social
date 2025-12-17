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
    <link rel="icon" type="image/png" href="assets/images/logooo.png">
    <link rel="apple-touch-icon" href="assets/images/logooo.png">
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

    <?php include 'includes/header.php'; ?>

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

    <?php include 'includes/footer.php'; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="script.js"></script>
</body>
</html>

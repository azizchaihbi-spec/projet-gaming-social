<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: profile.php');
    exit();
}

// Récupérer le token depuis l'URL
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
if (empty($token)) {
    header('Location: forgot_password.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Réinitialiser le mot de passe</title>
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
                            <h2><i class="fa fa-lock"></i> Nouveau mot de passe</h2>
                            <p>Créez votre nouveau mot de passe sécurisé</p>
                        </div>
                        
                        <form id="resetPasswordForm">
                            <input type="hidden" id="resetToken" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="form-group">
                                <label>Nouveau mot de passe <span class="required">*</span></label>
                                <input type="password" id="newPassword" placeholder="••••••••" autocomplete="new-password">
                                <small style="color: var(--text-light); font-size: 12px; display: block; margin-top: 5px;">
                                    Min 6 caractères, 1 majuscule, 1 minuscule, 1 chiffre
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label>Confirmer le mot de passe <span class="required">*</span></label>
                                <input type="password" id="confirmPassword" placeholder="••••••••" autocomplete="new-password">
                            </div>
                            
                            <button type="button" class="submit-btn" onclick="handleResetPassword()">
                                <i class="fa fa-check"></i> Réinitialiser le mot de passe
                            </button>
                            
                            <div class="error-message" id="resetError"></div>
                            <div class="success-message" id="resetSuccess"></div>
                            
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

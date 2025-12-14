<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Play to Help - Mon Profil</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
    <link rel="stylesheet" href="assets/css/owl.css" />
    <link rel="stylesheet" href="assets/css/animate.css" />
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="assets/css/cookie-banner.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
                                <li><a href="profile.php" class="active">Profil</a></li>
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


    <?php
    // Gérer la déconnexion
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: login.php');
        exit();
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-content">
                    <div class="profile-section active">
                        <div class="profile-header-top">
                            <h2>Mon Profil</h2>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <button class="btn-primary" onclick="exportProfileToPDF()" style="padding: 8px 15px; font-size: 14px;">
                                    <i class="fa fa-file-pdf-o"></i> Exporter en PDF
                                </button>
                                <a href="?logout=1" class="logout-btn" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter?')">
                                    <i class="fa fa-sign-out"></i> Déconnexion
                                </a>
                            </div>
                        </div>
                        
                        <div class="profile-card">
                            <div class="profile-main">
                                <div class="profile-image">
                                    <img id="profileImage" src="<?php echo htmlspecialchars($user['profile_image'] ?? 'assets/images/profile.jpg'); ?>" alt="Profile">
                                    <span class="status-badge" id="statusBadge">En ligne</span>
                                </div>
                                
                                <div class="profile-info">
                                    <h3 id="profileUsername"><?php echo htmlspecialchars($user['username']); ?></h3>
                                    <p class="email" id="profileEmail"><?php echo htmlspecialchars($user['email']); ?></p>
                                    
                                    <div class="profile-details">
                                        <div class="detail-item">
                                            <i class="fa fa-user"></i>
                                            <span><strong>Nom complet:</strong> <span id="profileFullName"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa fa-calendar"></i>
                                            <span><strong>Date de naissance:</strong> <span id="profileBirthdate"><?php echo date('d/m/Y', strtotime($user['birthdate'])); ?></span></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa fa-venus-mars"></i>
                                            <span><strong>Genre:</strong> <span id="profileGender">
                                                <?php 
                                                $genderMap = [
                                                    'male' => 'Homme',
                                                    'female' => 'Femme', 
                                                    'other' => 'Autre',
                                                    'prefer-not' => 'Non spécifié'
                                                ];
                                                echo $genderMap[$user['gender']] ?? 'Non spécifié';
                                                ?>
                                            </span></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa fa-map-marker"></i>
                                            <span><strong>Localisation:</strong> <span id="profileLocation">
                                                <?php
                                                $location = $user['country'] ?? 'Non spécifié';
                                                if (!empty($user['city'])) {
                                                    $location = $user['city'] . ', ' . $location;
                                                }
                                                echo htmlspecialchars($location);
                                                ?>
                                            </span></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa fa-star"></i>
                                            <span><strong>Membre depuis:</strong> <span id="profileJoinDate"><?php echo date('d/m/Y', strtotime($user['join_date'])); ?></span></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa fa-user-circle"></i>
                                            <span><strong>Rôle:</strong> <span id="profileRole">
                                                <?php
                                                    if ($user['role'] === 'streamer') {
                                                        echo 'Streamer';
                                                    } elseif ($user['role'] === 'admin') {
                                                        echo 'Administrateur';
                                                    } else {
                                                        echo 'Viewer';
                                                    }
                                                ?>
                                            </span></span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($user['role'] === 'streamer'): ?>
                                    <div id="streamerInfo" style="display: block; margin-top: 20px; padding: 15px; background: var(--darker-bg); border-radius: 12px;">
                                        <h4>Infos Streamer</h4>
                                        <div class="detail-item">
                                            <i class="fa fa-link"></i>
                                            <span><strong>Lien de stream:</strong> <span id="profileStreamLink"><?php echo htmlspecialchars($user['stream_link'] ?? 'Non spécifié'); ?></span></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa fa-comment"></i>
                                            <span><strong>Description:</strong> <span id="profileStreamDescription"><?php echo htmlspecialchars($user['stream_description'] ?? 'Aucune description'); ?></span></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fa fa-globe"></i>
                                            <span><strong>Plateforme:</strong> <span id="profileStreamPlatform">
                                                <?php
                                                $platformMap = [
                                                    'twitch' => 'Twitch',
                                                    'youtube' => 'YouTube',
                                                    'kick' => 'Kick',
                                                    'other' => 'Autre'
                                                ];
                                                echo $platformMap[$user['stream_platform']] ?? 'Non spécifié';
                                                ?>
                                            </span></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="action-buttons">
                                        <button class="btn-primary" onclick="openEditProfileModal()">
                                            <i class="fa fa-edit"></i> Modifier l'avatar
                                        </button>
                                        <button class="btn-primary" onclick="openFriendsModal()">
                                            <i class="fa fa-users"></i> Mes amis <span id="friendsCount" class="badge">0</span>
                                        </button>
                                        <?php if ($user['role'] === 'streamer'): ?>
                                        <button id="editStreamerButton" class="btn-primary" onclick="openEditStreamerModal()">
                                            <i class="fa fa-edit"></i> Modifier infos streamer
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($user['role'] === 'admin'): ?>
                                        <a href="../../views/backoffice/index.php" class="btn-primary">
                                            <i class="fa fa-cog"></i> Back-Office Admin
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="user-stats">
                            <div class="stat-card">
                                <h3 id="statGames">0</h3>
                                <p>Jeux Téléchargés</p>
                            </div>
                            <div class="stat-card clickable" onclick="openFriendsModal()">
                                <h3 id="statFriends">0</h3>
                                <p>Amis en ligne</p>
                            </div>
                            <div class="stat-card">
                                <h3 id="statStreams">0</h3>
                                <p>Streams Live</p>
                            </div>
                            <div class="stat-card">
                                <h3 id="statClips">0</h3>
                                <p>Clips</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour modifier le profil -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditProfileModal()">&times;</span>
            <h2>Choisir ou télécharger un avatar</h2>
            
            <!-- Onglets -->
            <div class="avatar-tabs">
                <button class="tab-btn active" onclick="switchAvatarTab('preset')">
                    <i class="fa fa-image"></i> Avatars prédéfinis
                </button>
                <button class="tab-btn" onclick="switchAvatarTab('upload')">
                    <i class="fa fa-upload"></i> Télécharger une photo
                </button>
            </div>
            
            <!-- Tab 1: Avatars prédéfinis -->
            <div id="presetAvatarTab" class="avatar-tab active">
                <div class="avatar-options">
                    <img class="avatar-option" src="assets/images/avatars/avatar1.png" alt="Avatar 1" onclick="selectAvatar(this)">
                    <img class="avatar-option" src="assets/images/avatars/avatar2.png" alt="Avatar 2" onclick="selectAvatar(this)">
                    <img class="avatar-option" src="assets/images/avatars/avatar3.png" alt="Avatar 3" onclick="selectAvatar(this)">
                    <img class="avatar-option" src="assets/images/avatars/avatar4.png" alt="Avatar 4" onclick="selectAvatar(this)">
                    <img class="avatar-option" src="assets/images/avatars/avatar5.png" alt="Avatar 5" onclick="selectAvatar(this)">
                </div>
            </div>
            
            <!-- Tab 2: Upload personnalisé -->
            <div id="uploadAvatarTab" class="avatar-tab">
                <div class="upload-section">
                    <div class="upload-area" id="uploadArea" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                        <i class="fa fa-cloud-upload"></i>
                        <p>Glissez votre photo ici ou cliquez pour sélectionner</p>
                        <input type="file" id="avatarInput" accept="image/*" onchange="handleFileSelect(event)" style="display: none;">
                    </div>
                    <div id="uploadPreview" style="display: none; margin-top: 20px; text-align: center;">
                        <img id="previewImage" src="" alt="Aperçu" style="max-width: 200px; max-height: 200px; border-radius: 10px;">
                        <button type="button" class="submit-btn" onclick="document.getElementById('avatarInput').click()" style="margin-top: 10px;">
                            <i class="fa fa-refresh"></i> Changer la photo
                        </button>
                    </div>
                </div>
            </div>
            
            <button class="submit-btn" onclick="handleSaveAvatar()" style="margin-top: 20px;">
                <i class="fa fa-save"></i> Enregistrer
            </button>
            <div class="error-message" id="editError"></div>
            <div class="success-message" id="editSuccess"></div>
        </div>
    </div>

    <!-- Modal pour modifier les infos streamer -->
    <?php if ($user['role'] === 'streamer'): ?>
    <div id="editStreamerModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditStreamerModal()">&times;</span>
            <h2>Modifier infos streamer</h2>
            
            <div class="form-group">
                <label>Lien de stream</label>
                <input type="url" id="editStreamLink" value="<?php echo htmlspecialchars($user['stream_link'] ?? ''); ?>" placeholder="https://twitch.tv/votrepseudo">
            </div>
            
            <div class="form-group">
                <label>Description de streamer</label>
                <textarea id="editStreamDescription" placeholder="Décrivez votre chaîne..." rows="3"><?php echo htmlspecialchars($user['stream_description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Plateforme</label>
                <select id="editStreamPlatform">
                    <option value="">Sélectionner...</option>
                    <option value="twitch" <?php echo ($user['stream_platform'] ?? '') === 'twitch' ? 'selected' : ''; ?>>Twitch</option>
                    <option value="youtube" <?php echo ($user['stream_platform'] ?? '') === 'youtube' ? 'selected' : ''; ?>>YouTube</option>
                    <option value="kick" <?php echo ($user['stream_platform'] ?? '') === 'kick' ? 'selected' : ''; ?>>Kick</option>
                    <option value="other" <?php echo ($user['stream_platform'] ?? '') === 'other' ? 'selected' : ''; ?>>Autre</option>
                </select>
            </div>
            
            <button class="submit-btn" onclick="handleSaveStreamer()">
                <i class="fa fa-save"></i> Enregistrer
            </button>
            
            <div class="error-message" id="editStreamerError"></div>
            <div class="success-message" id="editStreamerSuccess"></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal des Amis -->
    <div id="friendsModal" class="modal">
        <div class="modal-content" style="max-width: 900px;">
            <span class="close-modal" onclick="closeFriendsModal()">&times;</span>
            <h2>Mes Amis</h2>
            
            <!-- Onglets -->
            <div class="friends-tabs">
                <button class="friends-tab-btn active" onclick="switchFriendsTab('all')">
                    <i class="fa fa-users"></i> Tous (<span id="allFriendsCount">0</span>)
                </button>
                <button class="friends-tab-btn" onclick="switchFriendsTab('online')">
                    <i class="fa fa-circle" style="color: #51cf66;"></i> En ligne (<span id="onlineFriendsCount">0</span>)
                </button>
                <button class="friends-tab-btn" onclick="switchFriendsTab('requests')">
                    <i class="fa fa-envelope"></i> Demandes (<span id="requestsCount">0</span>)
                </button>
                <button class="friends-tab-btn" onclick="switchFriendsTab('search')">
                    <i class="fa fa-search"></i> Rechercher
                </button>
            </div>
            
            <!-- Tab: Tous les amis -->
            <div id="allFriendsTab" class="friends-tab-content active">
                <div id="allFriendsList" class="friends-list">
                    <p class="loading-text"><i class="fa fa-spinner fa-spin"></i> Chargement...</p>
                </div>
            </div>
            
            <!-- Tab: Amis en ligne -->
            <div id="onlineFriendsTab" class="friends-tab-content">
                <div id="onlineFriendsList" class="friends-list">
                    <p class="loading-text"><i class="fa fa-spinner fa-spin"></i> Chargement...</p>
                </div>
            </div>
            
            <!-- Tab: Demandes d'amis -->
            <div id="requestsTab" class="friends-tab-content">
                <div id="requestsList" class="friends-list">
                    <p class="loading-text"><i class="fa fa-spinner fa-spin"></i> Chargement...</p>
                </div>
            </div>
            
            <!-- Tab: Recherche -->
            <div id="searchTab" class="friends-tab-content">
                <div class="search-box">
                    <input type="text" id="friendSearchInput" placeholder="Rechercher un utilisateur..." onkeyup="searchFriends(this.value)">
                    <i class="fa fa-search"></i>
                </div>
                <div id="searchResults" class="friends-list">
                    <p class="placeholder-text">Tapez pour rechercher des utilisateurs</p>
                </div>
            </div>
            
            <div class="error-message" id="friendsError"></div>
            <div class="success-message" id="friendsSuccess"></div>
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
    <script src="assets/js/friends.js"></script>
    <script src="assets/js/profile-export.js"></script>
</body>
</html>
// Système de gestion des utilisateurs

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing...');
    
    // Initialiser les champs streamer
    initializeStreamerFields();
    
    // Cacher le preloader
    setTimeout(() => {
        const preloader = document.getElementById('js-preloader');
        if (preloader) {
            preloader.style.display = 'none';
        }
    }, 1000);
});

// Initialiser les champs streamer
function initializeStreamerFields() {
    const roleSelect = document.getElementById('signupRole');
    const streamerFields = document.getElementById('streamerFields');
    
    if (roleSelect && streamerFields) {
        roleSelect.addEventListener('change', toggleStreamerFields);
        toggleStreamerFields();
    }
}

// Afficher/masquer les champs streamer avec animation
function toggleStreamerFields() {
    const role = document.getElementById('signupRole').value;
    const streamerFields = document.getElementById('streamerFields');
    
    if (!streamerFields) return;
    
    if (role === 'streamer') {
        streamerFields.style.display = 'block';
        setTimeout(() => {
            streamerFields.style.opacity = '1';
            streamerFields.style.transform = 'translateY(0)';
        }, 10);
    } else {
        streamerFields.style.opacity = '0';
        streamerFields.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            streamerFields.style.display = 'none';
        }, 300);
    }
}

// Helpers de validation (côté client)
function isAlphaName(name) {
    if (!name) return false;
    return /^[A-Za-zÀ-ÖØ-öø-ÿ' -]{2,50}$/u.test(name);
}

function isValidUsername(username) {
    if (!username) return false;
    return /^[A-Za-z0-9_-]{3,30}$/.test(username);
}

function isValidEmailFormat(email) {
    if (!email) return false;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPasswordStrength(password) {
    if (!password) return false;
    return password.length >= 6 && /[a-z]/.test(password) && /[A-Z]/.test(password) && /[0-9]/.test(password);
}

function isAtLeastAge(dateString, minAge) {
    if (!dateString) return false;
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return false;
    const today = new Date();
    let age = today.getFullYear() - d.getFullYear();
    const m = today.getMonth() - d.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < d.getDate())) {
        age--;
    }
    return age >= minAge;
}

function isValidHttpUrl(url) {
    if (!url) return true; // facultatif
    try {
        const u = new URL(url);
        return u.protocol === 'http:' || u.protocol === 'https:';
    } catch (_) {
        return false;
    }
}

// Gérer l'inscription
async function handleSignup() {
    const firstName = document.getElementById('signupFirstName').value.trim();
    const lastName = document.getElementById('signupLastName').value.trim();
    const username = document.getElementById('signupUsername').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    const birthdate = document.getElementById('signupBirthdate').value;
    const gender = document.getElementById('signupGender').value;
    const country = document.getElementById('signupCountry').value;
    const city = document.getElementById('signupCity').value.trim();
    const role = document.getElementById('signupRole').value;
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('signupConfirmPassword').value;
    
    let streamLink = '';
    let streamDescription = '';
    let streamPlatform = '';
    
    if (role === 'streamer') {
        streamLink = document.getElementById('signupStreamLink').value.trim();
        streamDescription = document.getElementById('signupStreamDescription').value.trim();
        streamPlatform = document.getElementById('signupStreamPlatform').value;
    }
    
    clearMessages();
    
    // Validation agrégée
    const errors = [];
    // Champs obligatoires
    if (!firstName) errors.push('Prénom: champ obligatoire');
    if (!lastName) errors.push('Nom: champ obligatoire');
    if (!username) errors.push("Nom d'utilisateur: champ obligatoire");
    if (!email) errors.push('Email: champ obligatoire');
    if (!birthdate) errors.push('Date de naissance: champ obligatoire');
    if (!country) errors.push('Pays: champ obligatoire');
    if (!role) errors.push('Rôle: champ obligatoire');
    if (!password) errors.push('Mot de passe: champ obligatoire');
    if (!confirmPassword) errors.push('Confirmation mot de passe: champ obligatoire');

    // Formats
    if (firstName && !isAlphaName(firstName)) {
        errors.push('Prénom: lettres uniquement (2-50), ');
    }
    if (lastName && !isAlphaName(lastName)) {
        errors.push('Nom: lettres uniquement (2-50), ');
    }
    if (username && !isValidUsername(username)) {
        errors.push("Nom d'utilisateur: 3-30 (lettres, chiffres, - et _ seulement)");
    }
    if (birthdate && !isAtLeastAge(birthdate, 13)) {
        errors.push('Vous devez avoir au moins 13 ans pour vous inscrire');
    }
    if (password && confirmPassword && password !== confirmPassword) {
        errors.push('Les mots de passe ne correspondent pas');
    }
    if (password && !isValidPasswordStrength(password)) {
        errors.push('Mot de passe: min 6 caractères, 1 majuscule, 1 minuscule, 1 chiffre');
    }
    if (email && !isValidEmailFormat(email)) {
        errors.push('Email: format invalide');
    }
    if (role === 'streamer' && streamLink && !isValidHttpUrl(streamLink)) {
        errors.push('Lien de stream invalide (http(s) requis)');
    }

    if (errors.length > 0) {
        showErrorList('signupError', errors);
        return;
    }
    
    // Préparer les données
    const userData = {
        firstName: firstName,
        lastName: lastName,
        username: username,
        email: email,
        birthdate: birthdate,
        gender: gender,
        country: country,
        city: city,
        role: role,
        streamLink: streamLink,
        streamDescription: streamDescription,
        streamPlatform: streamPlatform,
        password: password
    };
    
    try {
        const response = await fetch('../../Controller/authController.php?action=register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(userData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('signupSuccess', result.message);
            
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            const msg = typeof result.message === 'string' ? result.message : '';
            // Split on any pipe, with or without spaces
            const list = msg.indexOf('|') !== -1
                ? msg.split('|').map(s => s.trim()).filter(Boolean)
                : [msg || 'Inscription impossible'];
            showErrorList('signupError', list);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showError('signupError', 'Erreur de connexion au serveur. Veuillez réessayer.');
    }
}

// Gérer la connexion
async function handleLogin() {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    
    clearMessages();
    
    if (!email || !password) {
        showError('loginError', 'Veuillez remplir tous les champs');
        return;
    }

    // reCAPTCHA v2: lire le token du widget checkbox
    let recaptchaToken = null;
    try {
        if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse) {
            recaptchaToken = grecaptcha.getResponse();
        }
    } catch (error) {
        console.error('Erreur reCAPTCHA:', error);
        recaptchaToken = null;
    }
    
    try {
        const response = await fetch('../../Controller/authController.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password, recaptchaToken })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('loginSuccess', result.message);
            
            // Stocker l'utilisateur dans le localStorage
            localStorage.setItem('currentUser', JSON.stringify(result.user));
            
            setTimeout(() => {
                window.location.href = 'profile.php';
            }, 1000);
        } else {
            const msg = typeof result.message === 'string' ? result.message : '';
            const lower = msg.toLowerCase();
            // Reset reCAPTCHA si échec de vérification
            if (lower.includes('captcha') || lower.includes('recaptcha')) {
                try { 
                    if (typeof grecaptcha !== 'undefined' && grecaptcha.reset) {
                        grecaptcha.reset();
                    }
                } catch(_){}
            }
            const isBanMessage = /\b(banni|suspendu|ban|suspension)\b/i.test(msg);
            if (isBanMessage) {
                showCenterAlert('error', 'Accès refusé', msg);
            } else {
                showError('loginError', msg || 'Connexion impossible');
            }
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showError('loginError', 'Erreur de connexion au serveur. Veuillez réessayer.');
    }
}

// Fonctions utilitaires
let selectedAvatarSrc = null;
let uploadedFile = null;

// Gestion des onglets d'avatar
function switchAvatarTab(tabName) {
    // Masquer tous les onglets
    document.querySelectorAll('.avatar-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Afficher l'onglet sélectionné
    const tabElement = document.getElementById(tabName + 'AvatarTab');
    if (tabElement) {
        tabElement.classList.add('active');
    }
    
    // Marquer le bouton comme actif
    event.target.classList.add('active');
}

// Gestion du drag and drop
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadArea').classList.add('drag-over');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadArea').classList.remove('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('uploadArea').classList.remove('drag-over');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFileSelect({ target: { files: files } });
    }
}

// Gestion de la sélection de fichier
function handleFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Vérifier que c'est une image
    if (!file.type.startsWith('image/')) {
        showError('editError', 'Veuillez sélectionner une image');
        return;
    }
    
    // Vérifier la taille (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showError('editError', 'La taille de l\'image ne doit pas dépasser 5MB');
        return;
    }
    
    uploadedFile = file;
    
    // Afficher l'aperçu
    const reader = new FileReader();
    reader.onload = function(event) {
        const previewImg = document.getElementById('previewImage');
        previewImg.src = event.target.result;
        document.getElementById('uploadPreview').style.display = 'flex';
        selectedAvatarSrc = null; // Réinitialiser la sélection prédéfinie
        clearMessages();
    };
    reader.readAsDataURL(file);
}

// Clic sur la zone d'upload
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    if (uploadArea) {
        uploadArea.addEventListener('click', function() {
            document.getElementById('avatarInput').click();
        });
    }
});

function selectAvatar(element) {
    const options = document.querySelectorAll('.avatar-option');
    options.forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
    uploadedFile = null; // Réinitialiser l'upload
    document.getElementById('uploadPreview').style.display = 'none';
    
    // Extraire le chemin relatif de l'URL complète
    // Si src = "http://localhost/path/assets/images/avatars/avatar1.png"
    // On veut juste "assets/images/avatars/avatar1.png"
    const srcUrl = element.src;
    const pathMatch = srcUrl.match(/assets\/images\/avatars\/[^?#]+/);
    selectedAvatarSrc = pathMatch ? pathMatch[0] : srcUrl;
}

function openEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'flex';
}

function closeEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'none';
    clearMessages();
}

function openEditStreamerModal() {
    document.getElementById('editStreamerModal').style.display = 'flex';
}

function closeEditStreamerModal() {
    document.getElementById('editStreamerModal').style.display = 'none';
    clearMessages();
}

function showError(elementId, message) {
    const el = document.getElementById(elementId);
    if (!el) return;
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'polite');
    el.classList.add('alert', 'alert-error', 'show');
    el.innerHTML = `
        <div class="alert-body">
            <i class="fa fa-exclamation-circle alert-icon" aria-hidden="true"></i>
            <div>
                <div class="alert-title">Erreur</div>
                <div>${message}</div>
            </div>
        </div>
    `;
    el.style.display = 'block';
}

function showErrorList(elementId, messages) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const list = Array.isArray(messages) ? messages : [String(messages)];
    const items = list.map(m => `<li>${m}</li>`).join('');
    el.setAttribute('role', 'alert');
    el.setAttribute('aria-live', 'polite');
    el.classList.add('alert', 'alert-error', 'show');
    el.innerHTML = `
        <div class="alert-body">
            <i class="fa fa-exclamation-triangle alert-icon" aria-hidden="true"></i>
            <div>
                <div class="alert-title">Veuillez corriger les erreurs suivantes :</div>
                <ul class="alert-list">${items}</ul>
            </div>
        </div>
    `;
    el.style.display = 'block';
}

function showSuccess(elementId, message) {
    const el = document.getElementById(elementId);
    if (!el) return;
    el.setAttribute('role', 'status');
    el.setAttribute('aria-live', 'polite');
    el.classList.add('alert', 'alert-success', 'show');
    el.innerHTML = `
        <div class="alert-body">
            <i class="fa fa-check-circle alert-icon" aria-hidden="true"></i>
            <div>
                <div class="alert-title">Succès</div>
                <div>${message}</div>
            </div>
        </div>
    `;
    el.style.display = 'block';
}

function clearMessages() {
    document.querySelectorAll('.error-message, .success-message').forEach(el => {
        el.style.display = 'none';
        el.innerHTML = '';
        el.classList.remove('alert', 'alert-error', 'alert-success', 'show');
    });
}

// Affichage modale centré pour messages importants (ex: bannissement)
function showCenterAlert(type, title, message) {
    let overlay = document.getElementById('centerAlertOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'centerAlertOverlay';
        overlay.className = 'ban-alert-overlay';
        document.body.appendChild(overlay);
    }

    // Clear previous content
    overlay.innerHTML = '';
    
    const titleId = `centerAlertTitle-${Date.now()}`;
    const messageId = `centerAlertMessage-${Date.now()}`;

    const box = document.createElement('div');
    box.className = `ban-alert-modal ban-alert-modal--${type || 'info'}`;
    box.setAttribute('role', 'alertdialog');
    box.setAttribute('aria-modal', 'true');
    box.setAttribute('aria-labelledby', titleId);
    box.setAttribute('aria-describedby', messageId);

    const header = document.createElement('div');
    header.className = 'ban-alert-modal__header';

    const badge = document.createElement('span');
    badge.className = 'ban-alert-modal__badge';
    badge.textContent = type === 'success' ? 'Succès' : type === 'warning' ? 'Attention' : 'Alerte';

    const titleEl = document.createElement('h4');
    titleEl.className = 'ban-alert-modal__title';
    titleEl.id = titleId;
    titleEl.textContent = title || 'Accès refusé';

    const closeIconBtn = document.createElement('button');
    closeIconBtn.type = 'button';
    closeIconBtn.className = 'ban-alert-close';
    closeIconBtn.setAttribute('aria-label', 'Fermer');
    closeIconBtn.innerHTML = '&times;';

    header.appendChild(badge);
    header.appendChild(titleEl);
    header.appendChild(closeIconBtn);

    const msgEl = document.createElement('div');
    msgEl.className = 'ban-alert-modal__message';
    msgEl.id = messageId;
    msgEl.textContent = message;

    const actions = document.createElement('div');
    actions.className = 'ban-alert-modal__actions';

    const closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'ban-alert-modal__action';
    closeBtn.textContent = 'Fermer';

    actions.appendChild(closeBtn);

    box.appendChild(header);
    box.appendChild(msgEl);
    box.appendChild(actions);
    overlay.appendChild(box);

    // Show overlay
    overlay.classList.add('active');
    overlay.style.display = 'flex';

    // Dismiss function - simple and direct (no debug logs)
    function dismiss() {
        document.removeEventListener('keydown', escHandler);
        overlay.classList.remove('active');
        overlay.style.display = 'none';
        overlay.innerHTML = '';
    }

    // ESC key handler
    function escHandler(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            dismiss();
        }
    }

    // Close icon button
    closeIconBtn.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        dismiss();
    });
    
    // Close text button
    closeBtn.addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        dismiss();
    });
    
    // Prevent clicks inside modal from closing
    box.addEventListener('click', function(event) {
        event.stopPropagation();
    });
    
    // Click outside to close
    overlay.addEventListener('click', function(event) {
        if (event.target === overlay) {
            dismiss();
        }
    });
    
    document.addEventListener('keydown', escHandler);
}

// Gérer la sauvegarde des infos streamer
async function handleSaveStreamer() {
    const streamLink = document.getElementById('editStreamLink').value.trim();
    const streamDescription = document.getElementById('editStreamDescription').value.trim();
    const streamPlatform = document.getElementById('editStreamPlatform').value;
    
    try {
        const formData = new FormData();
        formData.append('stream_link', streamLink);
        formData.append('stream_description', streamDescription); 
        formData.append('stream_platform', streamPlatform);
        
        const response = await fetch('../../Controller/authController.php?action=updateStreamerInfo', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('editStreamerSuccess', result.message);
            // Mettre à jour le localStorage
            const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
            currentUser.stream_link = streamLink;
            currentUser.stream_description = streamDescription;
            currentUser.stream_platform = streamPlatform;
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            
            // Rafraîchir la page pour voir les changements
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showError('editStreamerError', result.message);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showError('editStreamerError', 'Erreur lors de la mise à jour');
    }
}

// Gérer la demande de réinitialisation de mot de passe
async function handleForgotPassword() {
    const email = document.getElementById('forgotEmail').value.trim();
    
    clearMessages();
    
    // Validation côté client
    if (!email) {
        showError('forgotError', 'Veuillez entrer votre adresse email');
        return;
    }
    
    if (!isValidEmailFormat(email)) {
        showError('forgotError', 'Format d\'email invalide');
        return;
    }
    
    try {
        const response = await fetch('../../Controller/authController.php?action=requestPasswordReset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('forgotSuccess', result.message + '. Vérifiez votre boîte de réception.');
            document.getElementById('forgotEmail').value = '';
        } else {
            showError('forgotError', result.message);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showError('forgotError', 'Erreur de connexion au serveur. Veuillez réessayer.');
    }
}

// Gérer la réinitialisation du mot de passe
async function handleResetPassword() {
    const token = document.getElementById('resetToken').value.trim();
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    clearMessages();
    
    // Validation côté client
    const errors = [];
    
    if (!newPassword) {
        errors.push('Nouveau mot de passe: champ obligatoire');
    } else if (!isValidPasswordStrength(newPassword)) {
        errors.push('Mot de passe: min 6 caractères, 1 majuscule, 1 minuscule, 1 chiffre');
    }
    
    if (!confirmPassword) {
        errors.push('Confirmation: champ obligatoire');
    } else if (newPassword !== confirmPassword) {
        errors.push('Les mots de passe ne correspondent pas');
    }
    
    if (errors.length > 0) {
        showErrorList('resetError', errors);
        return;
    }
    
    try {
        const response = await fetch('../../Controller/authController.php?action=resetPassword', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                token: token,
                newPassword: newPassword,
                confirmPassword: confirmPassword
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('resetSuccess', result.message);
            
            // Rediriger vers la page de connexion après 2 secondes
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            const msg = typeof result.message === 'string' ? result.message : '';
            const list = msg.indexOf('|') !== -1
                ? msg.split('|').map(s => s.trim()).filter(Boolean)
                : [msg || 'Erreur lors de la réinitialisation'];
            showErrorList('resetError', list);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showError('resetError', 'Erreur de connexion au serveur. Veuillez réessayer.');
    }
}

// Sauvegarder l'avatar sélectionné
async function handleSaveAvatar() {
    if (!selectedAvatarSrc && !uploadedFile) {
        showError('editError', 'Veuillez sélectionner ou télécharger un avatar');
        return;
    }

    clearMessages();

    try {
        const formData = new FormData();
        
        // Si un fichier est uploadé, l'envoyer
        if (uploadedFile) {
            formData.append('avatar_file', uploadedFile);
        } else {
            // Sinon envoyer l'avatar prédéfini
            formData.append('profile_image', selectedAvatarSrc);
        }

        const response = await fetch('../../Controller/authController.php?action=updateProfile', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showSuccess('editSuccess', result.message || 'Avatar mis à jour avec succès');
            
            // Mettre à jour l'image de profil affichée
            if (uploadedFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profileImage').src = e.target.result;
                };
                reader.readAsDataURL(uploadedFile);
            } else {
                document.getElementById('profileImage').src = selectedAvatarSrc;
            }
            
            // Mettre à jour la session
            if (result.user) {
                localStorage.setItem('currentUser', JSON.stringify(result.user));
            }
            
            // Fermer le modal après 1 seconde
            setTimeout(() => {
                closeEditProfileModal();
                // Rafraîchir la page pour afficher les nouvelles données
                location.reload();
            }, 1000);
        } else {
            showError('editError', result.message || 'Erreur lors de la mise à jour');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('editError', 'Erreur de connexion au serveur. Veuillez réessayer.');
    }
}

// Sauvegarder les infos streamer
async function handleSaveStreamer() {
    const streamLink = document.getElementById('editStreamLink').value.trim();
    const streamDescription = document.getElementById('editStreamDescription').value.trim();
    const streamPlatform = document.getElementById('editStreamPlatform').value;

    clearMessages();

    // Validation côté client
    if (streamLink && !isValidHttpUrl(streamLink)) {
        showError('editStreamerError', 'Le lien de stream doit être une URL valide (http:// ou https://)');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('stream_link', streamLink);
        formData.append('stream_description', streamDescription);
        formData.append('stream_platform', streamPlatform);

        const response = await fetch('../../Controller/authController.php?action=updateStreamerInfo', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            showSuccess('editStreamerSuccess', result.message || 'Informations mises à jour avec succès');
            
            // Mettre à jour les infos affichées
            if (result.user) {
                document.getElementById('profileStreamLink').textContent = result.user.stream_link || 'Non spécifié';
                document.getElementById('profileStreamDescription').textContent = result.user.stream_description || 'Aucune description';
                
                const platformMap = {
                    'twitch': 'Twitch',
                    'youtube': 'YouTube',
                    'kick': 'Kick',
                    'other': 'Autre'
                };
                document.getElementById('profileStreamPlatform').textContent = platformMap[result.user.stream_platform] || 'Non spécifié';
                
                // Mettre à jour la session
                localStorage.setItem('currentUser', JSON.stringify(result.user));
            }
            
            // Fermer le modal après 1 seconde
            setTimeout(() => {
                closeEditStreamerModal();
                // Rafraîchir la page pour afficher les nouvelles données
                location.reload();
            }, 1000);
        } else {
            showError('editStreamerError', result.message || 'Erreur lors de la mise à jour');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('editStreamerError', 'Erreur de connexion au serveur. Veuillez réessayer.');
    }
}

// ==================== Password Strength Checker ====================
/**
 * Check password strength and update UI indicators
 * Used on register page for real-time password validation
 */
function checkPasswordStrength() {
    const password = document.getElementById('signupPassword').value;
    const strengthFill = document.getElementById('passwordStrengthFill');
    const strengthText = document.getElementById('passwordStrengthText');
    const strengthLabel = document.getElementById('strengthLabel');
    const strengthScore = document.getElementById('strengthScore');
    
    // Éléments des exigences
    const reqLength = document.getElementById('req-length');
    const reqUppercase = document.getElementById('req-uppercase');
    const reqLowercase = document.getElementById('req-lowercase');
    const reqNumber = document.getElementById('req-number');
    const reqSpecial = document.getElementById('req-special');
    
    // Si le mot de passe est vide
    if (password.length === 0) {
        strengthFill.className = 'password-strength-fill';
        strengthText.className = 'password-strength-text';
        strengthLabel.textContent = 'Saisissez un mot de passe';
        strengthScore.textContent = '';
        
        reqLength.classList.remove('met');
        reqUppercase.classList.remove('met');
        reqLowercase.classList.remove('met');
        reqNumber.classList.remove('met');
        reqSpecial.classList.remove('met');
        
        return;
    }
    
    // Calcul du score de force
    let score = 0;
    let requirements = 0;
    
    // Vérifier la longueur (minimum 8 caractères)
    const hasLength = password.length >= 8;
    if (hasLength) {
        score += 20;
        requirements++;
        reqLength.classList.add('met');
    } else {
        reqLength.classList.remove('met');
    }
    
    // Bonus pour longueur supplémentaire
    if (password.length >= 12) score += 10;
    if (password.length >= 16) score += 10;
    
    // Vérifier les majuscules
    const hasUppercase = /[A-Z]/.test(password);
    if (hasUppercase) {
        score += 20;
        requirements++;
        reqUppercase.classList.add('met');
    } else {
        reqUppercase.classList.remove('met');
    }
    
    // Vérifier les minuscules
    const hasLowercase = /[a-z]/.test(password);
    if (hasLowercase) {
        score += 20;
        requirements++;
        reqLowercase.classList.add('met');
    } else {
        reqLowercase.classList.remove('met');
    }
    
    // Vérifier les chiffres
    const hasNumber = /[0-9]/.test(password);
    if (hasNumber) {
        score += 20;
        requirements++;
        reqNumber.classList.add('met');
    } else {
        reqNumber.classList.remove('met');
    }
    
    // Vérifier les caractères spéciaux
    const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
    if (hasSpecial) {
        score += 20;
        requirements++;
        reqSpecial.classList.add('met');
    } else {
        reqSpecial.classList.remove('met');
    }
    
    // Bonus pour la diversité des caractères
    const uniqueChars = new Set(password).size;
    if (uniqueChars >= 8) score += 10;
    
    // Déterminer le niveau de force
    let strength = 'weak';
    let strengthText_label = 'Faible';
    let strengthIcon = '🔴';
    
    if (score >= 80 && requirements >= 4) {
        strength = 'strong';
        strengthText_label = 'Fort';
        strengthIcon = '🟢';
    } else if (score >= 50 && requirements >= 3) {
        strength = 'medium';
        strengthText_label = 'Moyen';
        strengthIcon = '🟡';
    }
    
    // Mettre à jour l'affichage
    strengthFill.className = `password-strength-fill ${strength}`;
    strengthText.className = `password-strength-text ${strength}`;
    strengthLabel.textContent = `${strengthIcon} ${strengthText_label}`;
    strengthScore.textContent = `${requirements}/5 exigences`;
}

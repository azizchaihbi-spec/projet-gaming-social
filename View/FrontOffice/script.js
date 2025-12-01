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
    
    try {
        const response = await fetch('../../Controller/authController.php?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
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
            showError('loginError', result.message);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showError('loginError', 'Erreur de connexion au serveur. Veuillez réessayer.');
    }
}

// Fonctions utilitaires
let selectedAvatarSrc = null;

function selectAvatar(element) {
    const options = document.querySelectorAll('.avatar-option');
    options.forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
    selectedAvatarSrc = element.src;
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

// Gérer la sauvegarde de l'avatar
async function handleSaveAvatar() {
    if (!selectedAvatarSrc) {
        showError('editError', 'Veuillez sélectionner un avatar');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('profile_image', selectedAvatarSrc);
        
        const response = await fetch('../../Controller/authController.php?action=updateProfile', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('editSuccess', result.message);
            // Mettre à jour le localStorage et la session
            const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
            currentUser.profile_image = selectedAvatarSrc;
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            
            // Mettre à jour l'image affichée
            document.getElementById('profileImage').src = selectedAvatarSrc;
            
            setTimeout(() => {
                closeEditProfileModal();
            }, 1000);
        } else {
            showError('editError', result.message);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        showError('editError', 'Erreur lors de la mise à jour');
    }
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

// Système de gestion des utilisateurs

// Initialisation
window.addEventListener('load', function() {
    checkUserSession();
    setTimeout(() => {
        document.getElementById('js-preloader').style.display = 'none';
    }, 1000);
});

// Vérifier si l'utilisateur est connecté
function checkUserSession() {
    const currentUser = localStorage.getItem('currentUser');
    if (currentUser) {
        const user = JSON.parse(currentUser);
        showProfile(user);
    } else {
        showAuth();
    }
}

// Basculer entre les onglets
function switchTab(tab) {
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');
    
    tabs.forEach(t => t.classList.remove('active'));
    forms.forEach(f => f.classList.remove('active'));
    
    if (tab === 'login') {
        tabs[0].classList.add('active');
        document.getElementById('loginForm').classList.add('active');
    } else if (tab === 'signup') {
        tabs[1].classList.add('active');
        document.getElementById('signupForm').classList.add('active');
    } else if (tab === 'forgot') {
        tabs[0].classList.add('active'); // Garder l'onglet connexion actif
        document.getElementById('forgotForm').classList.add('active');
    }
    
    clearMessages();
}

// Gérer l'inscription
function handleSignup() {
    const firstName = document.getElementById('signupFirstName').value.trim();
    const lastName = document.getElementById('signupLastName').value.trim();
    const username = document.getElementById('signupUsername').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    const birthdate = document.getElementById('signupBirthdate').value;
    const gender = document.getElementById('signupGender').value;
    const country = document.getElementById('signupCountry').value;
    const city = document.getElementById('signupCity').value.trim();
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('signupConfirmPassword').value;
    
    clearMessages();
    
    // Validation des champs requis
    if (!firstName || !lastName || !username || !email || !birthdate || !country || !password || !confirmPassword) {
        showError('signupError', 'Veuillez remplir tous les champs obligatoires (*)');
        return;
    }
    
    // Validation de l'âge (minimum 13 ans)
    const today = new Date();
    const birth = new Date(birthdate);
    const age = today.getFullYear() - birth.getFullYear();
    if (age < 13) {
        showError('signupError', 'Vous devez avoir au moins 13 ans pour vous inscrire');
        return;
    }
    
    // Validation du mot de passe
    if (password !== confirmPassword) {
        showError('signupError', 'Les mots de passe ne correspondent pas');
        return;
    }
    
    if (password.length < 6) {
        showError('signupError', 'Le mot de passe doit contenir au moins 6 caractères');
        return;
    }
    
    // Validation de l'email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showError('signupError', 'Veuillez entrer une adresse email valide');
        return;
    }
    
    // Vérifier si l'email ou le username existe déjà
    const users = JSON.parse(localStorage.getItem('users') || '[]');
    if (users.find(u => u.email === email)) {
        showError('signupError', 'Cet email est déjà utilisé');
        return;
    }
    if (users.find(u => u.username === username)) {
        showError('signupError', 'Ce nom d\'utilisateur est déjà pris');
        return;
    }
    
    // Créer le nouvel utilisateur
    const newUser = {
        id: Date.now(),
        firstName: firstName,
        lastName: lastName,
        username: username,
        email: email,
        birthdate: birthdate,
        gender: gender,
        country: country,
        city: city,
        password: password,
        joinDate: new Date().toLocaleDateString('fr-FR'),
        profileImage: 'assets/images/profile.jpg', // Image par défaut
        stats: {
            gamesDownloaded: 0,
            friendsOnline: 0,
            liveStreams: 0,
            clips: 0
        }
    };
    
    // Sauvegarder
    users.push(newUser);
    localStorage.setItem('users', JSON.stringify(users));
    
    showSuccess('signupSuccess', '✓ Inscription réussie! Vous pouvez maintenant vous connecter.');
    
    // Rediriger vers login après 2 secondes
    setTimeout(() => {
        switchTab('login');
        document.getElementById('loginEmail').value = email;
    }, 2000);
}

// Gérer la connexion
function handleLogin() {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    
    clearMessages();
    
    if (!email || !password) {
        showError('loginError', 'Veuillez remplir tous les champs');
        return;
    }
    
    // Vérifier les identifiants
    const users = JSON.parse(localStorage.getItem('users') || '[]');
    const user = users.find(u => u.email === email && u.password === password);
    
    if (!user) {
        showError('loginError', 'Email ou mot de passe incorrect');
        return;
    }
    
    // Connexion réussie
    localStorage.setItem('currentUser', JSON.stringify(user));
    showSuccess('loginSuccess', '✓ Connexion réussie! Bienvenue ' + user.username + '!');
    
    setTimeout(() => {
        showProfile(user);
    }, 1000);
}

// Gérer le mot de passe oublié (simulation)
function handleForgotPassword() {
    const email = document.getElementById('forgotEmail').value.trim();
    
    clearMessages();
    
    if (!email) {
        showError('forgotError', 'Veuillez entrer votre email');
        return;
    }
    
    const users = JSON.parse(localStorage.getItem('users') || '[]');
    const user = users.find(u => u.email === email);
    
    if (!user) {
        showError('forgotError', 'Aucun compte trouvé avec cet email');
        return;
    }
    
    // Simulation d'envoi d'email
    console.log('Lien de réinitialisation envoyé à ' + email);
    showSuccess('forgotSuccess', '✓ Un lien de réinitialisation a été envoyé à votre email.');
    
    setTimeout(() => {
        switchTab('login');
    }, 2000);
}

// Gérer la déconnexion
function handleLogout() {
    if (confirm('Êtes-vous sûr de vouloir vous déconnecter?')) {
        localStorage.removeItem('currentUser');
        showAuth();
    }
}

// Afficher le profil
function showProfile(user) {
    document.getElementById('authSection').style.display = 'none';
    document.getElementById('profileSection').classList.add('active');
    
    // Remplir les informations du profil
    document.getElementById('profileUsername').textContent = user.username;
    document.getElementById('profileEmail').textContent = user.email;
    document.getElementById('profileFullName').textContent = user.firstName + ' ' + user.lastName;
    document.getElementById('profileBirthdate').textContent = new Date(user.birthdate).toLocaleDateString('fr-FR');
    
    // Genre
    const genderMap = {
        'male': 'Homme',
        'female': 'Femme',
        'other': 'Autre',
        'prefer-not': 'Non spécifié'
    };
    document.getElementById('profileGender').textContent = genderMap[user.gender] || 'Non spécifié';
    
    // Localisation
    let location = user.country || 'Non spécifié';
    if (user.city) location = user.city + ', ' + location;
    document.getElementById('profileLocation').textContent = location;
    
    document.getElementById('profileJoinDate').textContent = user.joinDate;
    
    // Image de profil
    if (user.profileImage) {
        document.getElementById('profileImage').src = user.profileImage;
    }
    
    // Stats
    document.getElementById('statGames').textContent = user.stats.gamesDownloaded;
    document.getElementById('statFriends').textContent = user.stats.friendsOnline;
    document.getElementById('statStreams').textContent = user.stats.liveStreams || 'None';
    document.getElementById('statClips').textContent = user.stats.clips;
}

// Afficher le formulaire d'authentification
function showAuth() {
    document.getElementById('authSection').style.display = 'block';
    document.getElementById('profileSection').classList.remove('active');
}

// Afficher une erreur
function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    errorElement.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + message;
    errorElement.style.display = 'block';
}

// Afficher un succès
function showSuccess(elementId, message) {
    const successElement = document.getElementById(elementId);
    successElement.innerHTML = '<i class="fa fa-check-circle"></i> ' + message;
    successElement.style.display = 'block';
}

// Effacer les messages
function clearMessages() {
    document.querySelectorAll('.error-message, .success-message').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
}

// Ouvrir le modal de modification de profil
function openEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'flex';
    // Pré-sélectionner l'avatar actuel si possible
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (currentUser && currentUser.profileImage) {
        const options = document.querySelectorAll('.avatar-option');
        options.forEach(option => {
            if (option.src === currentUser.profileImage) {
                option.classList.add('selected');
            } else {
                option.classList.remove('selected');
            }
        });
    }
}

// Fermer le modal
function closeEditProfileModal() {
    document.getElementById('editProfileModal').style.display = 'none';
    clearMessages();
}

// Sélectionner un avatar
let selectedAvatarSrc = null;
function selectAvatar(element) {
    const options = document.querySelectorAll('.avatar-option');
    options.forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
    selectedAvatarSrc = element.src;
}

// Enregistrer l'avatar choisi
function handleSaveAvatar() {
    if (!selectedAvatarSrc) {
        showError('editError', 'Veuillez sélectionner un logo');
        return;
    }
    
    const users = JSON.parse(localStorage.getItem('users') || '[]');
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    
    const userIndex = users.findIndex(u => u.id === currentUser.id);
    if (userIndex !== -1) {
        users[userIndex].profileImage = selectedAvatarSrc;
        localStorage.setItem('users', JSON.stringify(users));
        currentUser.profileImage = selectedAvatarSrc;
        localStorage.setItem('currentUser', JSON.stringify(currentUser));
        
        document.getElementById('profileImage').src = selectedAvatarSrc;
        showSuccess('editSuccess', '✓ Logo mis à jour avec succès!');
        
        setTimeout(() => {
            closeEditProfileModal();
        }, 1000);
    } else {
        showError('editError', 'Erreur lors de la mise à jour');
    }
}
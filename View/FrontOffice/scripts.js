// =============================================
// CONFIGURATION DES URLS - À ADAPTER SI BESOIN
// =============================================

// METHODE 1: URLs hardcodées (plus fiable)
const BASE_URL = 'http://localhost/play to help mvc f';
const CONTROLLER_URL = BASE_URL + '/Controller/authController.php';

// METHODE 2: URLs dynamiques (décommentez si la méthode 1 ne fonctionne pas)
// const getBasePath = () => {
//     return '/play to help mvc f'; // Ajustez selon votre installation
// };
// const BASE_URL = window.location.origin + getBasePath();
// const CONTROLLER_URL = BASE_URL + '/Controller/authController.php';

console.log('=== CONFIGURATION URLS ===');
console.log('Base URL:', BASE_URL);
console.log('Controller URL:', CONTROLLER_URL);

// =============================================
// INITIALISATION
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - System initialized');
    console.log('Controller endpoint:', CONTROLLER_URL);
    
    initializeStreamerFields();
    
    setTimeout(() => {
        const preloader = document.getElementById('js-preloader');
        if (preloader) {
            preloader.style.display = 'none';
        }
    }, 1000);
});

// =============================================
// FONCTIONS PRINCIPALES
// =============================================

function initializeStreamerFields() {
    const roleSelect = document.getElementById('signupRole');
    const streamerFields = document.getElementById('streamerFields');
    
    if (roleSelect && streamerFields) {
        roleSelect.addEventListener('change', toggleStreamerFields);
        toggleStreamerFields();
    }
}

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

async function handleSignup() {
    console.log('🚀 Starting signup process...');
    
    const formData = {
        firstName: document.getElementById('signupFirstName').value.trim(),
        lastName: document.getElementById('signupLastName').value.trim(),
        username: document.getElementById('signupUsername').value.trim(),
        email: document.getElementById('signupEmail').value.trim(),
        birthdate: document.getElementById('signupBirthdate').value,
        gender: document.getElementById('signupGender').value,
        country: document.getElementById('signupCountry').value,
        city: document.getElementById('signupCity').value.trim(),
        role: document.getElementById('signupRole').value,
        password: document.getElementById('signupPassword').value,
        confirmPassword: document.getElementById('signupConfirmPassword').value
    };
    
    if (formData.role === 'streamer') {
        formData.streamLink = document.getElementById('signupStreamLink').value.trim();
        formData.streamDescription = document.getElementById('signupStreamDescription').value.trim();
        formData.streamPlatform = document.getElementById('signupStreamPlatform').value;
    } else {
        formData.streamLink = '';
        formData.streamDescription = '';
        formData.streamPlatform = '';
    }
    
    clearMessages();
    
    // Validation
    if (!validateSignup(formData)) return;
    
    // Préparer les données pour l'API
    const apiData = { ...formData };
    delete apiData.confirmPassword;
    
    console.log('📤 Sending to:', CONTROLLER_URL);
    console.log('Data:', apiData);
    
    try {
        const response = await fetch(CONTROLLER_URL + '?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(apiData)
        });
        
        console.log('📥 Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status} - ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📋 API Response:', result);
        
        if (result.success) {
            showSuccess('signupSuccess', result.message);
            setTimeout(() => window.location.href = 'login.php', 2000);
        } else {
            showError('signupError', result.message);
        }
        
    } catch (error) {
        console.error('❌ Fetch error:', error);
        showError('signupError', 'Erreur réseau: ' + error.message);
    }
}

async function handleLogin() {
    console.log('🔐 Starting login process...');
    
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    
    clearMessages();
    
    if (!email || !password) {
        showError('loginError', 'Veuillez remplir tous les champs');
        return;
    }
    
    console.log('📤 Sending to:', CONTROLLER_URL + '?action=login');
    
    try {
        const response = await fetch(CONTROLLER_URL + '?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        
        console.log('📥 Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status} - ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('📋 API Response:', result);
        
        if (result.success) {
            showSuccess('loginSuccess', result.message);
            localStorage.setItem('currentUser', JSON.stringify(result.user));
            setTimeout(() => window.location.href = 'profile.php', 1000);
        } else {
            showError('loginError', result.message);
        }
        
    } catch (error) {
        console.error('❌ Fetch error:', error);
        showError('loginError', 'Erreur réseau: ' + error.message);
    }
}

// =============================================
// FONCTIONS UTILITAIRES
// =============================================

let selectedAvatarSrc = null;

function selectAvatar(element) {
    document.querySelectorAll('.avatar-option').forEach(opt => opt.classList.remove('selected'));
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

function validateSignup(data) {
    if (!data.firstName || !data.lastName || !data.username || !data.email || 
        !data.birthdate || !data.country || !data.role || !data.password || !data.confirmPassword) {
        showError('signupError', 'Veuillez remplir tous les champs obligatoires (*)');
        return false;
    }
    
    const age = new Date().getFullYear() - new Date(data.birthdate).getFullYear();
    if (age < 13) {
        showError('signupError', 'Vous devez avoir au moins 13 ans');
        return false;
    }
    
    if (data.password !== data.confirmPassword) {
        showError('signupError', 'Les mots de passe ne correspondent pas');
        return false;
    }
    
    if (data.password.length < 6) {
        showError('signupError', 'Le mot de passe doit contenir au moins 6 caractères');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(data.email)) {
        showError('signupError', 'Email invalide');
        return false;
    }
    
    return true;
}

function showError(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<i class="fa fa-exclamation-circle"></i> ' + message;
        element.style.display = 'block';
    }
}

function showSuccess(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<i class="fa fa-check-circle"></i> ' + message;
        element.style.display = 'block';
    }
}

function clearMessages() {
    document.querySelectorAll('.error-message, .success-message').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
}

async function handleSaveAvatar() {
    if (!selectedAvatarSrc) {
        showError('editError', 'Veuillez sélectionner un avatar');
        return;
    }
    
    try {
        const response = await fetch(CONTROLLER_URL + '?action=updateProfile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ profile_image: selectedAvatarSrc })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('editSuccess', result.message);
            const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
            currentUser.profile_image = selectedAvatarSrc;
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            document.getElementById('profileImage').src = selectedAvatarSrc;
            setTimeout(closeEditProfileModal, 1000);
        } else {
            showError('editError', result.message);
        }
        
    } catch (error) {
        console.error('Error:', error);
        showError('editError', 'Erreur: ' + error.message);
    }
}

async function handleSaveStreamer() {
    const data = {
        stream_link: document.getElementById('editStreamLink').value.trim(),
        stream_description: document.getElementById('editStreamDescription').value.trim(),
        stream_platform: document.getElementById('editStreamPlatform').value
    };
    
    try {
        const response = await fetch(CONTROLLER_URL + '?action=updateStreamerInfo', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showSuccess('editStreamerSuccess', result.message);
            const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
            Object.assign(currentUser, data);
            localStorage.setItem('currentUser', JSON.stringify(currentUser));
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showError('editStreamerError', result.message);
        }
        
    } catch (error) {
        console.error('Error:', error);
        showError('editStreamerError', 'Erreur: ' + error.message);
    }
}
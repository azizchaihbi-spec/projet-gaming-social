// Shared utilities and functions
document.addEventListener('DOMContentLoaded', () => {
    // Shared initialization for BackOffice pages
    console.log('Dashboard loaded');

    // wire up create user form validation if present
    const createForm = document.getElementById('createUserForm');
    if (createForm) {
        createForm.addEventListener('submit', validateCreateUserForm);
    }

    // wire up modify user form validation if present
    const modifForm = document.getElementById('modifUserForm');
    if (modifForm) {
        modifForm.addEventListener('submit', validateModifyUserForm);
    }

    // wire up role -> streamer fields toggle
    const roleSelect = document.querySelector('select[name="role"]');
    if (roleSelect) {
        roleSelect.addEventListener('change', toggleStreamerFields);
        // initialize visibility on load
        toggleStreamerFields.call(roleSelect);
    }

    // run feather icons replacement if available
    if (window.feather && typeof window.feather.replace === 'function') {
        window.feather.replace();
    }
});

// Validation helpers and form logic
function isValidEmail(email) {
    if (!email) return false;
    // simple, permissive email regex
    const re = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
    return re.test(email);
}

function isValidURL(url) {
    if (!url) return false;
    try {
        new URL(url);
        return true;
    } catch (e) {
        return false;
    }
}

function showClientErrors(errors) {
    const container = document.getElementById('clientErrors');
    if (!container) return;
    const html = [];
    html.push('<div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4 mb-6">');
    html.push('<h3 class="text-red-400 font-bold mb-2">Erreurs :</h3>');
    html.push('<ul class="list-disc list-inside">');
    errors.forEach(err => { html.push('<li class="text-red-300">' + escapeHtml(err) + '</li>'); });
    html.push('</ul></div>');
    container.innerHTML = html.join('\n');
}

function clearClientErrors() {
    const container = document.getElementById('clientErrors');
    if (container) container.innerHTML = '';
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function toggleStreamerFields() {
    const streamerFields = document.getElementById('streamer-fields');
    if (!streamerFields) return;
    const val = (this && this.value) ? this.value : (document.querySelector('select[name="role"]') || {}).value;
    if (val === 'streamer') {
        streamerFields.classList.remove('hidden');
    } else {
        streamerFields.classList.add('hidden');
    }
}

function validateCreateUserForm(e) {
    // if called as event handler, e is Event; if called directly, first arg may be event
    if (e && e.preventDefault) e = e;
    const form = document.getElementById('createUserForm');
    if (!form) return true;
    const errors = [];

    const firstName = form.elements['first_name']?.value?.trim() || '';
    const lastName = form.elements['last_name']?.value?.trim() || '';
    const birthdate = form.elements['birthdate']?.value?.trim() || '';
    const username = form.elements['username']?.value?.trim() || '';
    const email = form.elements['email']?.value?.trim() || '';
    const role = form.elements['role']?.value || '';
    const password = form.elements['password']?.value || '';
    const streamLink = form.elements['stream_link']?.value?.trim() || '';

    if (!isValidFirstName(firstName)) errors.push('Prénom: 2-50 caractères requis');
    if (!isValidLastName(lastName)) errors.push('Nom: 2-50 caractères requis');
    if (!isValidUsername(username)) errors.push('Username: 3-30 caractères (alphanumériques, -, _)');
    if (!email) errors.push('Email requis');
    else if (!isValidEmail(email)) errors.push('Format d\'email invalide');
    if (!role) errors.push('Rôle requis');
    if (!isValidPassword(password)) errors.push('Mot de passe: min 6 caractères, 1 majuscule, 1 minuscule, 1 chiffre');
    // birthdate required and must be > 13 years
    if (!birthdate) errors.push('Date de naissance requise');
    else if (!isAtLeastAge(birthdate, 13)) errors.push('Vous devez avoir plus de 13 ans');
    if (role === 'streamer') {
        if (!streamLink) errors.push('Lien de stream requis pour les streamers');
        else if (!isValidURL(streamLink)) errors.push('Format de lien de stream invalide');
    }

    if (errors.length > 0) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        showClientErrors(errors);
        return false;
    }

    clearClientErrors();
    return true;
}

// Animation for stat cards
const animateStatCards = () => {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        card.style.transform = 'translateY(0)';
        
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
            card.style.boxShadow = '0 10px 15px rgba(16, 185, 129, 0.3)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
            card.style.boxShadow = '0 0 10px rgba(16, 185, 129, 0.2)';
        });
    });
};
function isAdult(dateString) {
    const today = new Date();
    const birthDate = new Date(dateString);
    let age = today.getFullYear() - birthDate.getFullYear();
    
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age >= 18;
}

// Parse flexible date strings (ISO YYYY-MM-DD or DD/MM/YYYY). Returns Date or null.
function parseDateInput(dateString) {
    if (!dateString) return null;
    // Trim whitespace
    const s = dateString.trim();
    // ISO format
    const iso = Date.parse(s);
    if (!isNaN(iso)) return new Date(iso);

    // Try DD/MM/YYYY or D/M/YYYY
    const parts = s.split('/');
    if (parts.length === 3) {
        const day = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const year = parseInt(parts[2], 10);
        const d = new Date(year, month, day);
        if (!isNaN(d.getTime())) return d;
    }

    return null;
}

// Check age strictly greater than minAge (e.g., minAge=13 means >13 years old)
function isAtLeastAge(dateString, minAge) {
    const birthDate = parseDateInput(dateString);
    if (!birthDate) return false;
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age > minAge;
}

// Validation functions for standard fields
function isValidFirstName(firstName) {
    if (!firstName || typeof firstName !== 'string') return false;
    const trimmed = firstName.trim();
    // Only alphabetic characters, spaces, hyphens, apostrophes and accented letters
    const nameRe = /^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/u;
    return trimmed.length >= 2 && trimmed.length <= 50 && nameRe.test(trimmed);
}

function isValidLastName(lastName) {
    if (!lastName || typeof lastName !== 'string') return false;
    const trimmed = lastName.trim();
    const nameRe = /^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/u;
    return trimmed.length >= 2 && trimmed.length <= 50 && nameRe.test(trimmed);
}

function isValidUsername(username) {
    if (!username || typeof username !== 'string') return false;
    const trimmed = username.trim();
    // alphanumeric, underscore, hyphen; 3-30 chars
    return trimmed.length >= 3 && trimmed.length <= 30 && /^[a-zA-Z0-9_-]+$/.test(trimmed);
}

function isValidPassword(password) {
    if (!password || typeof password !== 'string') return false;
    // min 6 chars, at least one uppercase, one lowercase, one digit
    return password.length >= 6 && /[A-Z]/.test(password) && /[a-z]/.test(password) && /\d/.test(password);
}

function validateModifyUserForm(e) {
    if (e && e.preventDefault) e = e;
    const form = document.getElementById('modifUserForm');
    if (!form) return true;
    const errors = [];

    const firstName = form.elements['first_name']?.value?.trim() || '';
    const lastName = form.elements['last_name']?.value?.trim() || '';
    const username = form.elements['username']?.value?.trim() || '';
    const birthdate = form.elements['birthdate']?.value?.trim() || '';
    const email = form.elements['email']?.value?.trim() || '';
    const role = form.elements['role']?.value || '';
    const password = form.elements['password']?.value || '';
    const streamLink = form.elements['stream_link']?.value?.trim() || '';

    if (!isValidFirstName(firstName)) errors.push('Prénom: 2-50 caractères requis');
    if (!isValidLastName(lastName)) errors.push('Nom: 2-50 caractères requis');
    if (!isValidUsername(username)) errors.push('Username: 3-30 caractères (alphanumériques, -, _)');
    if (!email) errors.push('Email requis');
    else if (!isValidEmail(email)) errors.push('Format d\'email invalide');
    if (!role) errors.push('Rôle requis');
    // password is optional on modify (leave empty to keep current)
    if (password && !isValidPassword(password)) errors.push('Mot de passe: min 6 caractères, 1 majuscule, 1 minuscule, 1 chiffre');
    if (role === 'streamer') {
        if (streamLink && !isValidURL(streamLink)) errors.push('Format de lien de stream invalide');
    }
    // birthdate required and must be > 13 years
    if (!birthdate) errors.push('Date de naissance requise');
    else if (!isAtLeastAge(birthdate, 13)) errors.push('Vous devez avoir plus de 13 ans');

    if (errors.length > 0) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        showClientErrors(errors);
        return false;
    }

    clearClientErrors();
    return true;
}

// Initialize animations when page loads
window.addEventListener('load', () => {
    animateStatCards();
});

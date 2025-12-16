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
    statCards.forEach((card) => {
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

// ============================================================================
// DASHBOARD INDEX FUNCTIONS - Table Management, Pagination, Ban System
// ============================================================================

// Variables pour gérer l'état du tableau
let tableState = {
    currentPage: 1,
    rowsPerPage: 10,
    sortColumn: 'created_at',
    sortOrder: 'DESC',
    searchQuery: '',
    roleFilter: '',
    statusFilter: ''
};

/**
 * Charge les données du tableau depuis le serveur
 */
function loadTableData() {
    const params = new URLSearchParams({
        page: tableState.currentPage,
        rowsPerPage: tableState.rowsPerPage,
        sortColumn: tableState.sortColumn,
        sortOrder: tableState.sortOrder,
        search: tableState.searchQuery,
        role: tableState.roleFilter,
        status: tableState.statusFilter
    });

    // Afficher un chargement
    const tbody = document.getElementById('tableBody');
    if (tbody) {
        tbody.innerHTML = `<tr>
            <td colspan="7" class="py-8 text-center text-gray-400">
                <i data-feather="loader" class="w-12 h-12 mx-auto mb-4 animate-spin"></i>
                <p>Chargement...</p>
            </td>
        </tr>`;
    }

    // Appel AJAX vers l'API
    fetch(`indexsinda.php?action=getTableData&${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderTable(data.data);
                updatePaginationUI(data.pagination);
            } else {
                showError('Erreur lors du chargement des données: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur de communication avec le serveur');
        });
}

/**
 * Affiche les données dans le tableau
 */
function renderTable(users) {
    const tbody = document.getElementById('tableBody');
    const isCardMode = window.innerWidth < 1100;
    
    if (!users || users.length === 0) {
        const emptyHtml = `<div style="grid-column: 1/-1; text-align: center; padding: 2rem;">
            <i data-feather="users" class="w-12 h-12 mx-auto mb-4" style="margin-bottom: 1rem;"></i>
            <p style="color: #9ca3af;">Aucun utilisateur trouvé</p>
        </div>`;
        
        if (isCardMode) {
            tbody.parentElement.innerHTML = emptyHtml;
        } else {
            tbody.innerHTML = `<tr>
                <td colspan="5" class="text-center py-8" style="padding: 2rem 1.25rem; vertical-align: middle;">
                    <i data-feather="users" class="w-12 h-12 mx-auto mb-4"></i>
                    <p style="color: #9ca3af;">Aucun utilisateur trouvé</p>
                </td>
            </tr>`;
        }
        feather.replace({ width: 20, height: 20 });
        return;
    }

    let html = '';
    users.forEach(user => {
        const isBanned = user.is_banned == 1;
        const banType = user.ban_type;
        const bannedUntil = user.banned_until;

        // Déterminer le badge de statut
        let statusBadge = '<span class="status-badge active">✅ Actif</span>';
        let statusText = 'Actif';
        
        if (isBanned) {
            if (banType === 'permanent') {
                statusBadge = '<span class="status-badge banned">🚫 Banni</span>';
                statusText = 'Banni';
            } else if (banType === 'soft' && bannedUntil) {
                const until = new Date(bannedUntil);
                const now = new Date();
                if (until > now) {
                    const untilStr = until.toLocaleDateString('fr-FR') + ' ' + until.toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
                    statusBadge = `<span class="status-badge suspended" title="Jusqu'au ${untilStr}">⏸️ Suspendu</span>`;
                    statusText = 'Suspendu';
                }
            }
        }

        // Déterminer le badge de rôle
        let roleBadge = '<span class="role-badge viewer">👁️ Viewer</span>';
        let roleText = 'Viewer';
        if (user.role === 'streamer') {
            roleBadge = '<span class="role-badge streamer">🎥 Streamer</span>';
            roleText = 'Streamer';
        } else if (user.role === 'admin') {
            roleBadge = '<span class="role-badge admin">⚙️ Admin</span>';
            roleText = 'Admin';
        }

        // Déterminer les boutons d'actions
        let actionButtons = '';
        if (isBanned) {
            actionButtons = `
                <a href="indexsinda.php?action=edit&id=${user.id}" title="Modifier">
                    <i data-feather="edit-2" class="w-5 h-5"></i>
                </a>
                <button onclick="unbanUser(${user.id})" title="Débannir">
                    <i data-feather="unlock" class="w-5 h-5"></i>
                </button>
                <a href="indexsinda.php?action=delete&id=${user.id}" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')" title="Supprimer">
                    <i data-feather="trash-2" class="w-5 h-5"></i>
                </a>
                <a href="indexsinda.php?action=view&id=${user.id}" title="Voir profil">
                    <i data-feather="eye" class="w-5 h-5"></i>
                </a>
            `;
        } else {
            actionButtons = `
                <a href="indexsinda.php?action=edit&id=${user.id}" title="Modifier">
                    <i data-feather="edit-2" class="w-5 h-5"></i>
                </a>
                <button onclick="openBanModal(${user.id}, '${user.username.replace(/'/g, "\\'")}')" title="Bannir">
                    <i data-feather="shield-off" class="w-5 h-5"></i>
                </button>
                <a href="indexsinda.php?action=delete&id=${user.id}" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')" title="Supprimer">
                    <i data-feather="trash-2" class="w-5 h-5"></i>
                </a>
                <a href="indexsinda.php?action=view&id=${user.id}" title="Voir profil">
                    <i data-feather="eye" class="w-5 h-5"></i>
                </a>
            `;
        }

        // Mode Card
        if (isCardMode) {
            html += `
                <div class="user-row-card">
                    <div class="card-header">
                        <div class="card-user-info">
                            <div style="font-size: 0.75rem; color: #6b7280; font-weight: 600; min-width: 50px;">ID: #${user.id}</div>
                            <div class="card-avatar">${user.first_name.charAt(0).toUpperCase()}</div>
                            <div class="card-user-details">
                                <div class="card-user-name">${user.full_name}</div>
                                <div class="card-user-username">@${user.username}</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-field">
                            <div class="card-field-label">Email</div>
                            <div class="card-field-value">${user.email}</div>
                        </div>
                        <div class="card-field">
                            <div class="card-field-label">Pays</div>
                            <div class="card-field-value">${user.country || 'N/A'}</div>
                        </div>
                        <div class="card-field">
                            <div class="card-field-label">Inscription</div>
                            <div class="card-field-value">${user.join_date}</div>
                        </div>
                        <div class="card-field">
                            <div class="card-field-label">Rôle</div>
                            <div class="card-field-value">${roleText}</div>
                        </div>
                    </div>
                    <div class="card-badges">
                        ${roleBadge}
                        ${statusBadge}
                    </div>
                    <div class="card-actions">
                        ${actionButtons}
                    </div>
                </div>
            `;
        } else {
            // Mode Table
            html += `
                <tr>
                    <td class="sticky-left id-col">#${user.id}</td>
                    <td class="sticky-left user-col">
                        <div class="user-cell">
                            <div class="user-avatar">${user.first_name.charAt(0).toUpperCase()}</div>
                            <div class="user-info">
                                <div class="user-name">${user.full_name}</div>
                                <div class="user-username">@${user.username}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="email-cell">${user.email}</div>
                        <div class="email-country">${user.country || 'N/A'}</div>
                    </td>
                    <td>
                        ${roleBadge}
                    </td>
                    <td>
                        ${statusBadge}
                    </td>
                    <td class="sticky-right" style="text-align: center;">
                        <div class="actions-cell">
                            ${actionButtons}
                        </div>
                    </td>
                </tr>
            `;
        }
    });

    if (isCardMode) {
        // Clear table and insert cards
        tbody.parentElement.innerHTML = `<div style="display: grid; gap: 0; padding: 0;">${html}</div>`;
    } else {
        tbody.innerHTML = html;
    }
    
    feather.replace({ width: 20, height: 20 });

    // Animation au survol et scroll hints
    if (!isCardMode) {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(8px)';
                this.style.transition = 'transform 0.2s ease';
            });
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });

        // Initialize scroll hints and add scroll listener
        setTimeout(() => {
            updateScrollHints();
            const container = document.getElementById('tableScrollContainer');
            if (container) {
                container.addEventListener('scroll', updateScrollHints, { passive: true });
            }
        }, 100);
    }
}

/**
 * Met à jour l'UI de pagination
 */
function updatePaginationUI(pagination) {
    const rangeStart = document.getElementById('rangeStart');
    const rangeEnd = document.getElementById('rangeEnd');
    const totalVisible = document.getElementById('totalVisible');
    const pageNumbers = document.getElementById('pageNumbers');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');

    if (rangeStart) rangeStart.textContent = pagination.startRow;
    if (rangeEnd) rangeEnd.textContent = pagination.endRow;
    if (totalVisible) totalVisible.textContent = pagination.totalRows;

    // Mettre à jour les boutons prev/next
    if (prevPageBtn) {
        if (pagination.currentPage === 1) {
            prevPageBtn.disabled = true;
            prevPageBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            prevPageBtn.disabled = false;
            prevPageBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    if (nextPageBtn) {
        if (pagination.currentPage === pagination.totalPages || pagination.totalPages === 0) {
            nextPageBtn.disabled = true;
            nextPageBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            nextPageBtn.disabled = false;
            nextPageBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // Générer les numéros de page
    if (pageNumbers) {
        pageNumbers.innerHTML = '';
        
        for (let i = 1; i <= pagination.totalPages; i++) {
            if (i === 1 || i === pagination.totalPages || (i >= pagination.currentPage - 1 && i <= pagination.currentPage + 1)) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className = i === pagination.currentPage 
                    ? 'px-4 py-2 bg-cyan-600 rounded-lg text-white font-bold'
                    : 'px-4 py-2 bg-gray-800 rounded-lg text-gray-300 hover:bg-gray-700 transition';
                pageBtn.onclick = () => goToPage(i);
                pageNumbers.appendChild(pageBtn);
            } else if (i === pagination.currentPage - 2 || i === pagination.currentPage + 2) {
                const dots = document.createElement('span');
                dots.textContent = '...';
                dots.className = 'px-2 text-gray-500';
                pageNumbers.appendChild(dots);
            }
        }
    }
}

/**
 * Fonction de tri - appelée au clic sur les en-têtes
 */
function sortTable(columnName) {
    // Si on clique sur la même colonne, inverser l'ordre
    if (tableState.sortColumn === columnName) {
        tableState.sortOrder = tableState.sortOrder === 'ASC' ? 'DESC' : 'ASC';
    } else {
        tableState.sortColumn = columnName;
        tableState.sortOrder = 'ASC';
    }

    // Mettre à jour les icônes de tri
    document.querySelectorAll('th .sort-icon').forEach(icon => {
        const column = icon.getAttribute('data-column');
        if (column === columnName) {
            icon.classList.remove('opacity-50');
            icon.classList.add('opacity-100');
            const featherIcon = icon.querySelector('i');
            if (featherIcon) {
                featherIcon.setAttribute('data-feather', tableState.sortOrder === 'ASC' ? 'chevron-up' : 'chevron-down');
            }
        } else {
            icon.classList.add('opacity-50');
            icon.classList.remove('opacity-100');
            const featherIcon = icon.querySelector('i');
            if (featherIcon) {
                featherIcon.setAttribute('data-feather', 'chevrons-up-down');
            }
        }
    });

    feather.replace({ width: 16, height: 16 });

    // Retourner à la page 1
    tableState.currentPage = 1;
    loadTableData();
}

/**
 * Filtre les données
 */
function applyFilters() {
    tableState.searchQuery = document.getElementById('userSearch')?.value.trim() || '';
    tableState.roleFilter = document.getElementById('roleFilter')?.value || '';
    tableState.statusFilter = document.getElementById('statusFilter')?.value || '';
    tableState.currentPage = 1;
    
    loadTableData();
}

/**
 * Va à une page spécifique
 */
function goToPage(page) {
    tableState.currentPage = page;
    loadTableData();
}

/**
 * Affiche une erreur
 */
function showError(message) {
    const tbody = document.getElementById('tableBody');
    if (tbody) {
        tbody.innerHTML = `<tr>
            <td colspan="7" class="py-8 text-center">
                <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4 inline-block">
                    <p class="text-red-400">${message}</p>
                </div>
            </td>
        </tr>`;
    }
}

/**
 * Fonction de scroll automatique vers la colonne Actions
 */
function scrollTableToRight() {
    const container = document.getElementById('tableContainer');
    if (container) {
        container.scrollTo({
            left: container.scrollWidth,
            behavior: 'smooth'
        });
    }
}

/**
 * Fonction de scroll automatique vers le début du tableau
 */
function scrollTableToLeft() {
    const container = document.getElementById('tableContainer');
    if (container) {
        container.scrollTo({
            left: 0,
            behavior: 'smooth'
        });
    }
}

// Fonction pour ouvrir le modal de bannissement
function openBanModal(userId, username) {
    const modal = document.createElement('div');
    modal.id = 'banModal';
    modal.className = 'fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-gray-900 border-2 border-cyan-500/50 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-cyan-400">🚫 Bannir l'utilisateur</h3>
                <button onclick="closeBanModal()" class="text-gray-400 hover:text-white text-2xl">&times;</button>
            </div>
            
            <div class="mb-6 p-4 bg-orange-500/10 border border-orange-500/30 rounded-lg">
                <p class="text-orange-300">Vous êtes sur le point de bannir <strong>@${username}</strong></p>
            </div>

            <form id="banForm" onsubmit="return submitBan(event, ${userId})">
                <div class="mb-4">
                    <label class="block text-cyan-400 mb-2 font-semibold">Type de bannissement</label>
                    <select id="banType" name="ban_type" class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500" onchange="toggleDateField()">
                        <option value="permanent">🚫 Bannissement permanent</option>
                        <option value="soft">⏸️ Suspension temporaire</option>
                    </select>
                </div>

                <div id="dateField" class="mb-4 hidden">
                    <label class="block text-cyan-400 mb-2 font-semibold">Date d'expiration</label>
                    <input type="datetime-local" id="bannedUntil" name="banned_until" 
                           class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500">
                    <p class="text-gray-400 text-sm mt-1">L'utilisateur sera automatiquement débanni à cette date</p>
                </div>

                <div class="mb-6">
                    <label class="block text-cyan-400 mb-2 font-semibold">Raison du bannissement</label>
                    <textarea id="banReason" name="ban_reason" rows="3" 
                              class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 resize-none"
                              placeholder="Ex: Violation des règles, spam, comportement inapproprié..."></textarea>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="closeBanModal()" 
                            class="flex-1 bg-gray-700 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 bg-red-600 hover:bg-red-500 text-white px-6 py-3 rounded-lg font-semibold transition">
                        Confirmer le bannissement
                    </button>
                </div>
            </form>
        </div>
    `;
    document.body.appendChild(modal);
}

function closeBanModal() {
    const modal = document.getElementById('banModal');
    if (modal) modal.remove();
}

function toggleDateField() {
    const banType = document.getElementById('banType').value;
    const dateField = document.getElementById('dateField');
    const dateInput = document.getElementById('bannedUntil');
    
    if (banType === 'soft') {
        dateField.classList.remove('hidden');
        dateInput.required = true;
        // Définir la date minimum à maintenant
        const now = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(now.getDate() + 1);
        dateInput.min = now.toISOString().slice(0, 16);
    } else {
        dateField.classList.add('hidden');
        dateInput.required = false;
    }
}

function submitBan(event, userId) {
    event.preventDefault();
    
    // On encode les données en x-www-form-urlencoded pour que PHP les lise dans $_POST
    const banType = document.getElementById('banType').value;
    const banReason = document.getElementById('banReason').value;
    let bannedUntil = '';
    if (banType === 'soft') {
        bannedUntil = document.getElementById('bannedUntil').value;
        if (!bannedUntil) {
            alert('Veuillez sélectionner une date d\'expiration pour la suspension temporaire');
            return false;
        }
    }
    const params = new URLSearchParams();
    params.append('user_id', userId);
    params.append('ban_type', banType);
    params.append('ban_reason', banReason);
    if (banType === 'soft') {
        params.append('banned_until', bannedUntil);
    }

    fetch('indexsinda.php?action=ban', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params.toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeBanModal();
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors du bannissement');
    });

    return false;
}

function unbanUser(userId) {
    if (!confirm('Êtes-vous sûr de vouloir débannir cet utilisateur ?')) {
        return;
    }

    const formData = new FormData();
    formData.append('user_id', userId);

    fetch('indexsinda.php?action=unban', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors du débannissement');
    });
}

/**
 * Gère les scroll hints visuels pour indiquer qu'il y a du contenu à gauche/droite
 */
function updateScrollHints() {
    const container = document.getElementById('tableScrollContainer');
    if (!container) return;

    const leftHint = document.querySelector('.table-scroll-hint.left');
    const rightHint = document.querySelector('.table-scroll-hint.right');

    if (!leftHint || !rightHint) return;

    // Montrer l'indice gauche si on peut scroller à gauche
    if (container.scrollLeft > 0) {
        leftHint.classList.add('visible');
    } else {
        leftHint.classList.remove('visible');
    }

    // Montrer l'indice droit si on peut scroller à droite
    const hasHorizontalScroll = container.scrollWidth > container.clientWidth;
    const isAtEnd = container.scrollLeft >= (container.scrollWidth - container.clientWidth - 10);

    if (hasHorizontalScroll && !isAtEnd) {
        rightHint.classList.add('visible');
    } else {
        rightHint.classList.remove('visible');
    }
}
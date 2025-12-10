/**
 * Système de gestion des amis
 * Play to Help - 2025
 */

// Variables globales
let friendsData = {
    all: [],
    online: [],
    requests: [],
    searchResults: []
};

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Mettre le statut en ligne
    updateUserStatus('online');
    
    // Charger les données initiales
    loadFriendsData();
    
    // Actualiser toutes les 30 secondes
    setInterval(loadFriendsData, 30000);
    
    // Actualiser le statut toutes les 2 minutes
    setInterval(() => updateUserStatus('online'), 120000);
});

// Avant de quitter la page, mettre offline
window.addEventListener('beforeunload', function() {
    updateUserStatus('offline');
});

/**
 * Charger toutes les données des amis
 */
async function loadFriendsData() {
    try {
        // Charger tous les amis
        const friendsResponse = await fetch('../../Controller/friendsController.php?action=getFriends');
        const friendsResult = await friendsResponse.json();
        
        if (friendsResult.success) {
            friendsData.all = friendsResult.friends;
            updateAllFriendsList();
        }
        
        // Charger amis en ligne
        const onlineResponse = await fetch('../../Controller/friendsController.php?action=getOnlineFriends');
        const onlineResult = await onlineResponse.json();
        
        if (onlineResult.success) {
            friendsData.online = onlineResult.friends;
            updateOnlineFriendsList();
            document.getElementById('statFriends').textContent = onlineResult.count;
        }
        
        // Charger demandes en attente
        const requestsResponse = await fetch('../../Controller/friendsController.php?action=getPendingRequests');
        const requestsResult = await requestsResponse.json();
        
        if (requestsResult.success) {
            friendsData.requests = requestsResult.requests;
            updateRequestsList();
        }
        
        // Mettre à jour les compteurs
        updateFriendsCounts();
        
    } catch (error) {
        console.error('Erreur chargement amis:', error);
    }
}

/**
 * Mettre à jour les compteurs
 */
function updateFriendsCounts() {
    document.getElementById('allFriendsCount').textContent = friendsData.all.length;
    document.getElementById('onlineFriendsCount').textContent = friendsData.online.length;
    document.getElementById('requestsCount').textContent = friendsData.requests.length;
    document.getElementById('friendsCount').textContent = friendsData.all.length;
}

/**
 * Afficher la liste de tous les amis
 */
function updateAllFriendsList() {
    const container = document.getElementById('allFriendsList');
    
    if (friendsData.all.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fa fa-users"></i>
                <p>Vous n'avez pas encore d'amis.<br>Recherchez des utilisateurs pour commencer!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = friendsData.all.map(friend => createFriendCard(friend, 'all')).join('');
}

/**
 * Afficher la liste des amis en ligne
 */
function updateOnlineFriendsList() {
    const container = document.getElementById('onlineFriendsList');
    
    if (friendsData.online.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fa fa-user-times"></i>
                <p>Aucun ami en ligne pour le moment</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = friendsData.online.map(friend => createFriendCard(friend, 'online')).join('');
}

/**
 * Afficher les demandes d'ami
 */
function updateRequestsList() {
    const container = document.getElementById('requestsList');
    
    if (friendsData.requests.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fa fa-envelope-open"></i>
                <p>Aucune demande d'ami en attente</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = friendsData.requests.map(request => createRequestCard(request)).join('');
}

/**
 * Créer une carte d'ami
 */
function createFriendCard(friend, context) {
    const status = friend.status || 'offline';
    const statusClass = status === 'online' || status === 'away' || status === 'busy' ? status : 'offline';
    const statusText = friend.status_message || getStatusText(status);
    
    return `
        <div class="friend-card">
            <div class="friend-avatar">
                <img src="${friend.profile_image || 'assets/images/profile.jpg'}" alt="${friend.username}">
                <span class="status-indicator ${statusClass}"></span>
            </div>
            <div class="friend-info">
                <div class="friend-name">${friend.first_name} ${friend.last_name}</div>
                <div class="friend-username">@${friend.username}</div>
                ${context === 'online' ? `<div class="friend-status-text">${statusText}</div>` : ''}
            </div>
            <div class="friend-actions">
                <button class="friend-btn friend-btn-remove" onclick="removeFriend(${friend.id}, '${friend.username}')">
                    <i class="fa fa-user-times"></i> Retirer
                </button>
            </div>
        </div>
    `;
}

/**
 * Créer une carte de demande d'ami
 */
function createRequestCard(request) {
    return `
        <div class="friend-card">
            <div class="friend-avatar">
                <img src="${request.profile_image || 'assets/images/profile.jpg'}" alt="${request.username}">
            </div>
            <div class="friend-info">
                <div class="friend-name">${request.first_name} ${request.last_name}</div>
                <div class="friend-username">@${request.username}</div>
                <div class="friend-status-text">Demande envoyée ${formatDate(request.created_at)}</div>
            </div>
            <div class="friend-actions">
                <button class="friend-btn friend-btn-accept" onclick="acceptRequest(${request.id}, '${request.username}')">
                    <i class="fa fa-check"></i> Accepter
                </button>
                <button class="friend-btn friend-btn-reject" onclick="rejectRequest(${request.id})">
                    <i class="fa fa-times"></i> Refuser
                </button>
            </div>
        </div>
    `;
}

/**
 * Créer une carte de résultat de recherche
 */
function createSearchResultCard(user) {
    let actionButton = '';
    
    switch(user.friendship_status) {
        case 'friend':
            actionButton = `<button class="friend-btn friend-btn-pending" disabled><i class="fa fa-check"></i> Déjà ami</button>`;
            break;
        case 'request_sent':
            actionButton = `<button class="friend-btn friend-btn-pending" disabled><i class="fa fa-clock-o"></i> En attente</button>`;
            break;
        case 'request_received':
            actionButton = `<button class="friend-btn friend-btn-accept" onclick="acceptRequest(${user.id}, '${user.username}')"><i class="fa fa-check"></i> Accepter</button>`;
            break;
        default:
            actionButton = `<button class="friend-btn friend-btn-add" onclick="sendRequest(${user.id}, '${user.username}')"><i class="fa fa-user-plus"></i> Ajouter</button>`;
    }
    
    return `
        <div class="friend-card">
            <div class="friend-avatar">
                <img src="${user.profile_image || 'assets/images/profile.jpg'}" alt="${user.username}">
            </div>
            <div class="friend-info">
                <div class="friend-name">${user.first_name} ${user.last_name}</div>
                <div class="friend-username">@${user.username}</div>
                ${user.role === 'streamer' ? '<div class="friend-status-text"><i class="fa fa-twitch"></i> Streamer</div>' : ''}
            </div>
            <div class="friend-actions">
                ${actionButton}
            </div>
        </div>
    `;
}

/**
 * Rechercher des utilisateurs
 */
let searchTimeout;
async function searchFriends(query) {
    clearTimeout(searchTimeout);
    
    const container = document.getElementById('searchResults');
    
    if (query.length < 2) {
        container.innerHTML = '<p class="placeholder-text">Tapez au moins 2 caractères</p>';
        return;
    }
    
    container.innerHTML = '<p class="loading-text"><i class="fa fa-spinner fa-spin"></i> Recherche...</p>';
    
    searchTimeout = setTimeout(async () => {
        try {
            const response = await fetch(`../../Controller/friendsController.php?action=searchUsers&q=${encodeURIComponent(query)}`);
            const result = await response.json();
            
            if (result.success) {
                if (result.users.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fa fa-search"></i>
                            <p>Aucun utilisateur trouvé</p>
                        </div>
                    `;
                } else {
                    container.innerHTML = result.users.map(user => createSearchResultCard(user)).join('');
                }
            } else {
                container.innerHTML = `<p class="placeholder-text">${result.message}</p>`;
            }
        } catch (error) {
            console.error('Erreur recherche:', error);
            container.innerHTML = '<p class="placeholder-text">Erreur lors de la recherche</p>';
        }
    }, 500);
}

/**
 * Envoyer une demande d'ami
 */
async function sendRequest(friendId, username) {
    try {
        const formData = new FormData();
        formData.append('friend_id', friendId);
        
        const response = await fetch('../../Controller/friendsController.php?action=sendRequest', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showFriendsSuccess(`Demande envoyée à @${username}`);
            // Recharger la recherche
            const searchInput = document.getElementById('friendSearchInput');
            if (searchInput.value) {
                searchFriends(searchInput.value);
            }
        } else {
            showFriendsError(result.message);
        }
    } catch (error) {
        console.error('Erreur envoi demande:', error);
        showFriendsError('Erreur lors de l\'envoi de la demande');
    }
}

/**
 * Accepter une demande d'ami
 */
async function acceptRequest(friendId, username) {
    try {
        const formData = new FormData();
        formData.append('friend_id', friendId);
        
        const response = await fetch('../../Controller/friendsController.php?action=acceptRequest', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showFriendsSuccess(`Vous êtes maintenant ami avec @${username}!`);
            loadFriendsData();
        } else {
            showFriendsError(result.message);
        }
    } catch (error) {
        console.error('Erreur acceptation:', error);
        showFriendsError('Erreur lors de l\'acceptation');
    }
}

/**
 * Refuser une demande d'ami
 */
async function rejectRequest(friendId) {
    try {
        const formData = new FormData();
        formData.append('friend_id', friendId);
        
        const response = await fetch('../../Controller/friendsController.php?action=rejectRequest', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showFriendsSuccess('Demande refusée');
            loadFriendsData();
        } else {
            showFriendsError(result.message);
        }
    } catch (error) {
        console.error('Erreur refus:', error);
        showFriendsError('Erreur lors du refus');
    }
}

/**
 * Retirer un ami
 */
async function removeFriend(friendId, username) {
    if (!confirm(`Voulez-vous vraiment retirer @${username} de vos amis?`)) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('friend_id', friendId);
        
        const response = await fetch('../../Controller/friendsController.php?action=removeFriend', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showFriendsSuccess(`@${username} a été retiré de vos amis`);
            loadFriendsData();
        } else {
            showFriendsError(result.message);
        }
    } catch (error) {
        console.error('Erreur suppression:', error);
        showFriendsError('Erreur lors de la suppression');
    }
}

/**
 * Mettre à jour le statut utilisateur
 */
async function updateUserStatus(status, statusMessage = null) {
    try {
        const formData = new FormData();
        formData.append('status', status);
        if (statusMessage) {
            formData.append('status_message', statusMessage);
        }
        
        await fetch('../../Controller/friendsController.php?action=updateStatus', {
            method: 'POST',
            body: formData
        });
    } catch (error) {
        console.error('Erreur mise à jour statut:', error);
    }
}

/**
 * Changer d'onglet dans le modal amis
 */
function switchFriendsTab(tabName) {
    // Masquer tous les onglets
    document.querySelectorAll('.friends-tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.friends-tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Afficher l'onglet sélectionné
    document.getElementById(tabName + 'FriendsTab' === 'allFriendsTab' ? 'allFriendsTab' : tabName + 'Tab').classList.add('active');
    event.target.classList.add('active');
    
    // Recharger les données si nécessaire
    if (tabName === 'online' && friendsData.online.length === 0) {
        loadFriendsData();
    }
}

/**
 * Ouvrir le modal des amis
 */
function openFriendsModal() {
    document.getElementById('friendsModal').style.display = 'flex';
    loadFriendsData();
}

/**
 * Fermer le modal des amis
 */
function closeFriendsModal() {
    document.getElementById('friendsModal').style.display = 'none';
    clearFriendsMessages();
}

/**
 * Afficher un message de succès
 */
function showFriendsSuccess(message) {
    const el = document.getElementById('friendsSuccess');
    el.textContent = message;
    el.style.display = 'block';
    setTimeout(() => {
        el.style.display = 'none';
    }, 3000);
}

/**
 * Afficher un message d'erreur
 */
function showFriendsError(message) {
    const el = document.getElementById('friendsError');
    el.textContent = message;
    el.style.display = 'block';
    setTimeout(() => {
        el.style.display = 'none';
    }, 5000);
}

/**
 * Effacer les messages
 */
function clearFriendsMessages() {
    document.getElementById('friendsSuccess').style.display = 'none';
    document.getElementById('friendsError').style.display = 'none';
}

/**
 * Obtenir le texte de statut
 */
function getStatusText(status) {
    const statusMap = {
        'online': 'En ligne',
        'offline': 'Hors ligne',
        'away': 'Absent',
        'busy': 'Occupé'
    };
    return statusMap[status] || 'Hors ligne';
}

/**
 * Formater une date relative
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'à l\'instant';
    if (diff < 3600) return `il y a ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `il y a ${Math.floor(diff / 3600)}h`;
    if (diff < 604800) return `il y a ${Math.floor(diff / 86400)}j`;
    return date.toLocaleDateString('fr-FR');
}

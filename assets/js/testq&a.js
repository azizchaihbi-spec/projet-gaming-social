const API_URL = '/play-to-help/api.php';
const MY_USER_ID = 1; // ← plus tard : $_SESSION['id']

function loadPosts() {
    const filter = document.getElementById('filterCommunity').value;
    fetch(`${API_URL}?action=get_all&filter=${filter}`)
        .then(r => r.json())
        .then(posts => {
            const container = document.getElementById('postsList');
            container.innerHTML = '';

            posts.forEach(p => {
                const isMyPost = p.id_auteur == MY_USER_ID;

                let answersHTML = '';
                p.answers.forEach(a => {
                    const isMyReply = a.id_auteur == MY_USER_ID;

                    answersHTML += `
                        <div class="answer" style="position:relative; margin:15px 0; padding:15px; background:rgba(20,20,40,0.9); border-radius:12px; border-left:5px solid ${isMyReply ? '#6e6eff' : '#ff69b4'};">
                            <strong style="color:${isMyReply ? '#6e6eff' : '#ff69b4'};">${a.prenom || 'Anonyme'} :</strong><br>
                            <div id="replyContent-${a.id_reponse}" style="margin:10px 0; color:#ddd;">${a.contenu.replace(/\n/g, '<br>')}</div>

                            ${isMyReply ? `
                            <div class="reply-actions">
                                <button onclick="showEditReplyForm(${a.id_reponse})" style="background:#6e6eff; color:white; border:none; padding:8px 16px; border-radius:50px; font-size:0.9em; margin-right:8px;">Modifier</button>
                                <button onclick="deleteReply(${a.id_reponse})" style="background:#e91e63; color:white; border:none; padding:8px 16px; border-radius:50px; font-size:0.9em;">Supprimer</button>
                            </div>

                            <!-- FORMULAIRE D'ÉDITION (caché) -->
                            <div id="editReplyForm-${a.id_reponse}" style="display:none; margin-top:15px; padding:15px; background:rgba(10,10,30,0.95); border:2px solid #6e6eff; border-radius:12px;">
                                <textarea id="editReplyText-${a.id_reponse}" style="width:100%; height:100px; background:#111; color:white; border:1px solid #ff69b4; border-radius:10px; padding:12px;">${a.contenu}</textarea>
                                <div style="text-align:right; margin-top:10px;">
                                    <button onclick="saveReply(${a.id_reponse})" style="background:#6e6eff; color:white; padding:10px 24px; border:none; border-radius:50px; margin-right:8px;">Sauvegarder</button>
                                    <button onclick="cancelEditReply(${a.id_reponse})" style="background:#444; color:white; padding:10px 20px; border:none; border-radius:50px;">Annuler</button>
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `;
                });

                const card = document.createElement('div');
                card.className = 'question-card';
                card.innerHTML = `
                    <div class="question-title">${p.titre} <span class="community-badge">${p.forum_nom}</span></div>
                    <div class="question-author">par ${p.prenom || 'Anonyme'} • ${new Date(p.date_publication).toLocaleString('fr-FR')}</div>
                    <div class="question-content">${p.contenu.replace(/\n/g,'<br>')}</div>

                    <div class="actions">
                        <button class="like-btn" onclick="vote(${p.id_publication}, 'like')">Like ${p.likes || 0}</button>
                        <button class="dislike-btn" onclick="vote(${p.id_publication}, 'dislike')">Dislike ${p.dislikes || 0}</button>
                        <button class="reply-btn" onclick="toggleReply(${p.id_publication})">Répondre</button>
                        ${isMyPost ? `<button class="edit-btn" onclick="editPost(${p.id_publication})">Modifier Question</button>
                                       <button class="delete-btn" onclick="deletePost(${p.id_publication})">Supprimer Question</button>` : ''}
                    </div>

                    <div class="reply-form" id="replyForm-${p.id_publication}" style="display:none; margin-top:15px;">
                        <textarea rows="3" placeholder="Ta réponse..."></textarea>
                        <button onclick="addReply(${p.id_publication})">Envoyer</button>
                    </div>

                    <div class="answers">
                        ${answersHTML || '<p style="color:#666; font-style:italic;">Aucune réponse pour l\'instant.</p>'}
                    </div>
                `;
                container.appendChild(card);
            });
        });
}

// === FONCTIONS POUR LES RÉPONSES ===
function showEditReplyForm(id) {
    document.getElementById(`editReplyForm-${id}`).style.display = 'block';
}

function cancelEditReply(id) {
    document.getElementById(`editReplyForm-${id}`).style.display = 'none';
}

function saveReply(id) {
    const nouveau = document.getElementById(`editReplyText-${id}`).value.trim();
    if (!nouveau) return alert("La réponse ne peut pas être vide !");

    fetch(API_URL + '?action=edit_reply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, contenu: nouveau })
    }).then(() => {
        cancelEditReply(id);
        loadPosts();
    });
}

function deleteReply(id) {
    if (confirm("Supprimer cette réponse ?")) {
        fetch(API_URL + '?action=delete_reply', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        }).then(() => loadPosts());
    }
}

// === LE RESTE (toggle, addReply, vote, editPost, etc.) reste IDENTIQUE ===
function toggleReply(id) {
    const form = document.getElementById(`replyForm-${id}`);
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
}

function addReply(id) {
    const textarea = document.querySelector(`#replyForm-${id} textarea`);
    const contenu = textarea.value.trim();
    if (!contenu) return alert("Écris une réponse !");
    fetch(API_URL + '?action=add_reply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_publication: id, contenu })
    }).then(() => { textarea.value = ''; toggleReply(id); loadPosts(); });
}

function vote(id, type) {
    fetch(API_URL + `?action=${type}`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) })
        .then(() => loadPosts());
}

function editPost(id) { /* ton code existant */ }
function deletePost(id) { /* ton code existant */ }

loadPosts();
document.getElementById('filterCommunity').onchange = loadPosts;
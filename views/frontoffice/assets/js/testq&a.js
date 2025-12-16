// Utiliser le chemin relatif correct
const API_URL = '../../api.php';
const MY_USER_ID = window.CURRENT_USER_ID || 1; // Récupéré depuis PHP

// Debug pour vérifier l'ID utilisateur
console.log('MY_USER_ID:', MY_USER_ID);
console.log('CURRENT_USER_ID from window:', window.CURRENT_USER_ID);
console.log('CURRENT_USER_NAME from window:', window.CURRENT_USER_NAME);

function loadPosts() {
    const filter = document.getElementById('filterCommunity').value;
    
    console.log('🔍 Tentative de chargement des posts...');
    console.log('📡 URL API:', `${API_URL}?action=get_all&filter=${filter}`);
    
    fetch(`${API_URL}?action=get_all&filter=${filter}`)
        .then(response => {
            console.log('📊 Réponse reçue:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(posts => {
            console.log('✅ Posts récupérés:', posts.length, 'publications');
            console.log('📝 Premier post:', posts[0]);
            
            const container = document.getElementById('postsList');
            
            // Debug: vérifier que le container existe
            if (!container) {
                console.error('❌ Container postsList introuvable !');
                alert('❌ Erreur: Container postsList introuvable dans la page !');
                return;
            }
            
            console.log('📦 Container trouvé:', container);
            container.innerHTML = '';
            
            // Debug: afficher un message si aucun post
            if (!posts || posts.length === 0) {
                container.innerHTML = '<div style="background: orange; color: white; padding: 20px; border-radius: 10px;">⚠️ Aucune publication reçue de l\'API</div>';
                return;
            }

            posts.forEach(p => {
                const isMyPost = p.id_auteur == MY_USER_ID;
                
                // Debug pour vérifier les IDs (seulement pour les 3 premiers posts)
                if (posts.indexOf(p) < 3) {
                    console.log(`Post ${p.id_publication}: auteur=${p.id_auteur}, current_user=${MY_USER_ID}, isMyPost=${isMyPost}`);
                }

                let answersHTML = '';
                p.answers.forEach(a => {
                    const isMyReply = a.id_auteur == MY_USER_ID;

                    answersHTML += `
                        <div class="answer" style="position:relative; margin:15px 0; padding:15px; background:rgba(20,20,40,0.9); border-radius:12px; border-left:5px solid ${isMyReply ? '#6e6eff' : '#ff69b4'};">
                            <strong style="color:${isMyReply ? '#6e6eff' : '#ff69b4'};">${a.prenom || 'Anonyme'} :</strong>
                            <span class="sentiment-badge" data-text="${a.contenu.replace(/"/g, '&quot;')}" style="display:inline-block; margin-left:10px; padding:4px 12px; border-radius:15px; font-size:0.85em; font-weight:bold;">
                                <span class="sentiment-loading">🔄 Analyse...</span>
                            </span>
                            <br>
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
                
                // Préparer le contenu avec les médias
                let contentHtml = p.contenu.replace(/\n/g,'<br>');
                
                // Ajouter les emojis si présents
                let emojisHtml = '';
                if (p.emojis) {
                    try {
                        const emojis = JSON.parse(p.emojis);
                        if (emojis.length > 0) {
                            emojisHtml = `<div class="emojis-display" style="margin: 10px 0; font-size: 1.5em;">${emojis.join(' ')}</div>`;
                        }
                    } catch (e) {
                        console.log('Erreur parsing emojis:', e);
                    }
                }
                
                // Ajouter le GIF si présent
                let gifHtml = '';
                if (p.gif_url) {
                    gifHtml = `<div class="gif-display" style="margin: 15px 0;"><img src="${p.gif_url}" alt="GIF" style="max-width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);"></div>`;
                }
                
                // Ajouter le sticker si présent
                let stickerHtml = '';
                if (p.sticker_url) {
                    stickerHtml = `<div class="sticker-display" style="margin: 15px 0;"><img src="${p.sticker_url}" alt="Sticker" style="max-width: 200px; border-radius: 8px;"></div>`;
                }
                
                // Ajouter l'image uploadée si présente
                let imageHtml = '';
                if (p.image) {
                    // Corriger le chemin de l'image depuis views/frontoffice/
                    const imagePath = p.image.startsWith('views/') ? '../../' + p.image : p.image;
                    imageHtml = `<div class="image-display" style="margin: 15px 0;"><img src="${imagePath}" alt="Image" style="max-width: 100%; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);" onerror="this.style.display='none'; console.log('Image non trouvée: ${imagePath}');"></div>`;
                }
                
                card.innerHTML = `
                    <div class="question-title">
                        ${p.titre} 
                        <span class="community-badge">${p.forum_nom}</span>
                    </div>
                    <div class="question-author">par ${p.prenom || 'Anonyme'} • ${new Date(p.date_publication).toLocaleString('fr-FR')}</div>
                    <div class="question-content">${contentHtml}</div>
                    ${emojisHtml}
                    ${imageHtml}
                    ${gifHtml}
                    ${stickerHtml}

                    <div class="actions">
                        <span class="sentiment-badge" data-text="${p.contenu.replace(/"/g, '&quot;')}" style="display:inline-block; padding:6px 16px; border-radius:50px; font-size:0.9em; font-weight:bold;">
                            <span class="sentiment-loading">🔄 Analyse...</span>
                        </span>
                        <button class="like-btn" onclick="vote(${p.id_publication}, 'like')">👍 ${p.likes || 0}</button>
                        <button class="dislike-btn" onclick="vote(${p.id_publication}, 'dislike')">👎 ${p.dislikes || 0}</button>
                        <button class="reply-btn" onclick="toggleReply(${p.id_publication})">💬 Répondre</button>
                        ${isMyPost ? `<button class="edit-btn" onclick="editPost(${p.id_publication})">✏️ Modifier</button>
                                       <button class="delete-btn" onclick="deletePost(${p.id_publication})">🗑️ Supprimer</button>` : ''}
                    </div>

                    <div class="reply-form" id="replyForm-${p.id_publication}" style="display:none; margin-top:15px;">
                        <textarea rows="3" placeholder="Ta réponse..." id="replyTextarea-${p.id_publication}"></textarea>
                        <div style="margin: 10px 0;">
                            <button type="button" onclick="generateReplyAI(${p.id_publication}, '${p.titre.replace(/'/g, "\\'")}', '${p.contenu.replace(/'/g, "\\'").replace(/\n/g, ' ')}')" 
                                    style="background: linear-gradient(135deg, #ff6b6b, #feca57); color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; margin-right: 10px;">
                                🤖 Générer avec AI
                            </button>
                            <button onclick="addReply(${p.id_publication})">Envoyer</button>
                        </div>
                    </div>

                    <div class="answers">
                        ${answersHTML || '<p style="color:#666; font-style:italic;">Aucune réponse pour l\'instant.</p>'}
                    </div>
                `;
                container.appendChild(card);
            });
            
            // Analyser le sentiment de tous les messages après le chargement
            analyzeSentiments();
        })
        .catch(error => {
            console.error('❌ Erreur lors du chargement des posts:', error);
            const container = document.getElementById('postsList');
            container.innerHTML = `
                <div style="background: #ff4444; color: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
                    <h3>❌ Erreur de chargement</h3>
                    <p><strong>Erreur:</strong> ${error.message}</p>
                    <p><strong>URL tentée:</strong> ${API_URL}?action=get_all&filter=${document.getElementById('filterCommunity').value}</p>
                    <p>Vérifiez la console du navigateur (F12) pour plus de détails.</p>
                    <button onclick="loadPosts()" style="background: white; color: #ff4444; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                        🔄 Réessayer
                    </button>
                </div>
            `;
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
    const textarea = document.getElementById(`replyTextarea-${id}`);
    const contenu = textarea.value.trim();
    if (!contenu) return alert("Écris une réponse !");
    fetch(API_URL + '?action=add_reply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id_publication: id, contenu })
    }).then(() => { textarea.value = ''; toggleReply(id); loadPosts(); });
}

// === GÉNÉRER UNE RÉPONSE AVEC L'IA ===
async function generateReplyAI(postId, questionTitle, questionContent) {
    const textarea = document.getElementById(`replyTextarea-${postId}`);
    const btn = event.target;
    
    btn.disabled = true;
    btn.innerHTML = '⏳ Génération...';
    
    try {
        const HF_API_KEY = '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';
        
        const prompt = `Question: ${questionTitle}
${questionContent}

Génère une réponse utile et amicale à cette question en 2-3 phrases.`;

        const response = await fetch('https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${HF_API_KEY}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                inputs: prompt,
                parameters: {
                    max_new_tokens: 150,
                    temperature: 0.7,
                    top_p: 0.9,
                    return_full_text: false
                }
            })
        });

        const data = await response.json();
        
        if (data[0] && data[0].generated_text) {
            textarea.value = data[0].generated_text.trim();
            alert('✅ Réponse générée par l\'IA ! Vous pouvez la modifier avant d\'envoyer.');
        } else {
            throw new Error('Pas de réponse de l\'IA');
        }
    } catch (error) {
        console.error('Erreur IA:', error);
        // Fallback avec des réponses prédéfinies
        const replies = [
            'Super question! D\'après mon expérience, je te conseille de commencer par les bases et de pratiquer régulièrement. N\'hésite pas si tu as d\'autres questions! 😊',
            'Salut! Je pense que la meilleure approche est de tester différentes stratégies et de voir ce qui fonctionne pour toi. Bon courage! 💪',
            'Hey! J\'ai eu le même problème au début. Ce qui m\'a aidé c\'est de regarder des tutoriels et de m\'entraîner. Tu vas y arriver! 🎮',
            'Bonne question! Je te recommande de rejoindre une communauté active où tu pourras échanger des astuces. Ça aide beaucoup! 🤝'
        ];
        const randomReply = replies[Math.floor(Math.random() * replies.length)];
        textarea.value = randomReply;
        alert('✅ Réponse générée ! (Mode hors ligne)');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '🤖 Générer avec AI';
    }
}

function vote(id, type) {
    fetch(API_URL + `?action=${type}`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) })
        .then(() => loadPosts());
}

function editPost(id) {
    // Récupérer les données actuelles du post
    const postCard = document.querySelector(`[onclick*="editPost(${id})"]`).closest('.question-card');
    const titleElement = postCard.querySelector('.question-title');
    const contentElement = postCard.querySelector('.question-content');
    
    // Extraire le titre (sans le badge de communauté)
    const currentTitle = titleElement.textContent.split('\n')[0].trim();
    const currentContent = contentElement.textContent.trim();
    
    // Créer le formulaire d'édition
    const editForm = document.createElement('div');
    editForm.id = `editForm-${id}`;
    editForm.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(10,10,30,0.98); border: 2px solid #6e6eff; border-radius: 15px; padding: 30px; z-index: 1000; width: 90%; max-width: 600px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);';
    
    editForm.innerHTML = `
        <h3 style="color: #6e6eff; margin-bottom: 20px;">✏️ Modifier la publication</h3>
        <input type="text" id="editTitle-${id}" value="${currentTitle}" 
               style="width: 100%; padding: 12px; margin-bottom: 15px; background: #111; color: white; border: 1px solid #6e6eff; border-radius: 8px;" 
               placeholder="Titre de la publication">
        <textarea id="editContent-${id}" rows="6" 
                  style="width: 100%; padding: 12px; margin-bottom: 20px; background: #111; color: white; border: 1px solid #6e6eff; border-radius: 8px;" 
                  placeholder="Contenu de la publication">${currentContent}</textarea>
        <div style="text-align: right;">
            <button onclick="saveEditPost(${id})" 
                    style="background: #6e6eff; color: white; padding: 12px 24px; border: none; border-radius: 25px; margin-right: 10px; cursor: pointer; font-weight: bold;">
                💾 Sauvegarder
            </button>
            <button onclick="cancelEditPost(${id})" 
                    style="background: #666; color: white; padding: 12px 20px; border: none; border-radius: 25px; cursor: pointer;">
                ❌ Annuler
            </button>
        </div>
    `;
    
    // Ajouter un overlay sombre
    const overlay = document.createElement('div');
    overlay.id = `overlay-${id}`;
    overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 999;';
    overlay.onclick = () => cancelEditPost(id);
    
    document.body.appendChild(overlay);
    document.body.appendChild(editForm);
}

function saveEditPost(id) {
    const titre = document.getElementById(`editTitle-${id}`).value.trim();
    const contenu = document.getElementById(`editContent-${id}`).value.trim();
    
    if (!titre || !contenu) {
        alert('❌ Le titre et le contenu sont obligatoires !');
        return;
    }
    
    fetch(API_URL + '?action=edit_post', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, titre: titre, contenu: contenu })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('✅ Publication modifiée avec succès !');
            cancelEditPost(id);
            loadPosts(); // Recharger les posts
        } else {
            alert('❌ Erreur lors de la modification: ' + (result.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('❌ Erreur réseau lors de la modification');
    });
}

function cancelEditPost(id) {
    const overlay = document.getElementById(`overlay-${id}`);
    const editForm = document.getElementById(`editForm-${id}`);
    
    if (overlay) overlay.remove();
    if (editForm) editForm.remove();
}

function deletePost(id) {
    if (confirm('🗑️ Êtes-vous sûr de vouloir supprimer cette publication ?\n\nCette action est irréversible et supprimera également toutes les réponses associées.')) {
        fetch(API_URL + '?action=delete_post', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('✅ Publication supprimée avec succès !');
                loadPosts(); // Recharger les posts
            } else {
                alert('❌ Erreur lors de la suppression: ' + (result.error || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('❌ Erreur réseau lors de la suppression');
        });
    }
}

// === ANALYSE DE SENTIMENT ===
async function analyzeSentiments() {
    const badges = document.querySelectorAll('.sentiment-badge');
    
    badges.forEach(async (badge) => {
        const text = badge.dataset.text;
        if (!text) return;
        
        // Utiliser seulement l'analyse locale pour éviter les erreurs CORS
        const sentiment = analyzeSentimentLocal(text);
        updateSentimentBadge(badge, sentiment);
    });
}

// === ANALYSE DE SENTIMENT VIA API ===
async function analyzeSentimentAPI(text) {
    const HF_API_KEY = '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';
    
    const response = await fetch('https://api-inference.huggingface.co/models/cardiffnlp/twitter-xlm-roberta-base-sentiment', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${HF_API_KEY}`,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            inputs: text.substring(0, 500) // Limiter à 500 caractères
        })
    });
    
    const data = await response.json();
    
    if (data && data[0]) {
        // Trouver le sentiment avec le score le plus élevé
        const result = data[0].reduce((max, item) => item.score > max.score ? item : max);
        return {
            label: result.label.toLowerCase(),
            score: result.score
        };
    }
    
    throw new Error('Pas de résultat');
}

// === ANALYSE DE SENTIMENT LOCALE (FALLBACK) ===
function analyzeSentimentLocal(text) {
    const textLower = text.toLowerCase();
    
    // Mots positifs
    const positiveWords = ['merci', 'super', 'génial', 'excellent', 'parfait', 'top', 'cool', 'bien', 'bon', 'bravo', 'love', 'adore', 'content', 'heureux', 'satisfait', '😊', '😀', '😃', '👍', '❤️', '💚', '🎉', '✅'];
    
    // Mots négatifs
    const negativeWords = ['nul', 'mauvais', 'horrible', 'problème', 'bug', 'erreur', 'pas', 'jamais', 'rien', 'déçu', 'triste', 'frustré', 'mécontent', '😢', '😭', '😡', '😠', '👎', '❌'];
    
    let positiveCount = 0;
    let negativeCount = 0;
    
    positiveWords.forEach(word => {
        if (textLower.includes(word)) positiveCount++;
    });
    
    negativeWords.forEach(word => {
        if (textLower.includes(word)) negativeCount++;
    });
    
    if (positiveCount > negativeCount) {
        return { label: 'positive', score: 0.8 };
    } else if (negativeCount > positiveCount) {
        return { label: 'negative', score: 0.8 };
    } else {
        return { label: 'neutral', score: 0.7 };
    }
}

// === METTRE À JOUR LE BADGE DE SENTIMENT ===
function updateSentimentBadge(badge, sentiment) {
    let emoji, text, color, bgColor;
    
    if (sentiment.label.includes('positive') || sentiment.label === 'positive') {
        emoji = '😊';
        text = 'Positif';
        color = '#10b981';
        bgColor = 'rgba(16, 185, 129, 0.2)';
    } else if (sentiment.label.includes('negative') || sentiment.label === 'negative') {
        emoji = '😔';
        text = 'Négatif';
        color = '#ef4444';
        bgColor = 'rgba(239, 68, 68, 0.2)';
    } else {
        emoji = '😐';
        text = 'Neutre';
        color = '#6b7280';
        bgColor = 'rgba(107, 114, 128, 0.2)';
    }
    
    badge.style.backgroundColor = bgColor;
    badge.style.color = color;
    badge.style.border = `1px solid ${color}`;
    badge.innerHTML = `${emoji} ${text}`;
    
    // Ajouter un tooltip avec le score
    badge.title = `Confiance: ${(sentiment.score * 100).toFixed(0)}%`;
}

loadPosts();
document.getElementById('filterCommunity').onchange = loadPosts;
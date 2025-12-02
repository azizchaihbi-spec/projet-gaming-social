// testq&a.js – Version MVC complète
const API = '/play-to-help/api.php';
const MY_ID = 1;

// =========================================
// FONCTIONS DE VALIDATION
// =========================================

const BAD_WORDS = ["con","merde","putain","fdp","enculé","nique","salope","batard","connard","tg","fuck","shit","bitch","asshole","cunt","dick","nigga","faggot"];

let lastPostTime = 0;

function validateRequiredFields(author, title, content, isReply = false) {
  if (!author || author.trim() === '') {
    alert(isReply ? "❌ Le pseudo pour la réponse ne peut pas être vide !" : "❌ Le pseudo ne peut pas être vide !");
    return false;
  }
  if (!isReply) {
    if (!title || title.trim() === '') {
      alert("❌ Le titre de la question ne peut pas être vide !");
      return false;
    }
  }
  if (!content || content.trim() === '') {
    alert(isReply ? "❌ La réponse ne peut pas être vide !" : "❌ La description de la question ne peut pas être vide !");
    return false;
  }
  return true;
}

function validateLength(author, title, content, isReply = false) {
  const trimmedAuthor = author.trim();
  const trimmedTitle = title ? title.trim() : '';
  const trimmedContent = content.trim();
  
  if (trimmedAuthor.length < 2) {
    alert("❌ Le pseudo doit contenir au moins 2 caractères !");
    return false;
  }
  if (trimmedAuthor.length > 30) {
    alert("❌ Le pseudo ne peut pas dépasser 30 caractères !");
    return false;
  }
  
  if (!isReply) {
    if (trimmedTitle.length < 5) {
      alert("❌ Le titre doit contenir au moins 5 caractères !");
      return false;
    }
    if (trimmedTitle.length > 100) {
      alert("❌ Le titre ne peut pas dépasser 100 caractères !");
      return false;
    }
  }
  
  const contentMin = isReply ? 2 : 10;
  const contentMax = isReply ? 500 : 1000;
  const contentLabel = isReply ? "La réponse" : "La question";
  
  if (trimmedContent.length < contentMin) {
    alert(`❌ ${contentLabel} doit contenir au moins ${contentMin} caractères !`);
    return false;
  }
  if (trimmedContent.length > contentMax) {
    alert(`❌ ${contentLabel} ne peut pas dépasser ${contentMax} caractères !`);
    return false;
  }
  
  return true;
}

function validateAuthorFormat(author) {
  const trimmedAuthor = author.trim();
  const authorRegex = /^[a-zA-Z0-9àâäéèêëïîôöùûüÿçñÀÂÄÉÈÊËÏÎÔÖÙÛÜŸÇÑ\s\-_\.]{2,30}$/;
  if (!authorRegex.test(trimmedAuthor)) {
    alert("❌ Le pseudo contient des caractères non autorisés !\n\nAutorisés : lettres, chiffres, espaces, - _ .");
    return false;
  }
  return true;
}

function hasBadWord(text) {
  const lower = text.toLowerCase();
  return BAD_WORDS.some(word => lower.includes(word));
}

function canPost(isReply = false) {
  const now = Date.now();
  const timeSinceLastPost = now - lastPostTime;
  const delay = isReply ? 10000 : 30000;
  
  if (timeSinceLastPost < delay) {
    const remaining = Math.ceil((delay - timeSinceLastPost) / 1000);
    alert(`⏰ Attendez ${remaining} secondes avant de ${isReply ? 'répondre' : 'poster'} à nouveau !`);
    return false;
  }
  
  lastPostTime = now;
  return true;
}

function validateImage(file) {
  if (!file) return true;
  
  if (file.size > 5 * 1024 * 1024) {
    alert("❌ L'image est trop lourde ! Taille maximum : 5MB");
    return false;
  }
  
  if (!file.type.startsWith('image/')) {
    alert("❌ Veuillez sélectionner un fichier image valide !");
    return false;
  }
  
  return true;
}

// =========================================
// FONCTIONS PRINCIPALES
// =========================================

function loadPosts() {
  const filter = document.getElementById('filterCommunity').value;
  
  fetch(API + '?action=get_all&filter=' + encodeURIComponent(filter))
    .then(r => {
      if (!r.ok) throw new Error('Erreur réseau: ' + r.status);
      return r.json();
    })
    .then(posts => {
      const container = document.getElementById('postsList');
      container.innerHTML = '';

      if (!posts || posts.length === 0) {
        container.innerHTML = '<p style="text-align:center; color:#888; padding:50px;">Aucune question pour le moment dans cette communauté.</p>';
        return;
      }

      posts.forEach(post => {
        const postElement = document.createElement('div');
        postElement.className = 'question-card';
        postElement.innerHTML = `
          <div class="question-title">
            ${post.titre}
            <span class="community-badge">${post.forum_nom}</span>
          </div>
          <div class="question-author">
            par ${post.prenom} • ${new Date(post.date_publication).toLocaleString('fr-FR')}
          </div>
          <div class="question-content">${post.contenu.replace(/\n/g, '<br>')}</div>
          
          ${post.image ? `<img src="${post.image}" class="question-image" alt="image jointe">` : ''}

          <div class="actions">
            <button class="like-btn" onclick="likePost(${post.id_publication})">👍 Like (${post.likes || 0})</button>
            <button class="dislike-btn" onclick="dislikePost(${post.id_publication})">👎 Dislike (${post.dislikes || 0})</button>
            <button class="reply-btn" onclick="toggleReply(${post.id_publication})">💬 Répondre</button>
            ${post.id_auteur == MY_ID ? `
  <button style="background: none; border: 1px solid #6e6eff; color: #6e6eff; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: bold; transition: all 0.3s ease; margin-left: 8px;" 
          onmouseover="this.style.background='rgba(110, 110, 255, 0.3)'; this.style.color='white'; this.style.boxShadow='0 0 15px rgba(110, 110, 255, 0.5)'; this.style.transform='translateY(-2px)'" 
          onmouseout="this.style.background='rgba(110, 110, 255, 0.1)'; this.style.color='#6e6eff'; this.style.boxShadow='none'; this.style.transform='translateY(0)'" 
          onclick="showEditForm(${post.id_publication})">
    ✏️ Modifier
  </button>
  <button style="background: none; border: 1px solid #e91e63; color: #e91e63; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-size: 0.9rem; font-weight: bold; transition: all 0.3s ease; margin-left: 8px;" 
          onmouseover="this.style.background='rgba(233, 30, 99, 0.3)'; this.style.color='white'; this.style.boxShadow='0 0 15px rgba(233, 30, 99, 0.5)'; this.style.transform='translateY(-2px)'" 
          onmouseout="this.style.background='rgba(233, 30, 99, 0.1)'; this.style.color='#e91e63'; this.style.boxShadow='none'; this.style.transform='translateY(0)'" 
          onclick="deletePost(${post.id_publication})">
    🗑️ Supprimer
  </button>
` : ''}
          </div>

          <div class="reply-form" id="editForm-${post.id_publication}" style="display:none;">
            <h4>✏️ Modifier la question</h4>
            <input type="text" id="editTitle-${post.id_publication}" value="${post.titre.replace(/"/g, '&quot;')}" 
                   placeholder="Titre de la question" required>
            <textarea id="editContent-${post.id_publication}" placeholder="Contenu de la question" required>${post.contenu}</textarea>
            <div>
              <button type="button" onclick="saveEdit(${post.id_publication})">💾 Sauvegarder</button>
              <button type="button" onclick="hideEditForm(${post.id_publication})">❌ Annuler</button>
            </div>
          </div>

          <div class="reply-form" id="replyForm-${post.id_publication}">
            <form onsubmit="event.preventDefault(); addReply(${post.id_publication})">
              <input type="text" id="replyAuthor-${post.id_publication}" placeholder="Ton pseudo" required>
              <textarea id="replyContent-${post.id_publication}" rows="3" placeholder="Ta réponse..." required></textarea>
              <button type="submit">📤 Envoyer</button>
              <button type="button" onclick="toggleReply(${post.id_publication})">❌ Annuler</button>
            </form>
          </div>

          <div class="answers">
            ${post.answers && post.answers.length > 0 ? 
              post.answers.map(answer => `
                <div class="answer">
                  <div class="answer-author">${answer.prenom}</div>
                  <div class="answer-date">${new Date(answer.date_reponse).toLocaleString('fr-FR')}</div>
                  <div class="answer-content">${answer.contenu.replace(/\n/g, '<br>')}</div>
                </div>
              `).join('') : 
              '<p style="color:#888; text-align:center;">Aucune réponse pour le moment</p>'
            }
          </div>
        `;
        container.appendChild(postElement);
      });
    })
    .catch(error => {
      console.error('❌ Erreur chargement posts:', error);
      document.getElementById('postsList').innerHTML = '<p style="text-align:center; color:red;">Erreur de chargement des questions</p>';
    });
}

// =========================================
// FONCTIONS INTERACTIVES
// =========================================

function likePost(id) {
  fetch(API + '?action=like', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id: id})
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      loadPosts();
    }
  });
}

function dislikePost(id) {
  fetch(API + '?action=dislike', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id: id})
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      loadPosts();
    }
  });
}

function toggleReply(id) {
  const form = document.getElementById('replyForm-' + id);
  if (form) {
    form.style.display = form.style.display === 'block' ? 'none' : 'block';
  }
}

function addReply(id) {
  const author = document.getElementById('replyAuthor-' + id).value;
  const content = document.getElementById('replyContent-' + id).value;

  if (!validateRequiredFields(author, null, content, true)) return;
  if (!validateLength(author, null, content, true)) return;
  if (!validateAuthorFormat(author)) return;
  if (hasBadWord(author + " " + content)) {
    alert("🚫 Langage inapproprié détecté !");
    return;
  }
  if (!canPost(true)) return;

  fetch(API + '?action=add_reply', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      id_publication: id, 
      contenu: content
    })
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      loadPosts();
      toggleReply(id);
      alert('✅ Réponse publiée avec succès !');
    } else {
      alert('❌ Erreur lors de la publication de la réponse');
    }
  });
}

function showEditForm(id) {
  const form = document.getElementById('editForm-' + id);
  if (form) {
    form.style.display = 'block';
  }
}

function hideEditForm(id) {
  const form = document.getElementById('editForm-' + id);
  if (form) {
    form.style.display = 'none';
  }
}

function saveEdit(id) {
  const titre = document.getElementById('editTitle-' + id).value.trim();
  const contenu = document.getElementById('editContent-' + id).value.trim();
  
  if (!titre || !contenu) {
    alert("❌ Le titre et le contenu ne peuvent pas être vides !");
    return;
  }

  fetch(API + '?action=edit_post', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({id: id, titre: titre, contenu: contenu})
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      hideEditForm(id);
      loadPosts();
      alert('✅ Question modifiée avec succès !');
    } else {
      alert('❌ Erreur lors de la modification');
    }
  });
}

function deletePost(id) {
  if (confirm("❌ Supprimer cette question ? Cette action est irréversible.")) {
    fetch(API + '?action=delete_post', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        loadPosts();
        alert('✅ Question supprimée avec succès !');
      } else {
        alert('❌ Erreur lors de la suppression');
      }
    });
  }
}

// =========================================
// ÉVÉNEMENTS AU CHARGEMENT
// =========================================

document.addEventListener('DOMContentLoaded', function() {
  // Filtre communauté
  const filterSelect = document.getElementById('filterCommunity');
  if (filterSelect) {
    filterSelect.addEventListener('change', loadPosts);
  }
  
  // Nouvelle question
  const newPostForm = document.getElementById('newPost');
  if (newPostForm) {
    newPostForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const author = document.getElementById('author').value;
      const title = document.getElementById('title').value;
      const content = document.getElementById('content').value;
      const forumId = document.getElementById('community').value;
      const imageFile = document.getElementById('imageInput').files[0];

      if (!validateRequiredFields(author, title, content, false)) return;
      if (!validateLength(author, title, content, false)) return;
      if (!validateAuthorFormat(author)) return;
      if (hasBadWord(author + title + content)) {
        alert("🚫 Langage inapproprié détecté !");
        return;
      }
      if (!canPost(false)) return;
      if (!validateImage(imageFile)) return;

      const formData = new FormData();
      formData.append('title', title.trim());
      formData.append('content', content.trim());
      formData.append('forum', forumId);
      
      if (imageFile) {
        formData.append('image', imageFile);
      }

      fetch('index.php?action=create_publication', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) throw new Error('Erreur HTTP: ' + response.status);
        return response.json();
      })
      .then(result => {
        if (result.success) {
          loadPosts();
          this.reset();
          const preview = document.getElementById('preview');
          if (preview) preview.innerHTML = '';
          alert('✅ Question publiée avec succès !');
        } else {
          alert('❌ Erreur lors de la publication: ' + (result.error || ''));
        }
      })
      .catch(error => {
        console.error('Erreur publication:', error);
        alert('❌ Erreur réseau lors de la publication');
      });
    });
  }

  // Prévisualisation image
  const imageInput = document.getElementById('imageInput');
  if (imageInput) {
    imageInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      const preview = document.getElementById('preview');
      
      if (file) {
        if (!validateImage(file)) {
          e.target.value = "";
          if (preview) preview.innerHTML = "";
          return;
        }
        
        const reader = new FileReader();
        reader.onload = function() {
          if (preview) {
            preview.innerHTML = `<img src="${reader.result}" style="max-height:250px; border-radius:10px; margin-top:10px;">`;
          }
        };
        reader.readAsDataURL(file);
      } else {
        if (preview) preview.innerHTML = "";
      }
    });
  }

  // Chargement initial
  loadPosts();
  console.log("✅ Application Q&A initialisée avec MVC");
});
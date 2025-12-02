function render() {
  const search = document.getElementById("searchInput").value;
  fetch(`/play-to-help/api_admin.php?action=get_all&search=${encodeURIComponent(search)}`)
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById("adminPostsList");
      list.innerHTML = "";

      document.getElementById("totalPosts").textContent = data.stats.totalPosts;
      document.getElementById("totalAnswers").textContent = data.stats.totalAnswers;
      document.getElementById("bannedCount").textContent = data.stats.bannedCount;

      if (data.posts.length === 0) {
        list.innerHTML = `<p style="text-align:center;color:#ff69b4;font-size:2rem;padding:100px;">
          ${search ? "Aucun résultat" : "Aucune publication"}
        </p>`;
        return;
      }

      data.posts.forEach(post => {
        const isBanned = false;
        const card = document.createElement("div");
        card.className = "admin-card";
        if (isBanned) card.innerHTML += '<div class="banned-overlay">BANNI</div>';

        card.innerHTML += `
          <div class="admin-actions">
            <button class="btn-admin btn-delete" onclick="deletePost(${post.id_publication})">Supprimer</button>
            <button class="btn-admin ${isBanned?'btn-unban':'btn-ban'}" 
                    onclick="toggleBan('${post.prenom} ${post.auteur_nom}')">
              ${isBanned ? 'Débannir' : 'Bannir'}
            </button>
          </div>
          <h2 style="color:#ff69b4;font-size:1.6rem;margin-bottom:10px;">
            ${post.titre} <span class="community-badge">${post.forum_nom}</span>
          </h2>
          <p><strong>Par :</strong> <span style="color:#6e6eff">${post.prenom} ${post.auteur_nom}</span> 
             • ${new Date(post.date_publication).toLocaleString("fr-FR")}</p>
          <div style="margin:15px 0;line-height:1.7;color:#ddd;">${post.contenu.replace(/\n/g,"<br>")}</div>
          ${post.image ? `<img src="${post.image}" style="max-width:100%;border-radius:12px;margin:15px 0;">` : ""}
          <div style="padding:15px;background:rgba(110,110,255,0.2);border-radius:12px;margin-top:20px;">
            <strong>Réponses : ${post.nb_reponses}</strong>
          </div>
        `;
        list.appendChild(card);
      });
    });
}

function deletePost(id) {
  if (confirm("Supprimer cette question et toutes ses réponses ?")) {
    fetch("/play-to-help/api_admin.php?action=delete_post", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "id=" + id
    }).then(() => render());
  }
}

function toggleBan(username) {
  const action = confirm(`Bannir ${username} ?`) ? 'ban_user' : 'unban_user';
  fetch(`/play-to-help/api_admin.php?action=${action}`, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "username=" + encodeURIComponent(username)
  }).then(() => render());
}

function clearAll() {
  if (prompt("DANGER ! Tape « OUI_ADMIN_2025 » pour tout supprimer") === "OUI_ADMIN_2025") {
    if (confirm("TOUT VA ÊTRE SUPPRIMÉ ! Dernière chance !")) {
      fetch("/play-to-help/api_admin.php?action=clear_all", {
        method: "POST",
        body: "confirm=OUI_ADMIN_2025"
      }).then(() => render());
    }
  }
}
function render() {
  const search = document.getElementById("searchInput").value;
  fetch(`/play-to-help/api_admin.php?action=get_all&search=${encodeURIComponent(search)}`)
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById("adminPostsList");
      list.innerHTML = "";

      document.getElementById("totalPosts").textContent = data.stats.totalPosts;
      document.getElementById("totalAnswers").textContent = data.stats.totalAnswers;
      document.getElementById("bannedCount").textContent = data.stats.bannedCount;

      if (data.posts.length === 0) {
        list.innerHTML = `<p style="text-align:center;color:#ff69b4;font-size:2rem;padding:100px;">
          ${search ? "Aucun résultat" : "Aucune publication"}
        </p>`;
        return;
      }

      data.posts.forEach(post => {
        const isBanned = false;
        const card = document.createElement("div");
        card.className = "admin-card";
        if (isBanned) card.innerHTML += '<div class="banned-overlay">BANNI</div>';

        card.innerHTML += `
          <div class="admin-actions">
            <button class="btn-admin btn-edit" onclick="showEditForm(${post.id_publication})">✏️ Modifier</button>
            <button class="btn-admin btn-delete" onclick="deletePost(${post.id_publication})">🗑️ Supprimer</button>
            <button class="btn-admin ${isBanned?'btn-unban':'btn-ban'}" 
                    onclick="toggleBan('${post.prenom} ${post.auteur_nom}')">
              ${isBanned ? 'Débannir' : '⛔ Bannir'}
            </button>
          </div>
          <h2 style="color:#ff69b4;font-size:1.6rem;margin-bottom:10px;">
            <span id="postTitle-${post.id_publication}">${post.titre}</span> 
            <span class="community-badge">${post.forum_nom}</span>
          </h2>
          <p><strong>Par :</strong> <span style="color:#6e6eff">${post.prenom} ${post.auteur_nom}</span> 
             • ${new Date(post.date_publication).toLocaleString("fr-FR")}</p>
          <div style="margin:15px 0;line-height:1.7;color:#ddd;" id="postContent-${post.id_publication}">
            ${post.contenu.replace(/\n/g,"<br>")}
          </div>
          ${post.image ? `<img src="${post.image}" style="max-width:100%;border-radius:12px;margin:15px 0;">` : ""}
          
          <!-- Formulaire de modification (caché par défaut) -->
          <div class="admin-edit-form" id="editForm-${post.id_publication}" style="display:none; margin-top:20px; padding:20px; background:rgba(10,10,30,0.9); border-radius:12px; border:2px solid #ff69b4;">
            <h3 style="color:#ff69b4; margin-bottom:15px;">✏️ Modifier la publication</h3>
            <input type="text" id="editTitle-${post.id_publication}" value="${post.titre.replace(/"/g, '&quot;')}" 
                   style="width:100%; padding:12px; margin:8px 0; background:rgba(20,20,40,0.8); border:1px solid #6e6eff; border-radius:8px; color:#fff;">
            <textarea id="editContent-${post.id_publication}" 
                      style="width:100%; height:120px; padding:12px; margin:8px 0; background:rgba(20,20,40,0.8); border:1px solid #6e6eff; border-radius:8px; color:#fff;">${post.contenu}</textarea>
            <div style="text-align:right; margin-top:15px;">
              <button class="btn-admin btn-save" onclick="saveEdit(${post.id_publication})">💾 Sauvegarder</button>
              <button class="btn-admin btn-cancel" onclick="hideEditForm(${post.id_publication})">❌ Annuler</button>
            </div>
          </div>
          
          <div style="padding:15px;background:rgba(110,110,255,0.2);border-radius:12px;margin-top:20px;">
            <strong>Réponses : ${post.nb_reponses}</strong>
          </div>
        `;
        list.appendChild(card);
      });
    });
}

// Fonctions de modification
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

  fetch("/play-to-help/api_admin.php?action=edit_post", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "id=" + id + "&titre=" + encodeURIComponent(titre) + "&contenu=" + encodeURIComponent(contenu)
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      hideEditForm(id);
      // Met à jour l'affichage sans recharger toute la page
      document.getElementById('postTitle-' + id).textContent = titre;
      document.getElementById('postContent-' + id).innerHTML = contenu.replace(/\n/g, '<br>');
      alert('✅ Publication modifiée avec succès !');
    } else {
      alert('❌ Erreur lors de la modification');
    }
  })
  .catch(error => {
    console.error('Erreur modification:', error);
    alert('❌ Erreur réseau lors de la modification');
  });
}

// Les autres fonctions existantes restent les mêmes
function deletePost(id) {
  if (confirm("Supprimer cette question et toutes ses réponses ?")) {
    fetch("/play-to-help/api_admin.php?action=delete_post", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "id=" + id
    }).then(() => render());
  }
}

function toggleBan(username) {
  const action = confirm(`Bannir ${username} ?`) ? 'ban_user' : 'unban_user';
  fetch(`/play-to-help/api_admin.php?action=${action}`, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "username=" + encodeURIComponent(username)
  }).then(() => render());
}

function clearAll() {
  if (prompt("DANGER ! Tape « OUI_ADMIN_2025 » pour tout supprimer") === "OUI_ADMIN_2025") {
    if (confirm("TOUT VA ÊTRE SUPPRIMÉ ! Dernière chance !")) {
      fetch("/play-to-help/api_admin.php?action=clear_all", {
        method: "POST",
        body: "confirm=OUI_ADMIN_2025"
      }).then(() => render());
    }
  }
}


document.getElementById("searchInput").addEventListener("input", render);
render();
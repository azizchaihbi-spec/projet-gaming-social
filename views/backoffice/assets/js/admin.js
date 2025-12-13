function render() {
  const search = document.getElementById("searchInput").value;
  fetch(`api_admin.php?action=get_all&search=${encodeURIComponent(search)}`)
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
        const isBanned = false; // on vérifiera plus tard via une jointure si tu veux
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
    fetch("api_admin.php?action=delete_post", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "id=" + id
    }).then(() => render());
  }
}

function toggleBan(username) {
  const action = confirm(`Bannir ${username} ?`) ? 'ban_user' : 'unban_user';
  fetch(`api_admin.php?action=${action}`, {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "username=" + encodeURIComponent(username)
  }).then(() => render());
}

function clearAll() {
  if (prompt("DANGER ! Tape « OUI_ADMIN_2025 » pour tout supprimer") === "OUI_ADMIN_2025") {
    if (confirm("TOUT VA ÊTRE SUPPRIMÉ ! Dernière chance !")) {
      fetch("api_admin.php?action=clear_all", {
        method: "POST",
        body: "confirm=OUI_ADMIN_2025"
      }).then(() => render());
    }
  }
}

document.getElementById("searchInput").addEventListener("input", render);
render();
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/PublicationModel.php';
require_once __DIR__ . '/../../models/ReponseModel.php';

// Connexion à la base de données
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Initialisation
$publicationModel = new PublicationModel($pdo);
$reponseModel = new ReponseModel($pdo);

// Récupérer tous les forums
$forums = $publicationModel->getForums();

// Statistiques globales
$totalPosts = $publicationModel->getTotalPublications();
$totalAnswers = $reponseModel->getTotalReponses();
$statsForums = $publicationModel->getStatistiquesParForum();

// === FILTRE PAR FORUM (CORRIGÉ) ===
$forumSelectionne = null;
$publications = [];

if (isset($_GET['id_forum']) && !empty($_GET['id_forum']) && is_numeric($_GET['id_forum'])) {
    $idForum = (int)$_GET['id_forum'];
    $publications = $publicationModel->afficherPublicationsParForum($idForum);
    
    foreach ($forums as $forum) {
        if ($forum['id_forum'] == $idForum) {
            $forumSelectionne = $forum;
            break;
        }
    }
} else {
    $publications = $publicationModel->getAllPublicationsForAdmin();
}
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Play2Help • Backoffice Q&A</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Space Mono', monospace; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(34, 211, 238, 0.3); }
    .neon { text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; }
    .glow:hover { box-shadow: 0 0 30px rgba(34, 211, 238, 0.6); }
    .scanline { position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, transparent, #22d3ee, transparent); animation: scan 6s linear infinite; }
    @keyframes scan { 0% { transform: translateY(-100%); } 100% { transform: translateY(100vh); } }
    .font-orbitron { font-family: 'Orbitron', sans-serif; }
    
    /* Animation de scroll automatique */
    .smooth-scroll {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body class="relative min-h-screen overflow-x-hidden smooth-scroll">
  <div class="scanline"></div>

  <main class="container mx-auto px-6 py-12 max-w-7xl">
    <!-- TITRE FUTURISTE -->
    <div class="text-center mb-16">
      <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">PLAY2HELP</h1>
      <p class="text-cyan-400 text-xl mt-4">Q&A Dashboard</p>
    </div>

    <!-- 3 CARTES DE STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
      <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
        <h3 class="text-5xl font-bold text-cyan-400 neon"><?= $totalPosts ?></h3>
        <p class="text-gray-300 mt-3 text-lg">Questions</p>
      </div>
      <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
        <h3 class="text-5xl font-bold text-emerald-400"><?= $totalAnswers ?></h3>
        <p class="text-gray-300 mt-3 text-lg">Réponses</p>
      </div>
      <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
        <h3 class="text-5xl font-bold text-purple-400"><?= count($forums) ?></h3>
        <p class="text-gray-300 mt-3 text-lg">Forums</p>
      </div>
    </div>

    <!-- GRAPHIQUES STATISTIQUES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">
      <!-- Graphique Doughnut - Répartition des publications par forum -->
      <div class="card p-10 rounded-3xl glow">
        <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">📊 Répartition par Forum</h2>
        <canvas id="donutChart"></canvas>
      </div>

      <!-- Graphique Bar - Likes par forum -->
      <div class="card p-10 rounded-3xl glow">
        <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">💚 Likes par Forum</h2>
        <canvas id="barChart"></canvas>
      </div>
    </div>

    <!-- CARTES CLIQUABLES PAR FORUM -->
    <div class="card rounded-3xl p-10 glow mb-10">
      <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">🎯 Accès Rapide aux Forums</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($statsForums as $stat): ?>
          <div class="card p-6 rounded-xl text-center border-2 transition transform hover:scale-105 cursor-pointer" 
               style="border-color: <?= htmlspecialchars($stat['couleur']) ?>;"
               onclick="filterByForum(<?= $stat['id_forum'] ?>)">
            <h3 class="text-xl font-bold mb-2" style="color: <?= htmlspecialchars($stat['couleur']) ?>;">
              <?= htmlspecialchars($stat['forum_nom']) ?>
            </h3>
            <div class="text-4xl font-bold text-white my-3"><?= $stat['nb_publications'] ?></div>
            <p class="text-gray-400 text-sm">publications</p>
            <p class="text-emerald-400 text-sm mt-2">💚 <?= $stat['total_likes'] ?> likes</p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- FILTRE PAR FORUM — AVEC SOUMISSION AUTOMATIQUE -->
    <div class="card rounded-3xl p-10 glow mb-10" id="filterSection">
      <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Filtrer par Forum</h2>
      
      <form method="GET" action="" id="filterForm" class="flex flex-col md:flex-row gap-4">
        <select name="id_forum" id="forumSelect" 
                class="flex-1 bg-gray-800 border-2 border-cyan-500 rounded-full px-6 py-3 text-white focus:outline-none focus:ring-2 focus:ring-cyan-400">
          <option value="">-- Tous les forums --</option>
          <?php foreach ($forums as $forum): ?>
            <option value="<?= $forum['id_forum'] ?>"
                    <?= (isset($_GET['id_forum']) && $_GET['id_forum'] == $forum['id_forum']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($forum['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-3 rounded-full text-xl font-bold hover:scale-110 transition">
          🔍 Filtrer
        </button>
        
        <?php if (isset($_GET['id_forum']) && !empty($_GET['id_forum'])): ?>
          <button type="button" onclick="resetFilter()" 
                  class="bg-gradient-to-r from-red-500 to-pink-500 px-8 py-3 rounded-full text-xl font-bold hover:scale-110 transition">
            ✖ Réinitialiser
          </button>
        <?php endif; ?>
      </form>
    </div>

    <!-- BANNIÈRE DU FORUM SÉLECTIONNÉ -->
    <?php if ($forumSelectionne): ?>
      <div class="card rounded-3xl p-10 glow mb-10 border-2 animate-pulse" 
           style="border-color: <?= htmlspecialchars($forumSelectionne['couleur']) ?>;" 
           id="forumBanner">
        <h2 class="text-4xl font-bold text-center mb-4" style="color: <?= htmlspecialchars($forumSelectionne['couleur']) ?>;">
          📁 <?= htmlspecialchars($forumSelectionne['nom']) ?>
        </h2>
        <p class="text-center text-gray-300 text-lg mb-4">
          <?= htmlspecialchars($forumSelectionne['description']) ?>
        </p>
        <p class="text-center text-cyan-400 font-bold text-2xl">
          ✅ <?= count($publications) ?> publication(s) trouvée(s)
        </p>
      </div>
    <?php endif; ?>

    <!-- LISTE DES PUBLICATIONS -->
    <div class="card rounded-3xl p-10 glow mb-20" id="publicationsList">
      <h2 class="text-4xl font-bold text-center mb-10 neon font-orbitron">
        📝 Publications <?= $forumSelectionne ? 'du forum ' . htmlspecialchars($forumSelectionne['nom']) : '' ?>
      </h2>
      
      <?php if (!empty($publications)): ?>
        <div class="grid grid-cols-1 gap-6">
          <?php foreach ($publications as $pub): ?>
            <div class="card p-8 rounded-2xl border-2 border-gray-700 hover:border-cyan-500 transition publication-item">
              <h3 class="text-2xl font-bold text-cyan-400 mb-3">
                <?= htmlspecialchars($pub['titre']) ?>
              </h3>
              <div class="text-gray-400 mb-4 flex flex-wrap gap-4">
                <span>👤 <?= htmlspecialchars($pub['prenom']) ?> <?= htmlspecialchars($pub['auteur_nom'] ?? '') ?></span>
                <span>📅 <?= date('d/m/Y à H:i', strtotime($pub['date_publication'])) ?></span>
                <span class="font-bold px-3 py-1 rounded-full" 
                      style="background-color: <?= htmlspecialchars($pub['forum_couleur'] ?? '#22d3ee') ?>33; color: <?= htmlspecialchars($pub['forum_couleur'] ?? '#22d3ee') ?>;">
                  📁 <?= htmlspecialchars($pub['forum_nom']) ?>
                </span>
              </div>
              <div class="text-gray-300 mb-4 leading-relaxed">
                <?= nl2br(htmlspecialchars($pub['contenu'])) ?>
              </div>
              <?php if (!empty($pub['image'])): ?>
                <img src="/play-to-help/<?= htmlspecialchars($pub['image']) ?>" alt="Image" class="max-w-full rounded-xl mb-4 shadow-lg">
              <?php endif; ?>
              <!-- Zone d'édition (cachée par défaut) -->
              <div id="editForm-<?= $pub['id_publication'] ?>" class="hidden mt-6 p-6 bg-gray-900 rounded-xl border-2 border-cyan-500">
                <h4 class="text-xl font-bold text-cyan-400 mb-4">✏️ Modifier la publication</h4>
                <input type="text" id="editTitle-<?= $pub['id_publication'] ?>" 
                       value="<?= htmlspecialchars($pub['titre']) ?>"
                       class="w-full bg-gray-800 border-2 border-gray-700 rounded-lg px-4 py-3 text-white mb-4 focus:border-cyan-500 focus:outline-none">
                <textarea id="editContent-<?= $pub['id_publication'] ?>" rows="6"
                          class="w-full bg-gray-800 border-2 border-gray-700 rounded-lg px-4 py-3 text-white mb-4 focus:border-cyan-500 focus:outline-none"><?= htmlspecialchars($pub['contenu']) ?></textarea>
                <div class="flex gap-4">
                  <button onclick="saveEdit(<?= $pub['id_publication'] ?>)" 
                          class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-3 rounded-full transition flex items-center gap-2">
                    <i data-feather="save"></i> Sauvegarder
                  </button>
                  <button onclick="cancelEdit(<?= $pub['id_publication'] ?>)" 
                          class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-full transition">
                    ✖ Annuler
                  </button>
                </div>
              </div>

              <div class="flex justify-between items-center pt-4 border-t border-gray-700">
                <div class="flex gap-6">
                  <span class="text-emerald-400 font-bold">💚 <?= $pub['likes'] ?> likes</span>
                  <span class="text-yellow-400 font-bold">💬 <?= $pub['nb_reponses'] ?> réponses</span>
                </div>
                <div class="flex gap-3">
                  <button onclick="showEditForm(<?= $pub['id_publication'] ?>)" 
                          class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded-full transition flex items-center gap-2">
                    <i data-feather="edit-2"></i> Modifier
                  </button>
                  <button onclick="deletePublication(<?= $pub['id_publication'] ?>)" 
                          class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-full transition flex items-center gap-2">
                    <i data-feather="trash-2"></i> Supprimer
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-center py-20">
          <p class="text-gray-400 text-2xl">😢 Aucune publication trouvée</p>
          <?php if (isset($_GET['id_forum'])): ?>
            <button onclick="resetFilter()" 
                    class="mt-6 bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-3 rounded-full text-xl font-bold hover:scale-110 transition">
              Voir toutes les publications
            </button>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <footer class="text-center py-8 text-gray-500 border-t border-gray-800 mt-20">
    <p>Copyright © 2025 <span class="text-cyan-400 font-bold">Play to Help</span></p>
  </footer>

  <script>
    feather.replace();

    // === DONNÉES POUR LES GRAPHIQUES ===
    const forumsData = <?= json_encode($statsForums) ?>;
    const forumsLabels = forumsData.map(f => f.forum_nom);
    const forumsPublications = forumsData.map(f => parseInt(f.nb_publications));
    const forumsLikes = forumsData.map(f => parseInt(f.total_likes));
    
    // Forcer des couleurs spécifiques pour éviter les doublons
    const forumsCouleurs = forumsData.map((f, index) => {
      // Forcer D&D en bleu
      if (f.forum_nom === 'D&D' || f.forum_nom === 'DnD' || f.forum_nom.includes('D&D')) {
        return '#3b82f6'; // Bleu
      }
      // Forcer Minecraft en vert
      if (f.forum_nom === 'Minecraft' || f.forum_nom.toLowerCase().includes('minecraft')) {
        return '#10b981'; // Vert
      }
      // Forcer Fortnite en violet
      if (f.forum_nom === 'Fortnite' || f.forum_nom.toLowerCase().includes('fortnite')) {
        return '#a78bfa'; // Violet
      }
      // Autres forums gardent leur couleur ou couleur par défaut
      return f.couleur || '#22d3ee';
    });

    // === GRAPHIQUE DOUGHNUT - Répartition des publications ===
    new Chart(document.getElementById('donutChart'), {
      type: 'doughnut',
      data: {
        labels: forumsLabels,
        datasets: [{
          data: forumsPublications,
          backgroundColor: forumsCouleurs,
          borderColor: '#0f172a',
          borderWidth: 4,
          hoverOffset: 20
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#e2e8f0',
              font: { size: 14, family: 'Space Mono' },
              padding: 15
            }
          },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleColor: '#22d3ee',
            bodyColor: '#e2e8f0',
            borderColor: '#22d3ee',
            borderWidth: 2,
            padding: 12,
            displayColors: true,
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return label + ': ' + value + ' (' + percentage + '%)';
              }
            }
          }
        },
        onClick: (event, elements) => {
          if (elements.length > 0) {
            const index = elements[0].index;
            const forumId = forumsData[index].id_forum;
            filterByForum(forumId);
          }
        }
      }
    });

    // === GRAPHIQUE BAR - Likes par forum ===
    new Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: {
        labels: forumsLabels,
        datasets: [{
          label: 'Likes',
          data: forumsLikes,
          backgroundColor: forumsCouleurs.map(c => c + '99'),
          borderColor: forumsCouleurs,
          borderWidth: 2,
          borderRadius: 8,
          hoverBackgroundColor: forumsCouleurs
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.9)',
            titleColor: '#22d3ee',
            bodyColor: '#e2e8f0',
            borderColor: '#22d3ee',
            borderWidth: 2,
            padding: 12
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              color: '#e2e8f0',
              font: { family: 'Space Mono' }
            },
            grid: { color: 'rgba(255, 255, 255, 0.1)' }
          },
          x: {
            ticks: {
              color: '#e2e8f0',
              font: { family: 'Space Mono' }
            },
            grid: { color: 'rgba(255, 255, 255, 0.1)' }
          }
        },
        onClick: (event, elements) => {
          if (elements.length > 0) {
            const index = elements[0].index;
            const forumId = forumsData[index].id_forum;
            filterByForum(forumId);
          }
        }
      }
    });

    // === SOUMISSION AUTOMATIQUE DÈS QUE VOUS CHANGEZ LE FORUM ===
    document.getElementById('forumSelect').addEventListener('change', function() {
      // Soumet automatiquement le formulaire
      document.getElementById('filterForm').submit();
    });

    // === FONCTION POUR FILTRER PAR FORUM (depuis les cartes stats) ===
    function filterByForum(forumId) {
      window.location.href = window.location.pathname + '?id_forum=' + forumId;
    }

    // === RÉINITIALISER LE FILTRE ===
    function resetFilter() {
      window.location.href = window.location.pathname;
    }

    // === SUPPRESSION D'UNE PUBLICATION ===
    function deletePublication(id) {
      if (!confirm("⚠️ Supprimer cette publication et toutes ses réponses ?")) return;
      
      fetch('/play-to-help/api_admin.php?action=delete_post', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('✅ Publication supprimée avec succès !');
          location.reload();
        } else {
          alert('❌ Erreur lors de la suppression');
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        alert('❌ Erreur réseau');
      });
    }

    // === AFFICHER LE FORMULAIRE DE MODIFICATION ===
    function showEditForm(id) {
      const form = document.getElementById('editForm-' + id);
      if (form) {
        form.classList.remove('hidden');
        // Scroll vers le formulaire
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

    // === ANNULER LA MODIFICATION ===
    function cancelEdit(id) {
      const form = document.getElementById('editForm-' + id);
      if (form) {
        form.classList.add('hidden');
      }
    }

    // === SAUVEGARDER LA MODIFICATION ===
    function saveEdit(id) {
      const titre = document.getElementById('editTitle-' + id).value.trim();
      const contenu = document.getElementById('editContent-' + id).value.trim();
      
      if (!titre || !contenu) {
        alert('❌ Le titre et le contenu ne peuvent pas être vides !');
        return;
      }

      // Désactiver le bouton pendant l'envoi
      const saveBtn = event.target;
      saveBtn.disabled = true;
      saveBtn.innerHTML = '<i data-feather="loader"></i> Enregistrement...';

      fetch('/play-to-help/api_admin.php?action=edit_post', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id + '&titre=' + encodeURIComponent(titre) + '&contenu=' + encodeURIComponent(contenu)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('✅ Publication modifiée avec succès !');
          location.reload();
        } else {
          alert('❌ Erreur lors de la modification');
          saveBtn.disabled = false;
          saveBtn.innerHTML = '<i data-feather="save"></i> Sauvegarder';
          feather.replace();
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        alert('❌ Erreur réseau');
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i data-feather="save"></i> Sauvegarder';
        feather.replace();
      });
    }

    // === SCROLL AUTOMATIQUE VERS LES RÉSULTATS SI UN FORUM EST SÉLECTIONNÉ ===
    <?php if ($forumSelectionne): ?>
      window.addEventListener('load', function() {
        document.getElementById('forumBanner').scrollIntoView({ 
          behavior: 'smooth', 
          block: 'center' 
        });
      });
    <?php endif; ?>
  </script>
</body>
</html>
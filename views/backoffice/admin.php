<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BACK-OFFICE Q&A - Admin</title>
  <link rel="stylesheet" href="/play-to-help/assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
  <header class="admin-header">
    <h1>BACK-OFFICE Q&A</h1>
    <p>Gestion complète • Suppression • Bannissement • Statistiques</p>
  </header>

  <div class="stats-grid">
    <div class="stat-card"><h3>Questions</h3><span id="totalPosts">0</span></div>
    <div class="stat-card"><h3>Réponses</h3><span id="totalAnswers">0</span></div>
    <div class="stat-card"><h3>Bannis</h3><span id="bannedCount">0</span></div>
  </div>

  <div class="admin-controls">
    <input type="text" id="searchInput" placeholder="Rechercher par pseudo ou mot-clé..." autocomplete="off">
    <button onclick="clearAll()" class="btn-danger">SUPPRIMER TOUT LE SITE</button>
  </div>

  <div id="adminPostsList" class="posts-grid"></div>
</div>

<script src="/play-to-help/assets/js/admin.js"></script>
</body>
</html>
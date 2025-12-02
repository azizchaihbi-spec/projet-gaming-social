<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Q&A Communauté</title>
  <link rel="stylesheet" href="/play-to-help/assets/css/styleq&a.css">
</head>
<body>

  <div class="container">
    <div class="header">
      <h1>Q&A Communauté</h1>
      <p>Fortnite • D&D • Minecraft • Valorant • Général</p>
    </div>

    <!-- Filtre par communauté -->
    <div style="text-align:center; margin-bottom:20px;">
      <select id="filterCommunity" class="ask-section" style="width:auto; display:inline-block; padding:10px;">
        <option value="all">Toutes les communautés</option>
        <option value="Général">Général</option>
        <option value="Fortnite">Fortnite</option>
        <option value="D&D / Jeux de rôle">D&D / Jeux de rôle</option>
        <option value="Minecraft">Minecraft</option>
        <option value="Valorant">Valorant</option>
      </select>
    </div>

    <!-- Formulaire nouvelle question -->
    <div class="ask-section">
      <h2>Poser une question</h2>
      <form id="newPost">
        <input type="text" id="author" placeholder="Ton pseudo" >
        <input type="text" id="title" placeholder="Titre de la question" >
        <textarea id="content" rows="4" placeholder="Ta question..." ></textarea>

        <p><strong>Communauté :</strong></p>
        <select id="community">
          <?php foreach ($forums as $forum): ?>
            <option value="<?= $forum['id_forum'] ?>"><?= $forum['nom'] ?></option>
          <?php endforeach; ?>
        </select>

        <p><strong>Image (optionnel) :</strong></p>
        <input type="file" id="imageInput" accept="image/*">
        <label for="imageInput">Choisir une image</label>
        <!-- ZONE DE PRÉVISUALISATION -->
        <div id="preview" style="margin:15px 0; text-align:center;"></div>
        
        <button type="submit">Publier la question</button>
      </form>
    </div>

    <div id="postsList">
      <!-- Le contenu sera chargé par JavaScript -->
    </div>
  </div>

  <!-- Inclure votre fichier JavaScript externe -->
  <script src="/play-to-help/assets/js/testq&a.js"></script>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger les forums pour le select (seulement si pas déjà défini par le contrôleur)
if (!isset($forums)) {
    require_once __DIR__ . '/../../config/config.php';
    require_once __DIR__ . '/../../models/PublicationModel.php';

    $pdo = config::getConnexion();
    $publicationModel = new PublicationModel($pdo);
    $forums = $publicationModel->getForums();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Q&A Communauté</title>
  <link rel="icon" type="image/png" href="assets/images/logooo.png">
  <link rel="apple-touch-icon" href="assets/images/logooo.png">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/fontawesome.css" />
  <link rel="stylesheet" href="assets/css/templatemo-cyborg-gaming.css" />
  <link rel="stylesheet" href="assets/css/owl.css" />
  <link rel="stylesheet" href="assets/css/animate.css" />
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
  <link rel="stylesheet" href="assets/css/dons-assoc.css" />
  <link rel="stylesheet" href="assets/css/styleq&a.css">
  <style>
    /* Fix header position au chargement */
    #mainHeader,
    .header-area,
    .header-area.header-sticky {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
      z-index: 99999 !important;
      /* transform géré par JS pour hide/show au scroll */
    }
    
    /* Fix taille des éléments du header - même taille que association.php */
    .header-area .main-nav {
      min-height: 80px !important;
    }
    .header-area .main-nav .nav li a {
      font-size: 15px !important;
      padding: 10px 15px !important;
    }
    .header-area .main-nav .logo img {
      height: 50px !important;
    }
    .header-area .search-input input {
      height: 46px !important;
      font-size: 14px !important;
    }
    
    /* Styles pour les sélecteurs de médias */
    .media-btn {
      background: linear-gradient(135deg, #6e6eff, #ff69b4);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 20px;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s ease;
    }
    .media-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(110, 110, 255, 0.4);
    }
    .media-picker {
      background: rgba(20, 20, 40, 0.95);
      border: 2px solid #6e6eff;
      border-radius: 15px;
      padding: 20px;
      margin: 15px 0;
      max-height: 400px;
      overflow-y: auto;
    }
    .emoji-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
      gap: 5px;
      font-size: 28px;
      text-align: center;
    }
    .emoji-grid span {
      cursor: pointer;
      padding: 5px;
      border-radius: 8px;
      transition: all 0.2s;
    }
    .emoji-grid span:hover {
      background: rgba(110, 110, 255, 0.3);
      transform: scale(1.3);
    }
    .gif-grid, .sticker-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
      gap: 10px;
    }
    .gif-item, .sticker-item {
      cursor: pointer;
      border-radius: 10px;
      overflow: hidden;
      transition: all 0.3s;
      border: 2px solid transparent;
    }
    .gif-item:hover, .sticker-item:hover {
      transform: scale(1.05);
      border-color: #ff69b4;
      box-shadow: 0 5px 20px rgba(255, 105, 180, 0.5);
    }
    .gif-item img, .sticker-item img {
      width: 100%;
      height: 150px;
      object-fit: cover;
      display: block;
    }
    
  </style>
</head>
<body>

    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots"><span></span><span></span><span></span></div>
        </div>
    </div>

    <?php include 'includes/header.php'; ?>

  <div class="container qa-container">
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
        <option value="League of Legends">League of Legends</option>
      </select>
    </div>

    <!-- Formulaire nouvelle question -->
    <div class="ask-section">
      <h2>Poser une question</h2>
      <form id="newPost">
        <input type="text" id="title" placeholder="Titre de la question" >
        <textarea id="content" rows="4" placeholder="Ta question..." ></textarea>

        <!-- BARRE D'OUTILS EMOJIS/GIFS/STICKERS/AI -->
        <div class="media-toolbar" style="margin: 15px 0; display: flex; gap: 10px; flex-wrap: wrap;">
          <button type="button" onclick="toggleEmojiPicker()" class="media-btn">😀 Emojis</button>
          <button type="button" onclick="toggleGifPicker()" class="media-btn">🎬 GIFs</button>
          <button type="button" onclick="toggleStickerPicker()" class="media-btn">✨ Stickers</button>
          <button type="button" onclick="generateQuestionWithAI()" class="media-btn" style="background: linear-gradient(135deg, #ff6b6b, #feca57);">
            🤖 Générer avec AI
          </button>
        </div>

        <!-- SÉLECTEUR D'EMOJIS -->
        <div id="emojiPicker" class="media-picker" style="display: none;">
          <div class="emoji-grid">
            😀 😃 😄 😁 😆 😅 🤣 😂 🙂 🙃 😉 😊 😇 🥰 😍 🤩 😘 😗 😚 😙
            🥲 😋 😛 😜 🤪 😝 🤑 🤗 🤭 🤫 🤔 🤐 🤨 😐 😑 😶 😏 😒 🙄 😬
            🤥 😌 😔 😪 🤤 😴 😷 🤒 🤕 🤢 🤮 🤧 🥵 🥶 😶‍🌫️ 🥴 😵 🤯 🤠 🥳
            🥸 😎 🤓 🧐 😕 😟 🙁 ☹️ 😮 😯 😲 😳 🥺 😦 😧 😨 😰 😥 😢 😭
            😱 😖 😣 😞 😓 😩 😫 🥱 😤 😡 😠 🤬 👍 👎 👊 ✊ 🤛 🤜 🤞 ✌️
            🤟 🤘 👌 🤌 🤏 👈 👉 👆 👇 ☝️ ✋ 🤚 🖐 🖖 👋 🤙 💪 🦾 🖕 ✍️
            🙏 🦶 🦵 🦿 💄 💋 👄 🦷 👅 👂 🦻 👃 👣 👁 👀 🧠 🫀 🫁 🦴 🦷
            🎮 🕹️ 🎯 🎲 🎰 🎳 🎮 🎪 🎭 🎨 🎬 🎤 🎧 🎼 🎹 🥁 🎷 🎺 🎸 🪕
            🎻 🎲 ♟️ 🎯 🎱 🔮 🪄 🧿 🎮 🕹️ 🎰 🎲 🧩 🧸 🪅 🪆 🪡 🧵 🪢 🎁
            🎈 🎏 🎀 🎊 🎉 🎎 🏮 🎐 🧧 ✉️ 📩 📨 📧 💌 📥 📤 📦 🏷️ 🪧 🔖
          </div>
        </div>

        <!-- SÉLECTEUR DE GIFS -->
        <div id="gifPicker" class="media-picker" style="display: none;">
          <input type="text" id="gifSearch" placeholder="🔍 Rechercher un GIF..." 
                 style="width: 100%; padding: 10px; margin-bottom: 10px; border: 2px solid #6e6eff; border-radius: 8px;">
          <div id="gifResults" class="gif-grid"></div>
        </div>

        <!-- SÉLECTEUR DE STICKERS -->
        <div id="stickerPicker" class="media-picker" style="display: none;">
          <input type="text" id="stickerSearch" placeholder="🔍 Rechercher un sticker..." 
                 style="width: 100%; padding: 10px; margin-bottom: 10px; border: 2px solid #6e6eff; border-radius: 8px;">
          <div id="stickerResults" class="sticker-grid"></div>
        </div>

        <p><strong>Communauté :</strong></p>
        <select id="community">
          <?php foreach ($forums as $forum): ?>
            <option value="<?= $forum['id_forum'] ?>"><?= $forum['nom'] ?></option>
          <?php endforeach; ?>
        </select>

        <p><strong>Image (optionnel) :</strong></p>
        <input type="file" id="imageInput" accept="image/*" onchange="previewImage(this)">
        <label for="imageInput">Choisir une image</label>
        <!-- ZONE DE PRÉVISUALISATION -->
        <div id="preview" style="margin:15px 0; text-align:center;"></div>
        
        <button type="submit">Publier la question</button>
      </form>
    </div>

    <script>
      // === GESTION DU FORMULAIRE DE PUBLICATION ===
      document.getElementById('newPost').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const titre = document.getElementById('title').value.trim();
        const contenu = document.getElementById('content').value.trim();
        const id_forum = document.getElementById('community').value;
        const imageFile = document.getElementById('imageInput').files[0];
        
        if (!titre || !contenu) {
          alert('❌ Veuillez remplir le titre et le contenu !');
          return;
        }
        
        // Utiliser FormData pour gérer l'upload d'image
        const formData = new FormData();
        formData.append('titre', titre);
        formData.append('contenu', contenu);
        formData.append('id_forum', id_forum);
        formData.append('emojis', JSON.stringify(window.usedEmojis || []));
        formData.append('gif_url', window.selectedGifUrl || '');
        formData.append('sticker_url', window.selectedStickerUrl || '');
        
        if (imageFile) {
          formData.append('image', imageFile);
        }
        
        try {
          const response = await fetch('../../api.php?action=create_publication', {
            method: 'POST',
            body: formData // Pas de Content-Type header avec FormData
          });
          
          const result = await response.json();
          
          if (result.success) {
            alert('✅ Publication créée avec succès !');
            // Réinitialiser le formulaire
            document.getElementById('newPost').reset();
            window.usedEmojis = [];
            window.selectedGifUrl = null;
            window.selectedStickerUrl = null;
            // Recharger les publications
            if (typeof loadPosts === 'function') {
              loadPosts();
            } else {
              location.reload();
            }
          } else {
            alert('❌ Erreur lors de la création de la publication');
          }
        } catch (error) {
          console.error('Erreur:', error);
          alert('❌ Erreur réseau');
        }
      });
    </script>

    <div id="postsList">
      <!-- Le contenu sera chargé par JavaScript -->
    </div>
  </div>

  <!-- Passer l'ID utilisateur à JavaScript -->
  <script>
    // Debug session complet
    <?php 
    echo "console.log('=== DEBUG SESSION ===');";
    echo "console.log('SESSION exists:', " . (isset($_SESSION) ? 'true' : 'false') . ");";
    echo "console.log('SESSION user exists:', " . (isset($_SESSION['user']) ? 'true' : 'false') . ");";
    if (isset($_SESSION['user'])) {
        echo "console.log('SESSION user keys:', " . json_encode(array_keys($_SESSION['user'])) . ");";
        echo "console.log('SESSION user:', " . json_encode($_SESSION['user']) . ");";
    }
    echo "console.log('=== FIN DEBUG ===');";
    ?>
    
    // Récupérer l'ID utilisateur depuis la session PHP et les rendre globales
    window.CURRENT_USER_ID = <?php echo isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 'null'; ?>;
    window.CURRENT_USER_NAME = "<?php echo isset($_SESSION['user']['username']) ? htmlspecialchars($_SESSION['user']['username']) : 'Anonyme'; ?>";
    
    console.log('CURRENT_USER_ID final:', window.CURRENT_USER_ID);
    console.log('CURRENT_USER_NAME final:', window.CURRENT_USER_NAME);
  </script>
  
  <!-- Scripts -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/tabs.js"></script>
  <script src="assets/js/popup.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/dons-assoc.js"></script>
  
  <!-- Inclure votre fichier JavaScript externe -->
  <script src="assets/js/testq&a.js"></script>
  

  
  <script>
    // === GIPHY API KEY (gratuite) ===
    const GIPHY_API_KEY = 'GlVGYHkr3WSBnllca54iNt0yFbjz7L65'; // Clé publique de démo
    
    // === HUGGING FACE API (gratuite pour génération de texte) ===
    const HF_API_KEY = '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'; // Clé de démo

    // === TOGGLE EMOJI PICKER ===
    function toggleEmojiPicker() {
      const picker = document.getElementById('emojiPicker');
      const gifPicker = document.getElementById('gifPicker');
      const stickerPicker = document.getElementById('stickerPicker');
      
      gifPicker.style.display = 'none';
      stickerPicker.style.display = 'none';
      picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
      
      if (picker.style.display === 'block' && !picker.dataset.initialized) {
        initEmojiPicker();
        picker.dataset.initialized = 'true';
      }
    }

    // === INITIALISER LE SÉLECTEUR D'EMOJIS ===
    function initEmojiPicker() {
      const emojiGrid = document.querySelector('.emoji-grid');
      const emojis = emojiGrid.textContent.trim().split(/\s+/);
      emojiGrid.innerHTML = '';
      
      emojis.forEach(emoji => {
        const span = document.createElement('span');
        span.textContent = emoji;
        span.onclick = () => insertEmoji(emoji);
        emojiGrid.appendChild(span);
      });
    }

    // === INSÉRER UN EMOJI DANS LE TEXTAREA ===
    function insertEmoji(emoji) {
      const textarea = document.getElementById('content');
      const cursorPos = textarea.selectionStart;
      const textBefore = textarea.value.substring(0, cursorPos);
      const textAfter = textarea.value.substring(cursorPos);
      
      textarea.value = textBefore + emoji + textAfter;
      textarea.focus();
      textarea.selectionStart = textarea.selectionEnd = cursorPos + emoji.length;
      
      // Stocker les emojis utilisés
      if (!window.usedEmojis) window.usedEmojis = [];
      if (!window.usedEmojis.includes(emoji)) {
        window.usedEmojis.push(emoji);
      }
    }

    // === TOGGLE GIF PICKER ===
    function toggleGifPicker() {
      const picker = document.getElementById('gifPicker');
      const emojiPicker = document.getElementById('emojiPicker');
      const stickerPicker = document.getElementById('stickerPicker');
      
      emojiPicker.style.display = 'none';
      stickerPicker.style.display = 'none';
      picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
      
      if (picker.style.display === 'block' && !picker.dataset.initialized) {
        loadTrendingGifs();
        setupGifSearch();
        picker.dataset.initialized = 'true';
      }
    }

    // === CHARGER LES GIFS TENDANCES ===
    async function loadTrendingGifs() {
      const resultsDiv = document.getElementById('gifResults');
      resultsDiv.innerHTML = '<p style="text-align:center; color:#6e6eff;">Chargement...</p>';
      
      try {
        const response = await fetch(`https://api.giphy.com/v1/gifs/trending?api_key=${GIPHY_API_KEY}&limit=20&rating=g`);
        const data = await response.json();
        displayGifs(data.data);
      } catch (error) {
        resultsDiv.innerHTML = '<p style="text-align:center; color:red;">Erreur de chargement</p>';
      }
    }

    // === RECHERCHE DE GIFS ===
    function setupGifSearch() {
      const searchInput = document.getElementById('gifSearch');
      let timeout;
      
      searchInput.addEventListener('input', (e) => {
        clearTimeout(timeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
          loadTrendingGifs();
          return;
        }
        
        timeout = setTimeout(() => searchGifs(query), 500);
      });
    }

    async function searchGifs(query) {
      const resultsDiv = document.getElementById('gifResults');
      resultsDiv.innerHTML = '<p style="text-align:center; color:#6e6eff;">Recherche...</p>';
      
      try {
        const response = await fetch(`https://api.giphy.com/v1/gifs/search?api_key=${GIPHY_API_KEY}&q=${encodeURIComponent(query)}&limit=20&rating=g`);
        const data = await response.json();
        displayGifs(data.data);
      } catch (error) {
        resultsDiv.innerHTML = '<p style="text-align:center; color:red;">Erreur de recherche</p>';
      }
    }

    // === AFFICHER LES GIFS ===
    function displayGifs(gifs) {
      const resultsDiv = document.getElementById('gifResults');
      
      if (gifs.length === 0) {
        resultsDiv.innerHTML = '<p style="text-align:center; color:#888;">Aucun GIF trouvé</p>';
        return;
      }
      
      resultsDiv.innerHTML = '';
      gifs.forEach(gif => {
        const div = document.createElement('div');
        div.className = 'gif-item';
        div.innerHTML = `<img src="${gif.images.fixed_height.url}" alt="${gif.title}">`;
        div.onclick = () => insertGif(gif.images.fixed_height.url);
        resultsDiv.appendChild(div);
      });
    }

    // === INSÉRER UN GIF ===
    function insertGif(gifUrl) {
      // Stocker l'URL du GIF pour l'envoi
      window.selectedGifUrl = gifUrl;
      
      const textarea = document.getElementById('content');
      const gifTag = `\n[GIF sélectionné]\n`;
      textarea.value += gifTag;
      document.getElementById('gifPicker').style.display = 'none';
      alert('✅ GIF ajouté ! Il sera affiché dans votre publication.');
    }

    // === TOGGLE STICKER PICKER ===
    function toggleStickerPicker() {
      const picker = document.getElementById('stickerPicker');
      const emojiPicker = document.getElementById('emojiPicker');
      const gifPicker = document.getElementById('gifPicker');
      
      emojiPicker.style.display = 'none';
      gifPicker.style.display = 'none';
      picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
      
      if (picker.style.display === 'block' && !picker.dataset.initialized) {
        loadTrendingStickers();
        setupStickerSearch();
        picker.dataset.initialized = 'true';
      }
    }

    // === CHARGER LES STICKERS TENDANCES ===
    async function loadTrendingStickers() {
      const resultsDiv = document.getElementById('stickerResults');
      resultsDiv.innerHTML = '<p style="text-align:center; color:#6e6eff;">Chargement...</p>';
      
      try {
        const response = await fetch(`https://api.giphy.com/v1/stickers/trending?api_key=${GIPHY_API_KEY}&limit=20&rating=g`);
        const data = await response.json();
        displayStickers(data.data);
      } catch (error) {
        resultsDiv.innerHTML = '<p style="text-align:center; color:red;">Erreur de chargement</p>';
      }
    }

    // === RECHERCHE DE STICKERS ===
    function setupStickerSearch() {
      const searchInput = document.getElementById('stickerSearch');
      let timeout;
      
      searchInput.addEventListener('input', (e) => {
        clearTimeout(timeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
          loadTrendingStickers();
          return;
        }
        
        timeout = setTimeout(() => searchStickers(query), 500);
      });
    }

    async function searchStickers(query) {
      const resultsDiv = document.getElementById('stickerResults');
      resultsDiv.innerHTML = '<p style="text-align:center; color:#6e6eff;">Recherche...</p>';
      
      try {
        const response = await fetch(`https://api.giphy.com/v1/stickers/search?api_key=${GIPHY_API_KEY}&q=${encodeURIComponent(query)}&limit=20&rating=g`);
        const data = await response.json();
        displayStickers(data.data);
      } catch (error) {
        resultsDiv.innerHTML = '<p style="text-align:center; color:red;">Erreur de recherche</p>';
      }
    }

    // === AFFICHER LES STICKERS ===
    function displayStickers(stickers) {
      const resultsDiv = document.getElementById('stickerResults');
      
      if (stickers.length === 0) {
        resultsDiv.innerHTML = '<p style="text-align:center; color:#888;">Aucun sticker trouvé</p>';
        return;
      }
      
      resultsDiv.innerHTML = '';
      stickers.forEach(sticker => {
        const div = document.createElement('div');
        div.className = 'sticker-item';
        div.innerHTML = `<img src="${sticker.images.fixed_height.url}" alt="${sticker.title}">`;
        div.onclick = () => insertSticker(sticker.images.fixed_height.url);
        resultsDiv.appendChild(div);
      });
    }

    // === INSÉRER UN STICKER ===
    function insertSticker(stickerUrl) {
      // Stocker l'URL du sticker pour l'envoi
      window.selectedStickerUrl = stickerUrl;
      
      const textarea = document.getElementById('content');
      const stickerTag = `\n[STICKER sélectionné]\n`;
      textarea.value += stickerTag;
      document.getElementById('stickerPicker').style.display = 'none';
      alert('✅ Sticker ajouté ! Il sera affiché dans votre publication.');
    }

    // === GÉNÉRER UNE QUESTION AVEC L'IA ===
    async function generateQuestionWithAI() {
      const titleInput = document.getElementById('title');
      const contentInput = document.getElementById('content');
      const communitySelect = document.getElementById('community');
      const communityText = communitySelect.options[communitySelect.selectedIndex].text;
      
      // Vérifier si on génère une question ou une réponse
      const isReplyForm = contentInput.closest('.reply-form');
      
      if (isReplyForm) {
        // C'est un formulaire de réponse
        await generateReplyWithAI(contentInput, isReplyForm);
      } else {
        // C'est un formulaire de question
        await generateFullQuestion(titleInput, contentInput, communityText);
      }
    }

    // === GÉNÉRER UNE QUESTION COMPLÈTE ===
    async function generateFullQuestion(titleInput, contentInput, community) {
      const btn = event.target;
      btn.disabled = true;
      btn.innerHTML = '⏳ Génération...';
      
      try {
        // Prompt pour générer une question
        const prompt = `Génère une question intéressante pour un forum de jeux vidéo sur le thème "${community}". 
Format:
TITRE: [titre court et accrocheur]
QUESTION: [question détaillée en 2-3 phrases]`;

        const response = await fetch('https://api-inference.huggingface.co/models/mistralai/Mistral-7B-Instruct-v0.2', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${HF_API_KEY}`,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            inputs: prompt,
            parameters: {
              max_new_tokens: 200,
              temperature: 0.8,
              top_p: 0.9,
              return_full_text: false
            }
          })
        });

        const data = await response.json();
        
        if (data[0] && data[0].generated_text) {
          const text = data[0].generated_text;
          
          // Extraire le titre et le contenu
          const titleMatch = text.match(/TITRE:\s*(.+?)(?:\n|QUESTION:)/i);
          const questionMatch = text.match(/QUESTION:\s*(.+)/is);
          
          if (titleMatch) {
            titleInput.value = titleMatch[1].trim();
          }
          if (questionMatch) {
            contentInput.value = questionMatch[1].trim();
          } else {
            // Si le format n'est pas respecté, mettre tout dans le contenu
            contentInput.value = text.trim();
          }
          
          alert('✅ Question générée par l\'IA ! Vous pouvez la modifier avant de publier.');
        } else {
          throw new Error('Pas de réponse de l\'IA');
        }
      } catch (error) {
        console.error('Erreur IA:', error);
        // Fallback avec des questions prédéfinies
        generateFallbackQuestion(titleInput, contentInput, community);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '🤖 Générer avec AI';
      }
    }

    // === GÉNÉRER UNE RÉPONSE AVEC L'IA ===
    async function generateReplyWithAI(contentInput, replyForm) {
      const btn = event.target;
      btn.disabled = true;
      btn.innerHTML = '⏳ Génération...';
      
      try {
        // Récupérer le contexte de la question
        const postCard = replyForm.closest('.post-card');
        const questionTitle = postCard.querySelector('h3')?.textContent || '';
        const questionContent = postCard.querySelector('.post-content')?.textContent || '';
        
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
          contentInput.value = data[0].generated_text.trim();
          alert('✅ Réponse générée par l\'IA ! Vous pouvez la modifier avant de publier.');
        } else {
          throw new Error('Pas de réponse de l\'IA');
        }
      } catch (error) {
        console.error('Erreur IA:', error);
        // Fallback avec des réponses prédéfinies
        generateFallbackReply(contentInput);
      } finally {
        btn.disabled = false;
        btn.innerHTML = '🤖 Générer avec AI';
      }
    }

    // === FALLBACK - Questions prédéfinies si l'API ne fonctionne pas ===
    function generateFallbackQuestion(titleInput, contentInput, community) {
      const questions = {
        'Fortnite': [
          { title: 'Meilleure stratégie pour gagner en solo?', content: 'Salut! Je cherche des conseils pour améliorer mon gameplay en solo. Quelles sont vos meilleures stratégies pour survivre jusqu\'à la fin? 🎮' },
          { title: 'Quel est le meilleur skin de la saison?', content: 'Hey la commu! Quel skin vous préférez cette saison? J\'hésite à acheter le Battle Pass 😊' }
        ],
        'Minecraft': [
          { title: 'Comment trouver des diamants rapidement?', content: 'Bonjour! Je débute dans Minecraft et j\'ai du mal à trouver des diamants. Vous avez des astuces? ⛏️' },
          { title: 'Idées de construction originales?', content: 'Salut! Je cherche de l\'inspiration pour ma prochaine construction. Vous avez des idées créatives? 🏰' }
        ],
        'D&D / Jeux de rôle': [
          { title: 'Conseils pour créer un bon personnage?', content: 'Hello! Je vais jouer ma première partie de D&D. Comment créer un personnage intéressant? 🎲' },
          { title: 'Quelle classe pour débuter?', content: 'Salut! Je suis nouveau dans D&D. Quelle classe me conseillez-vous pour commencer? 🧙‍♂️' }
        ],
        'Valorant': [
          { title: 'Meilleur agent pour débuter?', content: 'Hey! Je commence Valorant. Quel agent est le plus facile à maîtriser pour un débutant? 🎯' },
          { title: 'Comment améliorer mon aim?', content: 'Salut! Des conseils pour améliorer ma précision? Je rate trop de tirs 😅' }
        ],
        'Général': [
          { title: 'Quel jeu me conseillez-vous?', content: 'Bonjour! Je cherche un nouveau jeu à découvrir. Vous avez des recommandations? 🎮' },
          { title: 'Meilleur setup gaming?', content: 'Salut! Je veux améliorer mon setup. Quels sont vos périphériques préférés? ⌨️🖱️' }
        ]
      };
      
      const communityQuestions = questions[community] || questions['Général'];
      const randomQ = communityQuestions[Math.floor(Math.random() * communityQuestions.length)];
      
      titleInput.value = randomQ.title;
      contentInput.value = randomQ.content;
      alert('✅ Question générée ! (Mode hors ligne)');
    }

    // === FALLBACK - Réponses prédéfinies ===
    function generateFallbackReply(contentInput) {
      const replies = [
        'Super question! D\'après mon expérience, je te conseille de commencer par les bases et de pratiquer régulièrement. N\'hésite pas si tu as d\'autres questions! 😊',
        'Salut! Je pense que la meilleure approche est de tester différentes stratégies et de voir ce qui fonctionne pour toi. Bon courage! 💪',
        'Hey! J\'ai eu le même problème au début. Ce qui m\'a aidé c\'est de regarder des tutoriels et de m\'entraîner. Tu vas y arriver! 🎮',
        'Bonne question! Je te recommande de rejoindre une communauté active où tu pourras échanger des astuces. Ça aide beaucoup! 🤝'
      ];
      
      const randomReply = replies[Math.floor(Math.random() * replies.length)];
      contentInput.value = randomReply;
      alert('✅ Réponse générée ! (Mode hors ligne)');
    }

    // === PRÉVISUALISATION D'IMAGE ===
    function previewImage(input) {
      const preview = document.getElementById('preview');
      preview.innerHTML = '';
      
      if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Vérifier la taille (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          alert('❌ L\'image est trop lourde (max 5MB)');
          input.value = '';
          return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
          const img = document.createElement('img');
          img.src = e.target.result;
          img.style.cssText = 'max-width: 300px; max-height: 200px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);';
          
          const removeBtn = document.createElement('button');
          removeBtn.type = 'button';
          removeBtn.innerHTML = '❌ Supprimer';
          removeBtn.style.cssText = 'display: block; margin: 10px auto; padding: 5px 10px; background: #ff4757; color: white; border: none; border-radius: 5px; cursor: pointer;';
          removeBtn.onclick = function() {
            input.value = '';
            preview.innerHTML = '';
          };
          
          preview.appendChild(img);
          preview.appendChild(removeBtn);
        };
        reader.readAsDataURL(file);
      }
    }

    // === AJOUTER LE BOUTON AI AUX FORMULAIRES DE RÉPONSE ===
    // Cette fonction sera appelée quand un formulaire de réponse est créé
    window.addAIButtonToReplyForm = function(replyForm) {
      const textarea = replyForm.querySelector('textarea');
      if (!textarea) return;
      
      // Vérifier si le bouton n'existe pas déjà
      if (replyForm.querySelector('.ai-reply-btn')) return;
      
      const aiBtn = document.createElement('button');
      aiBtn.type = 'button';
      aiBtn.className = 'media-btn ai-reply-btn';
      aiBtn.style.cssText = 'background: linear-gradient(135deg, #ff6b6b, #feca57); margin: 10px 0;';
      aiBtn.innerHTML = '🤖 Générer une réponse avec AI';
      aiBtn.onclick = generateQuestionWithAI;
      
      textarea.parentNode.insertBefore(aiBtn, textarea.nextSibling);
    };
  </script>
  
  <?php include 'includes/footer.php'; ?>
</body>
</html>
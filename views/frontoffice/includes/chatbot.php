<!-- CHATBOT IA FLOTTANT - Play to Help -->
<div id="chatbot-container">
    <!-- Bouton flottant -->
    <button id="chatbot-toggle" class="chatbot-btn" title="Discuter avec notre assistant IA">
        <span class="chatbot-icon">🤖</span>
        <span class="chatbot-pulse"></span>
    </button>

    <!-- Fenêtre de chat -->
    <div id="chatbot-window" class="chatbot-window hidden">
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <span class="chatbot-avatar">🎮</span>
                <div>
                    <h4>Play to Help Assistant</h4>
                    <span class="chatbot-status">● En ligne</span>
                </div>
            </div>
            <button id="chatbot-close" class="chatbot-close-btn">&times;</button>
        </div>
        
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="chat-message bot">
                <div class="message-content">
                    Salut ! 👋 Je suis l'assistant IA de Play to Help. Comment puis-je t'aider aujourd'hui ?
                    <br><br>
                    Tu peux me poser des questions sur :
                    <ul>
                        <li>🎮 Les événements gaming</li>
                        <li>💚 Les dons et associations</li>
                        <li>📺 Les streams solidaires</li>
                        <li>❓ L'utilisation du site</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="chatbot-input-area">
            <div class="chatbot-suggestions">
                <button class="suggestion-btn" data-msg="Comment faire un don ?">💚 Faire un don</button>
                <button class="suggestion-btn" data-msg="Quels événements sont disponibles ?">🎮 Événements</button>
                <button class="suggestion-btn" data-msg="Comment devenir streamer ?">📺 Streamer</button>
            </div>
            <div class="chatbot-input-wrapper">
                <input type="text" id="chatbot-input" placeholder="Écris ton message..." autocomplete="off">
                <button id="chatbot-send" class="chatbot-send-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== CHATBOT STYLES ===== */
#chatbot-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 99999;
    font-family: 'Poppins', sans-serif;
}

/* Bouton flottant */
.chatbot-btn {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    position: relative;
    overflow: visible;
}

.chatbot-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 40px rgba(102, 126, 234, 0.6);
}

.chatbot-icon {
    font-size: 28px;
    z-index: 2;
}

.chatbot-pulse {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    animation: pulse 2s infinite;
    z-index: 1;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 0.7; }
    50% { transform: scale(1.3); opacity: 0; }
    100% { transform: scale(1); opacity: 0; }
}

/* Fenêtre de chat */
.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 550px;
    max-height: calc(100vh - 150px);
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    transform-origin: bottom right;
}

.chatbot-window.hidden {
    opacity: 0;
    transform: scale(0.8);
    pointer-events: none;
}

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chatbot-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chatbot-avatar {
    font-size: 32px;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px;
    border-radius: 12px;
}

.chatbot-header h4 {
    color: white;
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.chatbot-status {
    color: #90EE90;
    font-size: 12px;
}

.chatbot-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.chatbot-close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

/* Messages */
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.chat-message {
    display: flex;
    gap: 10px;
    max-width: 85%;
    animation: messageSlide 0.3s ease;
}

@keyframes messageSlide {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.chat-message.bot {
    align-self: flex-start;
}

.chat-message.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-content {
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.5;
}

.chat-message.bot .message-content {
    background: rgba(102, 126, 234, 0.2);
    color: #e0e0e0;
    border-bottom-left-radius: 4px;
}

.chat-message.user .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.message-content ul {
    margin: 10px 0 0 0;
    padding-left: 20px;
}

.message-content li {
    margin: 5px 0;
}

/* Typing indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 12px 16px;
    background: rgba(102, 126, 234, 0.2);
    border-radius: 18px;
    width: fit-content;
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    background: #667eea;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-8px); }
}

/* Input area */
.chatbot-input-area {
    padding: 15px;
    background: rgba(0, 0, 0, 0.2);
    border-top: 1px solid rgba(102, 126, 234, 0.2);
}

.chatbot-suggestions {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.suggestion-btn {
    background: rgba(102, 126, 234, 0.15);
    border: 1px solid rgba(102, 126, 234, 0.3);
    color: #a0a0ff;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.suggestion-btn:hover {
    background: rgba(102, 126, 234, 0.3);
    color: white;
    transform: translateY(-2px);
}

.chatbot-input-wrapper {
    display: flex;
    gap: 10px;
    align-items: center;
}

#chatbot-input {
    flex: 1;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 25px;
    padding: 12px 20px;
    color: white;
    font-size: 14px;
    outline: none;
    transition: all 0.2s;
}

#chatbot-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

#chatbot-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 15px rgba(102, 126, 234, 0.3);
}

.chatbot-send-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.chatbot-send-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}

.chatbot-send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Scrollbar */
.chatbot-messages::-webkit-scrollbar {
    width: 6px;
}

.chatbot-messages::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: rgba(102, 126, 234, 0.5);
    border-radius: 3px;
}

/* Responsive */
@media (max-width: 480px) {
    #chatbot-container {
        bottom: 20px;
        right: 20px;
    }
    
    .chatbot-window {
        width: calc(100vw - 40px);
        height: calc(100vh - 100px);
        bottom: 75px;
        right: -10px;
    }
    
    .chatbot-btn {
        width: 55px;
        height: 55px;
    }
    
    .chatbot-icon {
        font-size: 24px;
    }
}
</style>

<script>
// ===== CHATBOT IA - Play to Help =====
(function() {
    // Configuration Gemini API
    const GEMINI_API_KEY = 'AIzaSyBJ1keN8Wog_7zfYA_c49S8KzWUdIESsPY';
    // Gemini 2.0 Flash - modèle gratuit
    const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    
    // Rate limiting - éviter les erreurs 429
    let lastRequestTime = 0;
    const MIN_REQUEST_INTERVAL = 3000; // 3 secondes minimum entre les requêtes
    
    // Contexte du chatbot
    const SYSTEM_CONTEXT = `Tu es l'assistant IA de "Play to Help", une plateforme de gaming solidaire qui permet aux joueurs de faire des dons à des associations caritatives tout en jouant.

Ton rôle :
- Aider les utilisateurs à naviguer sur le site
- Expliquer comment faire des dons
- Informer sur les événements gaming solidaires
- Guider les streamers qui veulent rejoindre la plateforme
- Répondre aux questions sur les associations partenaires

Ton style :
- Amical et enthousiaste 🎮
- Utilise des emojis gaming
- Réponds en français
- Sois concis mais utile
- Encourage l'engagement solidaire

Pages du site :
- Accueil : présentation de la plateforme
- Dons : faire un don à une association
- Associations : liste des associations partenaires
- Streams : voir les streams solidaires en direct
- Événements : tournois et challenges gaming
- Forum Q&A : communauté et entraide

Si on te pose des questions hors sujet, ramène gentiment la conversation vers le gaming solidaire.`;

    let conversationHistory = [];
    
    // Éléments DOM
    const toggleBtn = document.getElementById('chatbot-toggle');
    const chatWindow = document.getElementById('chatbot-window');
    const closeBtn = document.getElementById('chatbot-close');
    const messagesContainer = document.getElementById('chatbot-messages');
    const inputField = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');

    // Toggle chat window
    toggleBtn?.addEventListener('click', () => {
        chatWindow.classList.toggle('hidden');
        if (!chatWindow.classList.contains('hidden')) {
            inputField.focus();
        }
    });

    closeBtn?.addEventListener('click', () => {
        chatWindow.classList.add('hidden');
    });

    // Send message
    sendBtn?.addEventListener('click', sendMessage);
    inputField?.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    // Suggestion buttons
    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const msg = btn.getAttribute('data-msg');
            inputField.value = msg;
            sendMessage();
        });
    });

    // Système de réponses intelligentes et interactives
    let userName = null;
    
    function getSmartResponse(message) {
        const msg = message.toLowerCase().trim();
        
        // Salutations et présentations
        if (msg.match(/^(salut|hello|bonjour|hey|coucou|yo|hi)/)) {
            const greetings = [
                "Hey ! 👋 Bienvenue sur Play to Help ! Je suis ton assistant gaming. Comment je peux t'aider aujourd'hui ?",
                "Salut gamer ! 🎮 Ravi de te voir ! Tu veux en savoir plus sur nos dons, événements ou streams ?",
                "Yo ! 👋 Bienvenue dans la communauté ! Qu'est-ce qui t'amène ?"
            ];
            return greetings[Math.floor(Math.random() * greetings.length)];
        }
        
        // Détection du nom
        const nameMatch = msg.match(/(?:je m'appelle|mon nom est|je suis|moi c'est|appelle[- ]moi)\s+(\w+)/i);
        if (nameMatch) {
            userName = nameMatch[1].charAt(0).toUpperCase() + nameMatch[1].slice(1);
            return `Enchanté ${userName} ! 🎮 Super de te rencontrer ! Tu es ici pour faire un don, découvrir nos événements, ou devenir streamer solidaire ?`;
        }
        
        // Questions sur le nom
        if (msg.match(/comment.*appelle|quel.*nom|qui.*es/)) {
            return "Je suis l'assistant IA de Play to Help ! 🤖 Tu peux m'appeler P2H Bot. Et toi, c'est quoi ton pseudo gamer ?";
        }
        
        // Remerciements
        if (msg.match(/merci|thanks|thx|cool|super|génial|parfait/)) {
            const thanks = userName 
                ? `Avec plaisir ${userName} ! 😊 N'hésite pas si tu as d'autres questions !`
                : "De rien ! 😊 Je suis là pour ça ! Autre chose ?";
            return thanks;
        }
        
        // Au revoir
        if (msg.match(/bye|au revoir|à plus|ciao|salut$/)) {
            return userName 
                ? `À bientôt ${userName} ! 👋 Reviens quand tu veux, et n'oublie pas : chaque don compte ! 💚`
                : "À bientôt ! 👋 Reviens nous voir et game for good ! 🎮💚";
        }
        
        // Questions sur Play to Help
        if (msg.match(/c'est quoi|qu'est[- ]ce que|explique|play to help/)) {
            return "🎮 **Play to Help** c'est une plateforme de gaming solidaire !\n\nL'idée : tu joues, tu streames, et tu collectes des dons pour des associations. On transforme ta passion du gaming en force pour le bien ! 💚\n\nTu veux savoir comment participer ?";
        }
        
        // Dons
        if (msg.match(/don|donner|payer|argent|euro|contribuer|soutenir/)) {
            return "💚 **Faire un don c'est simple !**\n\n1. Va sur la page Dons\n2. Choisis ton association préférée\n3. Entre le montant (même 1€ ça compte !)\n4. Paiement sécurisé par Stripe\n\n👉 Tu veux que je t'explique comment créer un Challenge de dons ?";
        }
        
        // Streamer
        if (msg.match(/stream|twitch|youtube|diffuser|live|streamer/)) {
            return "📺 **Devenir streamer solidaire ?**\n\n1. Inscris-toi en tant que Streamer\n2. Connecte ton compte Twitch/YouTube\n3. Crée des Challenges pour ta communauté\n4. Les dons arrivent en direct pendant ton stream !\n\n🎯 C'est gratuit et tu aides des associations. Tu streames sur quelle plateforme ?";
        }
        
        // Événements
        if (msg.match(/event|événement|tournoi|challenge|compétition|participer/)) {
            return "🎮 **Nos événements gaming !**\n\n- 🏆 Tournois caritatifs (Fortnite, LoL, Valorant...)\n- 🎯 Challenges communautaires\n- 📺 Streams marathon solidaires\n\nTout est sur la page Événements ! Tu joues à quoi comme jeux ?";
        }
        
        // Associations
        if (msg.match(/association|caritat|cause|partenaire|ong|humanitaire/)) {
            return "🤝 **Nos associations partenaires**\n\nOn travaille avec des assos vérifiées dans différents domaines :\n- 🏥 Santé\n- 🌍 Environnement\n- 👶 Enfance\n- 🐾 Animaux\n\n100% des dons vont aux associations ! Tu veux voir la liste complète ?";
        }
        
        // Inscription
        if (msg.match(/inscri|compte|register|créer|rejoindre|commencer/)) {
            return "👤 **Rejoindre la communauté !**\n\n1. Clique sur 'S'inscrire' en haut\n2. Choisis : Viewer ou Streamer\n3. Remplis tes infos\n4. C'est parti ! 🎉\n\nTu préfères être viewer ou streamer ?";
        }
        
        // Contact / Aide
        if (msg.match(/contact|discord|aide|support|problème|bug|question/)) {
            return "📞 **Besoin d'aide ?**\n\n- 💬 Discord : discord.gg/zbGbn4Pz (le plus rapide !)\n- 📧 Email : contact@playtohelp.org\n\nLa communauté Discord est super active, viens nous rejoindre ! 🎮";
        }
        
        // Jeux
        if (msg.match(/fortnite|lol|league|valorant|minecraft|fifa|cod|call of duty|apex|csgo|cs2/)) {
            const game = msg.match(/fortnite|lol|league|valorant|minecraft|fifa|cod|call of duty|apex|csgo|cs2/)[0];
            return `Oh tu joues à ${game.toUpperCase()} ? 🎮 Nice ! On a souvent des événements sur ce jeu. Check la page Événements pour voir les prochains tournois ! Tu participes souvent à des tournois ?`;
        }
        
        // Réponses positives/négatives
        if (msg.match(/^(oui|ouais|yes|yep|ok|d'accord|bien sûr)$/)) {
            return "Super ! 🎉 Qu'est-ce que tu veux savoir de plus ? Je peux t'expliquer les dons, les événements, ou comment devenir streamer !";
        }
        
        if (msg.match(/^(non|nope|nan|pas vraiment)$/)) {
            return "Pas de souci ! 😊 Si tu changes d'avis ou si tu as des questions, je suis là. Game on ! 🎮";
        }
        
        // Questions génériques
        if (msg.match(/comment ça marche|comment faire|aide[- ]moi/)) {
            return "Je t'explique ! 🎮\n\n**Play to Help en 3 étapes :**\n1. 📺 Les streamers créent des Challenges\n2. 💚 Les viewers font des dons\n3. 🤝 L'argent va aux associations\n\nTu veux plus de détails sur une partie en particulier ?";
        }
        
        // Réponse par défaut conversationnelle
        const defaults = [
            `Hmm, je ne suis pas sûr de comprendre 🤔 Tu peux me demander des infos sur :\n- 💚 Les dons\n- 📺 Le streaming solidaire\n- 🎮 Les événements\n- 🤝 Les associations`,
            `Je suis spécialisé dans Play to Help ! 🎮 Pose-moi des questions sur les dons, les streams ou les événements gaming solidaires !`,
            `Bonne question ! Mais je suis surtout calé sur le gaming solidaire 😅 Tu veux savoir comment faire un don ou participer à un événement ?`
        ];
        return defaults[Math.floor(Math.random() * defaults.length)];
    }

    async function sendMessage() {
        const message = inputField.value.trim();
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        inputField.value = '';
        sendBtn.disabled = true;

        // Show typing indicator
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot';
        typingDiv.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
        messagesContainer.appendChild(typingDiv);
        scrollToBottom();

        // Simulate typing delay
        await new Promise(resolve => setTimeout(resolve, 800 + Math.random() * 700));

        try {
            const response = await callGeminiAPI(message);
            typingDiv.remove();
            addMessage(response, 'bot');
        } catch (error) {
            console.error('Chatbot API error, using fallback:', error);
            typingDiv.remove();
            // Use smart predefined response as fallback
            const fallbackResponse = getSmartResponse(message);
            addMessage(fallbackResponse, 'bot');
        }

        sendBtn.disabled = false;
    }

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}`;
        messageDiv.innerHTML = `<div class="message-content">${formatMessage(text)}</div>`;
        messagesContainer.appendChild(messageDiv);
        scrollToBottom();
        
        // Save to history
        conversationHistory.push({ role: sender === 'user' ? 'user' : 'model', parts: [{ text }] });
    }

    function formatMessage(text) {
        // Convert markdown-like formatting
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    async function callGeminiAPI(userMessage, retryCount = 0) {
        // Rate limiting - attendre si nécessaire
        const now = Date.now();
        const timeSinceLastRequest = now - lastRequestTime;
        if (timeSinceLastRequest < MIN_REQUEST_INTERVAL) {
            const waitTime = MIN_REQUEST_INTERVAL - timeSinceLastRequest;
            console.log(`Rate limiting: waiting ${waitTime}ms`);
            await new Promise(resolve => setTimeout(resolve, waitTime));
        }
        lastRequestTime = Date.now();

        // Build the prompt with context
        let prompt = SYSTEM_CONTEXT + '\n\n';
        
        // Add conversation history (last 4 messages for context - reduced to save tokens)
        if (conversationHistory.length > 0) {
            const recentHistory = conversationHistory.slice(-4);
            prompt += 'Historique:\n';
            recentHistory.forEach(h => {
                prompt += `${h.role === 'user' ? 'User' : 'Bot'}: ${h.parts[0].text.substring(0, 100)}\n`;
            });
            prompt += '\n';
        }
        
        prompt += 'Utilisateur: ' + userMessage + '\n\nAssistant:';

        const requestBody = {
            contents: [{
                parts: [{ text: prompt }]
            }],
            generationConfig: {
                temperature: 0.8,
                maxOutputTokens: 400
            }
        };

        console.log('Calling Gemini API...');
        
        const response = await fetch(GEMINI_API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-goog-api-key': GEMINI_API_KEY
            },
            body: JSON.stringify(requestBody)
        });

        console.log('Response status:', response.status);

        // Handle rate limiting (429) with retry
        if (response.status === 429 && retryCount < 2) {
            console.log(`Rate limited, retrying in 3 seconds... (attempt ${retryCount + 1})`);
            await new Promise(resolve => setTimeout(resolve, 3000));
            return callGeminiAPI(userMessage, retryCount + 1);
        }

        if (!response.ok) {
            const errorText = await response.text();
            console.error('API Error:', errorText);
            throw new Error('API request failed: ' + response.status);
        }

        const data = await response.json();
        console.log('API Response:', data);
        
        if (data.candidates && data.candidates[0]?.content?.parts?.[0]?.text) {
            return data.candidates[0].content.parts[0].text;
        }
        
        throw new Error('Invalid API response structure');
    }

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!chatWindow.classList.contains('hidden') && 
            !chatWindow.contains(e.target) && 
            !toggleBtn.contains(e.target)) {
            chatWindow.classList.add('hidden');
        }
    });
})();
</script>

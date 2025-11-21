<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Modifier Utilisateur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Mono', monospace; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(34, 211, 238, 0.3); }
        .neon { text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">
    
    <!-- Navigation -->
    <nav class="bg-gray-900/80 backdrop-blur-lg border-b border-cyan-500/30 py-4 px-6">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-cyan-400 font-orbitron">PLAY2HELP</h1>
                <span class="text-gray-400">|</span>
                <span class="text-gray-300">Modifier Utilisateur</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="index.php" class="text-cyan-400 hover:text-cyan-300 transition">Dashboard</a>
                <button class="bg-cyan-600 hover:bg-cyan-500 px-4 py-2 rounded-lg transition">Déconnexion</button>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8 max-w-4xl">
        <!-- Titre -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold font-orbitron neon">MODIFIER UTILISATEUR</h1>
            <p class="text-cyan-400 text-lg mt-2">Modifier les informations de <?= htmlspecialchars($user->getFullName()) ?></p>
        </div>

        <!-- Messages d'erreur -->
        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="bg-red-500/20 border border-red-500/30 rounded-lg p-4 mb-6">
                <h3 class="text-red-400 font-bold mb-2">Erreurs :</h3>
                <ul class="list-disc list-inside">
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li class="text-red-300"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <!-- Formulaire -->
        <div class="card rounded-3xl p-8 glow">
            <form action="index.php?action=edit&id=<?= $user->getId() ?>" method="POST" class="space-y-6">
                
                <!-- Informations de base -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-cyan-400 mb-2">Prénom *</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($user->getFirstName()) ?>" required 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-cyan-400 mb-2">Nom *</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($user->getLastName()) ?>" required 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-cyan-400 mb-2">Nom d'utilisateur *</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user->getUsername()) ?>" required 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-cyan-400 mb-2">Email *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-cyan-400 mb-2">Date de naissance</label>
                        <input type="date" name="birthdate" value="<?= htmlspecialchars($user->getBirthdate()) ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-cyan-400 mb-2">Genre</label>
                        <select name="gender" class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                            <option value="">Sélectionner</option>
                            <option value="male" <?= $user->getGender() === 'male' ? 'selected' : '' ?>>Homme</option>
                            <option value="female" <?= $user->getGender() === 'female' ? 'selected' : '' ?>>Femme</option>
                            <option value="other" <?= $user->getGender() === 'other' ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>
                </div>

                <!-- Localisation -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-cyan-400 mb-2">Pays</label>
                        <input type="text" name="country" value="<?= htmlspecialchars($user->getCountry()) ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>
                    <div>
                        <label class="block text-cyan-400 mb-2">Ville</label>
                        <input type="text" name="city" value="<?= htmlspecialchars($user->getCity()) ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>
                </div>

                <!-- Rôle et mot de passe -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-cyan-400 mb-2">Rôle *</label>
                        <select name="role" required class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                            <option value="viewer" <?= $user->getRole() === 'viewer' ? 'selected' : '' ?>>Viewer</option>
                            <option value="streamer" <?= $user->getRole() === 'streamer' ? 'selected' : '' ?>>Streamer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-cyan-400 mb-2">Mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" name="password" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>
                </div>

                <!-- Section Streamer (conditionnelle) -->
                <div id="streamer-fields" class="space-y-6 <?= $user->getRole() === 'streamer' ? '' : 'hidden' ?>">
                    <h3 class="text-2xl font-bold text-purple-400 border-b border-purple-500/30 pb-2">Informations Streamer</h3>
                    
                    <div>
                        <label class="block text-cyan-400 mb-2">Lien de stream</label>
                        <input type="url" name="stream_link" value="<?= htmlspecialchars($user->getStreamLink()) ?>" 
                               class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                    </div>

                    <div>
                        <label class="block text-cyan-400 mb-2">Description du stream</label>
                        <textarea name="stream_description" rows="3" 
                                  class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400"><?= htmlspecialchars($user->getStreamDescription()) ?></textarea>
                    </div>

                    <div>
                        <label class="block text-cyan-400 mb-2">Plateforme de streaming</label>
                        <select name="stream_platform" class="w-full bg-gray-800 border border-cyan-500/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-400">
                            <option value="">Sélectionner</option>
                            <option value="twitch" <?= $user->getStreamPlatform() === 'twitch' ? 'selected' : '' ?>>Twitch</option>
                            <option value="youtube" <?= $user->getStreamPlatform() === 'youtube' ? 'selected' : '' ?>>YouTube</option>
                            <option value="facebook" <?= $user->getStreamPlatform() === 'facebook' ? 'selected' : '' ?>>Facebook Gaming</option>
                            <option value="tiktok" <?= $user->getStreamPlatform() === 'tiktok' ? 'selected' : '' ?>>TikTok</option>
                        </select>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-800">
                    <a href="index.php" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-lg transition">Annuler</a>
                    <button type="submit" class="bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 px-8 py-3 rounded-lg font-bold transition">
                        Modifier l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        feather.replace();

        // Afficher/masquer les champs streamer
        const roleSelect = document.querySelector('select[name="role"]');
        const streamerFields = document.getElementById('streamer-fields');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'streamer') {
                streamerFields.classList.remove('hidden');
            } else {
                streamerFields.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
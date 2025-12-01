<<?php
// Vérifier si la session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Profil Utilisateur</title>
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
                <span class="text-gray-300">Profil Utilisateur</span>
            </div>
            <div class="flex items-center space-x-6">
                <a href="index.php" class="text-cyan-400 hover:text-cyan-300 transition">Dashboard</a>

            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-8 max-w-4xl">
        <!-- Titre -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold font-orbitron neon">PROFIL UTILISATEUR</h1>
            <p class="text-cyan-400 text-lg mt-2">Détails de <?= htmlspecialchars($user->getFullName()) ?></p>
        </div>

        <!-- Carte profil -->
        <div class="card rounded-3xl p-8 glow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Photo et informations principales -->
                <div class="md:col-span-1 text-center">
                    <div class="w-32 h-32 bg-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl font-bold">
                        <?= strtoupper(substr($user->getFirstName(), 0, 1)) ?>
                    </div>
                    <h2 class="text-2xl font-bold text-white"><?= htmlspecialchars($user->getFullName()) ?></h2>
                    <p class="text-cyan-400">@<?= htmlspecialchars($user->getUsername()) ?></p>
                    
                    <div class="mt-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                            <?= $user->getRole() === 'streamer' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30' ?>">
                            <?= $user->getRole() === 'streamer' ? '🎥 Streamer' : '👁️ Viewer' ?>
                        </span>
                    </div>
                    
                    <div class="mt-6">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                            ✅ Actif
                        </span>
                    </div>
                </div>

                <!-- Informations détaillées -->
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-cyan-400 mb-4">Informations Personnelles</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-cyan-400 text-sm">Email</label>
                                <p class="text-white"><?= htmlspecialchars($user->getEmail()) ?></p>
                            </div>
                            <div>
                                <label class="block text-cyan-400 text-sm">Date de naissance</label>
                                <p class="text-white"><?= $user->getBirthdate() ? date('d/m/Y', strtotime($user->getBirthdate())) : 'Non renseignée' ?></p>
                            </div>
                            <div>
                                <label class="block text-cyan-400 text-sm">Genre</label>
                                <p class="text-white">
                                    <?= match($user->getGender()) {
                                        'male' => 'Homme',
                                        'female' => 'Femme',
                                        'other' => 'Autre',
                                        default => 'Non renseigné'
                                    } ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-cyan-400 text-sm">Âge</label>
                                <p class="text-white"><?= $user->getAge() ?? 'N/A' ?> ans</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold text-cyan-400 mb-4">Localisation</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-cyan-400 text-sm">Pays</label>
                                <p class="text-white"><?= htmlspecialchars($user->getCountry() ?: 'Non renseigné') ?></p>
                            </div>
                            <div>
                                <label class="block text-cyan-400 text-sm">Ville</label>
                                <p class="text-white"><?= htmlspecialchars($user->getCity() ?: 'Non renseignée') ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($user->isStreamer()): ?>
                    <div>
                        <h3 class="text-xl font-bold text-purple-400 mb-4">Informations Streamer</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-cyan-400 text-sm">Lien de stream</label>
                                <p class="text-white">
                                    <?php if ($user->getStreamLink()): ?>
                                        <a href="<?= htmlspecialchars($user->getStreamLink()) ?>" target="_blank" class="text-cyan-400 hover:text-cyan-300">
                                            <?= htmlspecialchars($user->getStreamLink()) ?>
                                        </a>
                                    <?php else: ?>
                                        Non renseigné
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-cyan-400 text-sm">Plateforme</label>
                                <p class="text-white"><?= htmlspecialchars($user->getStreamPlatform() ?: 'Non renseignée') ?></p>
                            </div>
                            <div>
                                <label class="block text-cyan-400 text-sm">Description</label>
                                <p class="text-white"><?= htmlspecialchars($user->getStreamDescription() ?: 'Aucune description') ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div>
                        <h3 class="text-xl font-bold text-cyan-400 mb-4">Informations Compte</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-cyan-400 text-sm">Date d'inscription</label>
                                <p class="text-white"><?= $user->getFormattedJoinDate() ?></p>
                            </div>
                            <div>
                                <label class="block text-cyan-400 text-sm">ID Utilisateur</label>
                                <p class="text-white font-mono">#<?= $user->getId() ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end space-x-4 pt-6 mt-6 border-t border-gray-800">
                <a href="index.php" class="bg-gray-700 hover:bg-gray-600 px-6 py-3 rounded-lg transition">Retour</a>
                <a href="index.php?action=edit&id=<?= $user->getId() ?>" class="bg-gradient-to-r from-cyan-500 to-emerald-500 hover:from-cyan-600 hover:to-emerald-600 px-6 py-3 rounded-lg font-bold transition">
                Modifier le profil
                </a>
            </div>
        </div>
    </main>

    <script>
        feather.replace();
    </script>
</body>
</html>
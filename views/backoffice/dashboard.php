<?php
require_once __DIR__ . '/../../controllers/StreamController.php';
require_once __DIR__ . '/../../controllers/EventController.php';

session_start();

$streamController = new StreamController();
$eventController = new EventController();

$streams = $streamController->listStreams();
$events = $eventController->listEvents();

// Debug: voir les données récupérées
error_log("Dashboard - Nombre de streams: " . count($streams));
if (!empty($streams)) {
    error_log("Dashboard - Premier stream: " . print_r($streams[0], true));
}

$totalStreams = count($streams);
$streamsEnCours = count(array_filter($streams, fn($s) => $s['statut'] === 'en_cours'));
$totalDonsStreams = array_sum(array_column($streams, 'don_total'));
$totalVues = array_sum(array_column($streams, 'nb_vues'));

$totalEvents = count($events);
$now = new DateTime();
$eventsEnCours = count(array_filter($events, function($e) use ($now) {
    $debut = new DateTime($e['date_debut']);
    $fin = new DateTime($e['date_fin']);
    return $debut <= $now && $now <= $fin;
}));
$eventsAVenir = count(array_filter($events, function($e) use ($now) {
    $debut = new DateTime($e['date_debut']);
    return $debut > $now;
}));
$objectifTotal = array_sum(array_column($events, 'objectif'));

if (isset($_GET['deleted']) && $_GET['deleted'] === 'success') {
    $deleteMsg = "Élément supprimé avec succès";
}
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Backoffice Unifié</title>
    <link rel="icon" type="image/png" href="../../views/frontoffice/assets/images/logooo.png">
    <link rel="apple-touch-icon" href="../../views/frontoffice/assets/images/logooo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Mono', monospace; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(34, 211, 238, 0.3); }
        .neon { text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; }
        .glow:hover { box-shadow: 0 0 30px rgba(34, 211, 238, 0.6); }
        .scanline { position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, transparent, #22d3ee, transparent); animation: scan 6s linear infinite; }
        @keyframes scan { 0% { transform: translateY(-100%); } 100% { transform: translateY(100vh); } }
        .tab-active { background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%); }
        .tab-inactive { background: rgba(30, 41, 59, 0.5); }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">

    <div class="scanline"></div>

    <!-- Header commun -->
    <?php include __DIR__ . '/includes/header.php'; ?>

    <?php if (isset($deleteMsg)): ?>
        <div class="container mx-auto px-6 py-4">
            <div class="bg-emerald-500/10 border border-emerald-500 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <i data-feather="check-circle" class="text-emerald-400"></i>
                    <span class="text-emerald-400"><?= $deleteMsg ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <main class="container mx-auto px-6 py-12 relative z-10">

        <!-- TITRE PRINCIPAL -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-7xl font-bold font-orbitron neon animate-pulse">
                BACKOFFICE UNIFIÉ
            </h1>
            <p class="text-cyan-400 text-xl mt-4">Gestion centralisée • Streams & Événements</p>
            
            <!-- QUICK ACCESS BUTTONS -->
            <div class="flex gap-4 justify-center mt-8">
                <a href="events/browse.php" class="bg-gradient-to-r from-rose-500 to-pink-600 px-8 py-3 rounded-full font-bold text-lg hover:scale-105 transition">
                    📅 Événements
                </a>
                <a href="stream/streams.php" class="bg-gradient-to-r from-purple-500 to-violet-600 px-8 py-3 rounded-full font-bold text-lg hover:scale-105 transition">
                    🎮 Streams
                </a>
            </div>
        </div>

        <!-- GRAPHIQUES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- GRAPHIQUE COURBE - Dons par Stream -->
            <div class="card rounded-3xl p-8 glow border-2 border-cyan-500/50">
                <h3 class="text-2xl font-bold text-cyan-400 mb-6 text-center font-orbitron">📈 DONS PAR STREAM</h3>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="donsChart" width="400" height="300"></canvas>
                </div>
            </div>

            <!-- GRAPHIQUE CERCLE - Répartition des Statuts -->
            <div class="card rounded-3xl p-8 glow border-2 border-purple-500/50">
                <h3 class="text-2xl font-bold text-purple-400 mb-6 text-center font-orbitron">📊 RÉPARTITION DES STATUTS</h3>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="statusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- STATISTIQUES GLOBALES -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="card rounded-2xl p-6 glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Total Streams</p>
                        <p class="text-4xl font-bold text-cyan-400"><?= $totalStreams ?></p>
                    </div>
                    <i data-feather="video" class="text-cyan-400 w-12 h-12"></i>
                </div>
            </div>

            <div class="card rounded-2xl p-6 glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Streams En Cours</p>
                        <p class="text-4xl font-bold text-emerald-400"><?= $streamsEnCours ?></p>
                    </div>
                    <i data-feather="radio" class="text-emerald-400 w-12 h-12"></i>
                </div>
            </div>

            <div class="card rounded-2xl p-6 glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Total Événements</p>
                        <p class="text-4xl font-bold text-purple-400"><?= $totalEvents ?></p>
                    </div>
                    <i data-feather="calendar" class="text-purple-400 w-12 h-12"></i>
                </div>
            </div>

            <div class="card rounded-2xl p-6 glow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">Dons Totaux (DT)</p>
                        <p class="text-4xl font-bold text-yellow-400"><?= number_format($totalDonsStreams, 2) ?></p>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- TABS NAVIGATION -->
        <div class="flex gap-4 mb-8">
            <button onclick="showTab('streams')" id="tab-streams" class="tab-active flex-1 px-8 py-4 rounded-full font-bold text-xl transition hover:scale-105">
                🎮 STREAMS
            </button>
            <button onclick="showTab('events')" id="tab-events" class="tab-inactive flex-1 px-8 py-4 rounded-full font-bold text-xl transition hover:scale-105">
                📅 ÉVÉNEMENTS
            </button>
        </div>

        <!-- STREAMS SECTION -->
        <div id="section-streams" class="section-content">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-cyan-400 font-orbitron">Gestion des Streams</h2>
                <a href="stream/streamadd.php" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-6 py-3 rounded-full font-bold hover:scale-105 transition">
                    ➕ Nouveau Stream
                </a>
            </div>

            <!-- STATS STREAMS -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <p class="text-gray-400 text-sm mb-2">Streams Planifiés</p>
                    <p class="text-3xl font-bold text-blue-400"><?= count(array_filter($streams, fn($s) => $s['statut'] === 'planifie')) ?></p>
                </div>
                <div class="card rounded-2xl p-6">
                    <p class="text-gray-400 text-sm mb-2">Streams En Cours</p>
                    <p class="text-3xl font-bold text-emerald-400"><?= $streamsEnCours ?></p>
                </div>
                <div class="card rounded-2xl p-6">
                    <p class="text-gray-400 text-sm mb-2">Streams Terminés</p>
                    <p class="text-3xl font-bold text-gray-400"><?= count(array_filter($streams, fn($s) => $s['statut'] === 'termine')) ?></p>
                </div>
                <div class="card rounded-2xl p-6">
                    <p class="text-gray-400 text-sm mb-2">Total Vues</p>
                    <p class="text-3xl font-bold text-cyan-400"><?= number_format($totalVues) ?></p>
                </div>
            </div>

            <div class="card rounded-2xl overflow-hidden border-2 border-cyan-500/50">
                <table class="w-full">
                    <thead class="bg-cyan-500/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-cyan-400 font-bold">ID</th>
                            <th class="px-6 py-4 text-left text-cyan-400 font-bold">Titre</th>
                            <th class="px-6 py-4 text-left text-cyan-400 font-bold">Streamer</th>
                            <th class="px-6 py-4 text-left text-cyan-400 font-bold">Plateforme</th>
                            <th class="px-6 py-4 text-left text-cyan-400 font-bold">Date</th>
                            <th class="px-6 py-4 text-left text-cyan-400 font-bold">Statut</th>
                            <th class="px-6 py-4 text-left text-cyan-400 font-bold">Dons (DT)</th>
                            <th class="px-6 py-4 text-center text-cyan-400 font-bold">Statistiques</th>
                            <th class="px-6 py-4 text-center text-cyan-400 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php foreach ($streams as $stream): ?>
                        <tr class="hover:bg-cyan-500/10 transition">
                            <td class="px-6 py-4 text-gray-300">#<?= $stream['id_stream'] ?></td>
                            <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($stream['titre']) ?></td>
                            <td class="px-6 py-4 text-purple-400"><?= htmlspecialchars($stream['streamer_name'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-500/20 text-purple-300">
                                    <?= htmlspecialchars($stream['plateforme']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400"><?= date('d/m/Y H:i', strtotime($stream['date_debut'])) ?></td>
                            <td class="px-6 py-4">
                                <?php
                                $statusColors = [
                                    'planifie' => 'bg-blue-500/20 text-blue-300',
                                    'en_cours' => 'bg-green-500/20 text-green-300',
                                    'termine' => 'bg-gray-500/20 text-gray-300',
                                    'annule' => 'bg-red-500/20 text-red-300'
                                ];
                                $statusLabels = [
                                    'planifie' => 'Planifié',
                                    'en_cours' => 'En_cours',
                                    'termine' => 'Terminé',
                                    'annule' => 'Annulé'
                                ];
                                $colorClass = $statusColors[$stream['statut']] ?? 'bg-gray-500/20 text-gray-300';
                                $label = $statusLabels[$stream['statut']] ?? $stream['statut'];
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $colorClass ?>">
                                    <?= $label ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-yellow-400 font-bold"><?= number_format($stream['don_total'], 2) ?></td>
                            <td class="px-6 py-4 text-center text-sm">
                                <div class="flex justify-center gap-2">
                                    <span title="Vues">👁️ <?= $stream['nb_vues'] ?></span>
                                    <span title="Likes">👍 <?= $stream['nb_likes'] ?></span>
                                    <span title="Dislikes">👎 <?= $stream['nb_dislikes'] ?></span>
                                    <span title="Commentaires">💬 <?= $stream['nb_commentaires'] ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <a href="stream/streamadd.php?edit=<?= $stream['id_stream'] ?>" 
                                       class="p-2 bg-yellow-500/20 rounded-lg hover:bg-yellow-500/40 transition" title="Modifier">
                                        <i data-feather="edit-2" class="w-4 h-4 text-yellow-400"></i>
                                    </a>
                                    <a href="stream/deletestream.php?id=<?= $stream['id_stream'] ?>" 
                                       onclick="return confirm('Confirmer la suppression ?')"
                                       class="p-2 bg-red-500/20 rounded-lg hover:bg-red-500/40 transition" title="Supprimer">
                                        <i data-feather="trash-2" class="w-4 h-4 text-red-400"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EVENTS SECTION -->
        <div id="section-events" class="section-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-purple-400 font-orbitron">Gestion des Événements</h2>
                <a href="events/event_add_edit.php" class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-3 rounded-full font-bold hover:scale-105 transition">
                    ➕ Nouvel Événement
                </a>
            </div>

            <!-- STATS ÉVÉNEMENTS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card rounded-2xl p-6">
                    <p class="text-gray-400 text-sm mb-2">Événements En Cours</p>
                    <p class="text-3xl font-bold text-emerald-400"><?= $eventsEnCours ?></p>
                </div>
                <div class="card rounded-2xl p-6">
                    <p class="text-gray-400 text-sm mb-2">Événements À Venir</p>
                    <p class="text-3xl font-bold text-blue-400"><?= $eventsAVenir ?></p>
                </div>
                <div class="card rounded-2xl p-6">
                    <p class="text-gray-400 text-sm mb-2">Objectif Total (DT)</p>
                    <p class="text-3xl font-bold text-yellow-400"><?= number_format($objectifTotal, 2) ?></p>
                </div>
            </div>

            <div class="card rounded-2xl overflow-hidden border-2 border-purple-500/50">
                <table class="w-full">
                    <thead class="bg-purple-500/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-purple-400 font-bold">ID</th>
                            <th class="px-6 py-4 text-left text-purple-400 font-bold">Titre</th>
                            <th class="px-6 py-4 text-left text-purple-400 font-bold">Thème</th>
                            <th class="px-6 py-4 text-left text-purple-400 font-bold">Date Début</th>
                            <th class="px-6 py-4 text-left text-purple-400 font-bold">Date Fin</th>
                            <th class="px-6 py-4 text-left text-purple-400 font-bold">Statut</th>
                            <th class="px-6 py-4 text-left text-purple-400 font-bold">Objectif (DT)</th>
                            <th class="px-6 py-4 text-center text-purple-400 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        <?php foreach ($events as $event): ?>
                        <?php
                        $debut = new DateTime($event['date_debut']);
                        $fin = new DateTime($event['date_fin']);
                        if ($debut <= $now && $now <= $fin) {
                            $status = '<span class="px-3 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-300">🔴 En cours</span>';
                        } elseif ($debut > $now) {
                            $status = '<span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300">📅 À venir</span>';
                        } else {
                            $status = '<span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-500/20 text-gray-300">✅ Terminé</span>';
                        }
                        ?>
                        <tr class="hover:bg-purple-500/10 transition">
                            <td class="px-6 py-4 text-gray-300">#<?= $event['id_evenement'] ?></td>
                            <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($event['titre']) ?></td>
                            <td class="px-6 py-4 text-cyan-400"><?= htmlspecialchars($event['theme']) ?></td>
                            <td class="px-6 py-4 text-sm"><?= $debut->format('d/m/Y') ?></td>
                            <td class="px-6 py-4 text-sm"><?= $fin->format('d/m/Y') ?></td>
                            <td class="px-6 py-4"><?= $status ?></td>
                            <td class="px-6 py-4 text-yellow-400 font-bold"><?= number_format($event['objectif'], 2) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <a href="events/event_add_edit.php?edit=<?= $event['id_evenement'] ?>" 
                                       class="p-2 bg-yellow-500/20 rounded-lg hover:bg-yellow-500/40 transition" title="Modifier">
                                        <i data-feather="edit-2" class="w-4 h-4 text-yellow-400"></i>
                                    </a>
                                    <a href="events/event_actions.php?delete=<?= $event['id_evenement'] ?>" 
                                       onclick="return confirm('Confirmer la suppression ?')"
                                       class="p-2 bg-red-500/20 rounded-lg hover:bg-red-500/40 transition" title="Supprimer">
                                        <i data-feather="trash-2" class="w-4 h-4 text-red-400"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/80 border-t border-cyan-500/30 py-8 mt-16">
        <div class="container mx-auto px-6 text-center">
            <p class="text-gray-400">&copy; 2025 Play2Help • Plateforme de Streaming Solidaire</p>
        </div>
    </footer>

    <script>
        // Initialize feather icons
        feather.replace({ width: 16, height: 16 });

        // Global function for tab switching (must be outside DOMContentLoaded)
        function showTab(tab) {
            console.log('showTab called with:', tab);
            
            const tabs = ['streams', 'events'];
            tabs.forEach(t => {
                const section = document.getElementById('section-' + t);
                const button = document.getElementById('tab-' + t);
                
                console.log('Processing tab:', t);
                console.log('Section found:', section);
                console.log('Button found:', button);
                
                if (!section || !button) {
                    console.error('Missing elements for tab:', t);
                    return;
                }
                
                if (t === tab) {
                    section.classList.remove('hidden');
                    button.classList.remove('tab-inactive');
                    button.classList.add('tab-active');
                    console.log('Activated tab:', t);
                } else {
                    section.classList.add('hidden');
                    button.classList.remove('tab-active');
                    button.classList.add('tab-inactive');
                    console.log('Deactivated tab:', t);
                }
            });
        }

        // Test function accessibility
        console.log('showTab function defined:', typeof showTab);

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing charts...');
            console.log('showTab function accessible in DOMContentLoaded:', typeof showTab);
            
            // Données PHP pour les graphiques
            <?php
            // Préparation des données pour le graphique des dons
            $streamLabels = [];
            $streamDons = [];
            if (is_array($streams)) {
                error_log("Dashboard - Processing " . count($streams) . " streams");
                foreach ($streams as $index => $stream) {
                    $titre = $stream['titre'] ?? '';
                    $donTotal = $stream['don_total'] ?? 0;
                    
                    error_log("Dashboard - Stream $index: titre='$titre', don_total='$donTotal'");
                    
                    if (!empty($titre)) {
                        $streamLabels[] = substr($titre, 0, 20) . (strlen($titre) > 20 ? '...' : '');
                    } else {
                        $streamLabels[] = "Stream #" . ($stream['id_stream'] ?? $index + 1);
                    }
                    $streamDons[] = (float)$donTotal;
                }
            }

            // Comptage des statuts pour le graphique circulaire
            $statusCount = [
                'planifie' => 0,
                'en_cours' => 0,
                'termine' => 0,
                'annule' => 0
            ];
            if (is_array($streams)) {
                foreach ($streams as $stream) {
                    $status = $stream['statut'] ?? 'planifie';
                    if (isset($statusCount[$status])) {
                        $statusCount[$status]++;
                    }
                }
            }
            
            // Ensure we have valid JSON
            $streamLabelsJson = json_encode($streamLabels, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            $streamDonsJson = json_encode($streamDons, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            
            if ($streamLabelsJson === false) $streamLabelsJson = '[]';
            if ($streamDonsJson === false) $streamDonsJson = '[]';
            ?>

            // Debug data
            const streamLabels = <?= $streamLabelsJson ?>;
            const streamDons = <?= $streamDonsJson ?>;
            const statusData = [
                <?= (int)$statusCount['planifie'] ?>,
                <?= (int)$statusCount['en_cours'] ?>,
                <?= (int)$statusCount['termine'] ?>,
                <?= (int)$statusCount['annule'] ?>
            ];
            
            console.log('Stream labels:', streamLabels);
            console.log('Stream dons:', streamDons);
            console.log('Status data:', statusData);

            // Fallback data if no streams exist
            const hasStreamData = streamDons.length > 0; // On se base sur les dons, pas les labels
            const hasStatusData = statusData.some(val => val > 0);
            
            console.log('hasStreamData:', hasStreamData, 'streamLabels.length:', streamLabels.length, 'streamDons.length:', streamDons.length);
            
            // Si on a des dons mais pas de labels, créer des labels par défaut
            if (streamDons.length > 0 && streamLabels.length === 0) {
                console.log('Creating default labels for streams');
                for (let i = 0; i < streamDons.length; i++) {
                    streamLabels.push('Stream #' + (i + 1));
                }
            }
            
            if (!hasStreamData) {
                console.warn('No stream data available, using fallback');
            }
            if (!hasStatusData) {
                console.warn('No status data available, using fallback');
            }

            // Check if Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded!');
                return;
            }

            // GRAPHIQUE COURBE - Dons par Stream
            try {
                const donsCanvas = document.getElementById('donsChart');
                if (!donsCanvas) {
                    console.error('Canvas donsChart not found!');
                    return;
                }
                
                const donsCtx = donsCanvas.getContext('2d');
                const donsChart = new Chart(donsCtx, {
                    type: 'line',
                    data: {
                        labels: hasStreamData ? streamLabels : ['Aucun stream'],
                        datasets: [{
                            label: 'Dons (DT)',
                            data: hasStreamData ? streamDons : [0],
                            borderColor: '#22d3ee',
                            backgroundColor: 'rgba(34, 211, 238, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#22d3ee',
                            pointBorderColor: '#0f172a',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#22d3ee',
                                    font: { size: 14, family: 'Space Mono' }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { color: '#94a3b8' },
                                grid: { color: 'rgba(34, 211, 238, 0.1)' }
                            },
                            x: {
                                ticks: { 
                                    color: '#94a3b8',
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: { color: 'rgba(34, 211, 238, 0.1)' }
                            }
                        }
                    }
                });
                console.log('Dons chart created successfully');
            } catch (error) {
                console.error('Error creating dons chart:', error);
            }

            // GRAPHIQUE CERCLE - Répartition des Statuts
            try {
                const statusCanvas = document.getElementById('statusChart');
                if (!statusCanvas) {
                    console.error('Canvas statusChart not found!');
                    return;
                }
                
                const statusCtx = statusCanvas.getContext('2d');
                const statusChart = new Chart(statusCtx, {
                    type: 'pie',
                    data: {
                        labels: hasStatusData ? ['📅 Planifié', '🔴 En cours', '✅ Terminé', '❌ Annulé'] : ['Aucune donnée'],
                        datasets: [{
                            data: hasStatusData ? statusData : [1],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(34, 197, 94, 0.8)',
                                'rgba(156, 163, 175, 0.8)',
                                'rgba(239, 68, 68, 0.8)'
                            ],
                            borderColor: [
                                'rgba(59, 130, 246, 1)',
                                'rgba(34, 197, 94, 1)',
                                'rgba(156, 163, 175, 1)',
                                'rgba(239, 68, 68, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#a855f7',
                                    font: { size: 14, family: 'Space Mono' },
                                    padding: 15
                                }
                            }
                        }
                    }
                });
                console.log('Status chart created successfully');
            } catch (error) {
                console.error('Error creating status chart:', error);
            }
        });
    </script>
</body>
</html>

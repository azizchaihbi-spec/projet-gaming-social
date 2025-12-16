<?php
require_once __DIR__ . '/../../../controllers/EventController.php';

session_start();

$controller = new EventController();
$events = $controller->listEvents();
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Gestion Événements</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Mono', monospace; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
        .card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(34, 211, 238, 0.3); }
        .neon { text-shadow: 0 0 20px #22d3ee, 0 0 40px #22d3ee; }
        .glow:hover { box-shadow: 0 0 30px rgba(34, 211, 238, 0.6); }
        .scanline { position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, transparent, #22d3ee, transparent); animation: scan 6s linear infinite; }
        @keyframes scan { 0% { transform: translateY(-100%); } 100% { transform: translateY(100vh); } }
    </style>
</head>
<body class="relative min-h-screen overflow-x-hidden">

    <div class="scanline"></div>

    <!-- Header commun -->
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="container mx-auto px-6 py-4">
            <div class="bg-green-500/20 border border-green-500/30 rounded-lg p-4">
                <span class="text-green-400">✅ Événement enregistré avec succès!</span>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="container mx-auto px-6 py-4">
            <div class="bg-orange-500/20 border border-orange-500/30 rounded-lg p-4">
                <span class="text-orange-400">🗑️ Événement supprimé</span>
            </div>
        </div>
    <?php endif; ?>

    <main class="container mx-auto px-6 py-12 max-w-7xl relative z-10">

        <!-- TITRE -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">GESTION ÉVÉNEMENTS</h1>
            <p class="text-cyan-400 text-xl mt-4">Événements Caritatifs • <?= count($events) ?> événement(s)</p>
        </div>

        <!-- STATS CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
            <?php
            $totalEvents = count($events);
            $now = new DateTime();
            $activeEvents = array_filter($events, function($e) use ($now) {
                $debut = new DateTime($e['date_debut']);
                $fin = new DateTime($e['date_fin']);
                return $debut <= $now && $now <= $fin;
            });
            $totalObjectif = array_sum(array_column($events, 'objectif'));
            $upcomingEvents = array_filter($events, function($e) use ($now) {
                return new DateTime($e['date_debut']) > $now;
            });
            ?>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-cyan-400 neon"><?= $totalEvents ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Total Événements</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-emerald-400"><?= count($activeEvents) ?></h3>
                <p class="text-gray-300 mt-3 text-lg">En Cours</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-purple-400"><?= count($upcomingEvents) ?></h3>
                <p class="text-gray-300 mt-3 text-lg">À Venir</p>
            </div>
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-4xl font-bold text-yellow-400"><?= number_format($totalObjectif, 2) ?> DT</h3>
                <p class="text-gray-300 mt-3 text-lg">Objectif Total</p>
            </div>
        </div>

        <!-- TABLEAU -->
        <div class="card rounded-3xl p-10 glow border-4 border-cyan-500/50">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <h2 class="text-4xl font-bold neon font-orbitron">Liste des Événements</h2>
                <a href="event_add_edit.php?add=1" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                    + Nouvel Événement
                </a>
            </div>

            <!-- FILTRES -->
            <div class="bg-gray-800/50 rounded-xl p-6 mb-8 border border-cyan-500/20">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="text-cyan-400 font-semibold text-sm mb-2 block">Thème</label>
                        <input type="text" id="filterTheme" placeholder="Rechercher un thème..." class="w-full bg-gray-700 border border-cyan-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>
                    <div>
                        <label class="text-cyan-400 font-semibold text-sm mb-2 block">Période</label>
                        <select id="filterPeriode" class="w-full bg-gray-700 border border-cyan-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="">Toutes les périodes</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="7days">7 derniers jours</option>
                            <option value="30days">30 derniers jours</option>
                            <option value="upcoming">À venir</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-cyan-400 font-semibold text-sm mb-2 block">Statut</label>
                        <select id="filterStatut" class="w-full bg-gray-700 border border-cyan-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500 transition">
                            <option value="">Tous les statuts</option>
                            <option value="en_cours">🔴 En cours</option>
                            <option value="a_venir">📅 À venir</option>
                            <option value="termine">✅ Terminé</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-cyan-400 font-semibold text-sm mb-2 block">Min Objectif (DT)</label>
                        <input type="number" id="filterObjectifMin" placeholder="0" min="0" step="0.01" class="w-full bg-gray-700 border border-cyan-500/30 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-cyan-500 transition">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button onclick="resetEventFilters()" class="bg-gradient-to-r from-pink-500 to-red-500 px-6 py-2 rounded-lg font-bold hover:scale-105 transition">
                        ✕ Réinitialiser
                    </button>
                    <button onclick="filterEvents()" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-6 py-2 rounded-lg font-bold hover:scale-105 transition">
                        🔍 Filtrer
                    </button>
                </div>
            </div>

            <div class="flex justify-between items-center mb-4">
                <div class="text-gray-400 text-sm">
                    Affichage <span id="currentPageInfo">1-10</span> sur <span id="totalEvents"><?= count($events) ?></span>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-cyan-400 text-sm">Éléments par page:</label>
                    <select id="itemsPerPage" class="bg-gray-700 border border-cyan-500/30 rounded px-3 py-1 text-white text-sm">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="all">Tous</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto border-2 border-cyan-500/30 rounded-xl bg-gray-800/80">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-cyan-500 text-cyan-400 bg-gray-800/50">
                            <th class="py-5 px-8">ID</th>
                            <th class="py-5 px-8">Titre</th>
                            <th class="py-5 px-8">Thème</th>
                            <th class="py-5 px-8">Période</th>
                            <th class="py-5 px-8">Statut</th>
                            <th class="py-5 px-8">Objectif</th>
                            <th class="py-5 px-8 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="eventTableBody">
                        <?php if (!empty($events)): ?>
                            <?php foreach ($events as $event): ?>
                            <?php
                            $debut = new DateTime($event['date_debut']);
                            $fin = new DateTime($event['date_fin']);
                            $now = new DateTime();
                            if ($debut <= $now && $now <= $fin) {
                                $statusClass = 'bg-green-500/20 text-green-400 border-green-500/30';
                                $statusLabel = '🔴 En cours';
                            } elseif ($debut > $now) {
                                $statusClass = 'bg-blue-500/20 text-blue-400 border-blue-500/30';
                                $statusLabel = '📅 À venir';
                            } else {
                                $statusClass = 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                                $statusLabel = '✅ Terminé';
                            }
                            ?>
                            <tr class="event-row border-b border-gray-700 hover:bg-gray-800/80 transition" 
                                data-theme="<?= strtolower(htmlspecialchars($event['theme'] ?? '')) ?>"
                                data-debut="<?= date('Y-m-d', strtotime($event['date_debut'])) ?>"
                                data-fin="<?= date('Y-m-d', strtotime($event['date_fin'])) ?>"
                                data-statut="<?= ($debut <= $now && $now <= $fin) ? 'en_cours' : (($debut > $now) ? 'a_venir' : 'termine') ?>"
                                data-objectif="<?= (float)($event['objectif'] ?? 0) ?>">
                                <td class="py-6 px-8 font-mono text-cyan-400">#<?= (int)$event['id_evenement'] ?></td>
                                <td class="py-6 px-8">
                                    <div class="font-medium text-white"><?= htmlspecialchars($event['titre']) ?></div>
                                </td>
                                <td class="py-6 px-8 text-gray-300">
                                    <?= htmlspecialchars($event['theme'] ?? '-') ?>
                                </td>
                                <td class="py-6 px-8 text-gray-400">
                                    <div><?= date('d/m/Y', strtotime($event['date_debut'])) ?></div>
                                    <div class="text-sm">→ <?= date('d/m/Y', strtotime($event['date_fin'])) ?></div>
                                </td>
                                <td class="py-6 px-8">
                                    <span class="px-3 py-1 rounded-full text-sm font-medium border <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="py-6 px-8 text-emerald-400 font-bold">
                                    <?= number_format((float)($event['objectif'] ?? 0), 2) ?> DT
                                </td>
                                <td class="py-6 px-8 text-center">
                                    <div class="flex justify-center gap-4">
                                        <a href="event_add_edit.php?edit=<?= (int)$event['id_evenement'] ?>" 
                                           class="text-yellow-400 hover:text-yellow-300 transition transform hover:scale-110" 
                                           title="Modifier">
                                            <i data-feather="edit-2" class="w-5 h-5"></i>
                                        </a>
                                        <a href="event_actions.php?delete=<?= (int)$event['id_evenement'] ?>" 
                                           onclick="return confirm('Supprimer cet événement ?')"
                                           class="text-red-500 hover:text-red-400 transition transform hover:scale-110" 
                                           title="Supprimer">
                                            <i data-feather="trash-2" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    <i data-feather="calendar" class="w-12 h-12 mx-auto mb-4"></i>
                                    <p>Aucun événement trouvé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div id="paginationContainer" class="flex justify-center items-center gap-3 mt-8">
                <!-- Buttons will be inserted by JS -->
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
        feather.replace({ width: 20, height: 20 });

        // All events data
        const allEvents = <?= json_encode($events) ?>;
        let currentPage = 1;
        let itemsPerPage = 10;
        let filteredEvents = [...allEvents];

        function renderEvents() {
            const tbody = document.getElementById('eventTableBody');
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = itemsPerPage === 'all' ? filteredEvents.length : startIndex + itemsPerPage;
            const eventsToShow = filteredEvents.slice(startIndex, endIndex);

            tbody.innerHTML = '';

            if (eventsToShow.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <i data-feather="calendar" class="w-12 h-12 mx-auto mb-4"></i>
                            <p>Aucun événement trouvé</p>
                        </td>
                    </tr>
                `;
                feather.replace();
                return;
            }

            eventsToShow.forEach(event => {
                const debut = new Date(event.date_debut);
                const fin = new Date(event.date_fin);
                const now = new Date();
                
                let statusClass, statusLabel;
                if (debut <= now && now <= fin) {
                    statusClass = 'bg-green-500/20 text-green-400 border-green-500/30';
                    statusLabel = '🔴 En cours';
                } else if (debut > now) {
                    statusClass = 'bg-blue-500/20 text-blue-400 border-blue-500/30';
                    statusLabel = '📅 À venir';
                } else {
                    statusClass = 'bg-gray-500/20 text-gray-400 border-gray-500/30';
                    statusLabel = '✅ Terminé';
                }

                tbody.innerHTML += `
                    <tr class="event-row border-b border-gray-700 hover:bg-gray-800/80 transition">
                        <td class="py-6 px-8 font-mono text-cyan-400">#${event.id_evenement}</td>
                        <td class="py-6 px-8">
                            <div class="font-medium text-white">${event.titre || ''}</div>
                        </td>
                        <td class="py-6 px-8 text-gray-300">
                            ${event.theme || '-'}
                        </td>
                        <td class="py-6 px-8 text-gray-400">
                            <div>${new Date(event.date_debut).toLocaleDateString('fr-FR')}</div>
                            <div class="text-sm">→ ${new Date(event.date_fin).toLocaleDateString('fr-FR')}</div>
                        </td>
                        <td class="py-6 px-8">
                            <span class="px-3 py-1 rounded-full text-sm font-medium border ${statusClass}">
                                ${statusLabel}
                            </span>
                        </td>
                        <td class="py-6 px-8 text-emerald-400 font-bold">
                            ${parseFloat(event.objectif || 0).toFixed(2)} DT
                        </td>
                        <td class="py-6 px-8 text-center">
                            <div class="flex justify-center gap-4">
                                <a href="event_add_edit.php?edit=${event.id_evenement}" 
                                   class="text-yellow-400 hover:text-yellow-300 transition transform hover:scale-110" 
                                   title="Modifier">
                                    <i data-feather="edit-2" class="w-5 h-5"></i>
                                </a>
                                <a href="event_actions.php?delete=${event.id_evenement}" 
                                   onclick="return confirm('Supprimer cet événement ?')"
                                   class="text-red-500 hover:text-red-400 transition transform hover:scale-110" 
                                   title="Supprimer">
                                    <i data-feather="trash-2" class="w-5 h-5"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });

            feather.replace();
            updatePagination();
            updatePageInfo();
        }

        function updatePagination() {
            const container = document.getElementById('paginationContainer');
            const totalPages = itemsPerPage === 'all' ? 1 : Math.ceil(filteredEvents.length / itemsPerPage);
            
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';

            // Previous button
            html += `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} 
                class="px-4 py-2 rounded-lg font-bold transition ${currentPage === 1 ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gradient-to-r from-cyan-500 to-emerald-500 hover:scale-105'}">
                ← Précédent
            </button>`;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    html += `<button onclick="changePage(${i})" 
                        class="px-4 py-2 rounded-lg font-bold transition ${i === currentPage ? 'bg-gradient-to-r from-purple-500 to-pink-500' : 'bg-gray-700 hover:bg-gray-600'}">
                        ${i}
                    </button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += `<span class="px-2 text-gray-500">...</span>`;
                }
            }

            // Next button
            html += `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} 
                class="px-4 py-2 rounded-lg font-bold transition ${currentPage === totalPages ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-gradient-to-r from-cyan-500 to-emerald-500 hover:scale-105'}">
                Suivant →
            </button>`;

            container.innerHTML = html;
        }

        function updatePageInfo() {
            const startIndex = (currentPage - 1) * itemsPerPage + 1;
            const endIndex = itemsPerPage === 'all' ? filteredEvents.length : Math.min(currentPage * itemsPerPage, filteredEvents.length);
            document.getElementById('currentPageInfo').textContent = `${startIndex}-${endIndex}`;
            document.getElementById('totalEvents').textContent = filteredEvents.length;
        }

        function changePage(page) {
            const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderEvents();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.getElementById('itemsPerPage').addEventListener('change', function() {
            itemsPerPage = this.value === 'all' ? 'all' : parseInt(this.value);
            currentPage = 1;
            renderEvents();
        });

        // Event Filtering Functions
        function filterEvents() {
            const theme = document.getElementById('filterTheme').value.toLowerCase().trim();
            const periode = document.getElementById('filterPeriode').value;
            const statut = document.getElementById('filterStatut').value;
            const objectifMin = parseFloat(document.getElementById('filterObjectifMin').value) || 0;
            const now = new Date();
            now.setHours(0, 0, 0, 0);

            filteredEvents = allEvents.filter(event => {
                const eventTheme = (event.theme || '').toLowerCase();
                const debut = new Date(event.date_debut);
                const fin = new Date(event.date_fin);
                const eventObjectif = parseFloat(event.objectif || 0);

                // Determine status
                let eventStatut;
                if (debut <= now && now <= fin) {
                    eventStatut = 'en_cours';
                } else if (debut > now) {
                    eventStatut = 'a_venir';
                } else {
                    eventStatut = 'termine';
                }

                // Theme filter (case-insensitive substring match)
                if (theme && !eventTheme.includes(theme)) return false;

                // Period filter
                if (periode) {
                    let periodStart = new Date(now);
                    let periodEnd = new Date(now);

                    if (periode === 'today') {
                        periodStart.setHours(0, 0, 0, 0);
                        periodEnd.setHours(23, 59, 59, 999);
                    } else if (periode === '7days') {
                        periodEnd.setDate(periodEnd.getDate() + 7);
                    } else if (periode === '30days') {
                        periodEnd.setDate(periodEnd.getDate() + 30);
                    } else if (periode === 'upcoming') {
                        periodStart = debut;
                    }

                    // Check if event overlaps with period
                    if (!(debut <= periodEnd && fin >= periodStart)) return false;
                }

                // Status filter
                if (statut && eventStatut !== statut) return false;

                // Objective minimum filter
                if (objectifMin > 0 && eventObjectif < objectifMin) return false;

                return true;
            });

            currentPage = 1;
            renderEvents();
        }

        function resetEventFilters() {
            document.getElementById('filterTheme').value = '';
            document.getElementById('filterPeriode').value = '';
            document.getElementById('filterStatut').value = '';
            document.getElementById('filterObjectifMin').value = '';

            filteredEvents = [...allEvents];
            currentPage = 1;
            renderEvents();
        }

        // Add event listeners to filters
        document.getElementById('filterTheme')?.addEventListener('keyup', filterEvents);
        document.getElementById('filterPeriode')?.addEventListener('change', filterEvents);
        document.getElementById('filterStatut')?.addEventListener('change', filterEvents);
        document.getElementById('filterObjectifMin')?.addEventListener('input', filterEvents);

        // Initial render
        renderEvents();
    </script>
</body>
</html>

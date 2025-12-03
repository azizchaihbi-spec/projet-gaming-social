<?php
require_once '../../config/db.php';
require_once '../../controllers/DonController.php';
require_once '../../controllers/ChallengeController.php';

// ---- DON DATA ---- //
$donC = new DonController();
$dons = $donC->list();

// Stats générales
$totalCollecte = array_sum(array_column($dons, 'montant'));
$nombreDons    = count($dons);
$associations  = count(array_unique(array_column($dons, 'id_association')));

// Répartition par association (doughnut)
$statsAssoc = [];
foreach ($dons as $d) {
    $nom = $d['association_nom'] ?? 'Autre';
    $statsAssoc[$nom] = ($statsAssoc[$nom] ?? 0) + $d['montant'];
}

// Évolution mensuelle (line chart)
$monthly = array_fill(0, 12, 0);
foreach ($dons as $d) {
    $mois = date('n', strtotime($d['date_don'])) - 1;
    $monthly[$mois] += $d['montant'];
}

// ---- CHALLENGE DATA ---- //
$challengeC = new ChallengeController();
$challenges = $challengeC->list();
?>

<!DOCTYPE html>
<html lang="fr" class="bg-gray-950 text-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play2Help • Backoffice</title>
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
        
        /* Styles pour l'édition inline */
        .editable-cell {
            position: relative;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .editable-cell:hover {
            background: rgba(167, 139, 250, 0.1);
            border: 1px solid rgba(167, 139, 250, 0.3);
        }
        .edit-input {
            background: rgba(30, 41, 59, 0.9);
            border: 2px solid #a78bfa;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            width: 120px;
            font-size: 16px;
            outline: none;
        }
        .edit-input:focus {
            box-shadow: 0 0 20px rgba(167, 139, 250, 0.5);
        }
        .save-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }
        .save-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
        }
        .cancel-btn {
            background: rgba(239, 68, 68, 0.8);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            border: none;
        }
        
        /* Navigation Buttons */
        .nav-btn {
            background: linear-gradient(135deg, rgba(167, 139, 250, 0.2), rgba(34, 211, 238, 0.2));
            border: 2px solid rgba(167, 139, 250, 0.5);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .nav-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(167, 139, 250, 0.6);
            border-color: #a78bfa;
        }
    </style>
</head>

<body class="relative min-h-screen overflow-x-hidden">

    <div class="scanline"></div>

    <main class="container mx-auto px-6 py-12 max-w-7xl">

        <!-- TITRE FUTURISTE -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-8xl font-bold font-orbitron neon animate-pulse">PLAY2HELP</h1>
            <p class="text-cyan-400 text-xl mt-4">⚡ Live Dashboard</p>
        </div>

        <!-- NAVIGATION VERS NOUVELLES PAGES -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12 max-w-4xl mx-auto">
            
            <!-- Analytics Challenges -->
            <a href="challenge_analytics.php" class="nav-btn rounded-2xl p-8 text-center block group">
                <div class="text-6xl mb-4 group-hover:scale-110 transition-transform">📊</div>
                <h3 class="text-2xl font-bold text-purple-300 mb-2">Analytics Challenges</h3>
                <p class="text-gray-400">Explorez les performances par association</p>
                <div class="mt-4 text-cyan-400 font-semibold">
                    Voir les stats <i data-feather="arrow-right" class="inline w-4 h-4 ml-1"></i>
                </div>
            </a>

            <!-- Retour Front -->
            <a href="../frontoffice/don.php" class="nav-btn rounded-2xl p-8 text-center block group">
                <div class="text-6xl mb-4 group-hover:scale-110 transition-transform">🎮</div>
                <h3 class="text-2xl font-bold text-emerald-300 mb-2">Espace Public</h3>
                <p class="text-gray-400">Retour à l'interface utilisateur</p>
                <div class="mt-4 text-cyan-400 font-semibold">
                    Accéder <i data-feather="arrow-right" class="inline w-4 h-4 ml-1"></i>
                </div>
            </a>

        </div>

        <!-- 4 CARTES DE STATS -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-16">
            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-cyan-400 neon"><?= number_format($totalCollecte, 0, '', ' ') ?> €</h3>
                <p class="text-gray-300 mt-3 text-lg">Total collecté</p>
            </div>

            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-emerald-400"><?= $nombreDons ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Dons reçus</p>
            </div>

            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-purple-400"><?= $associations ?></h3>
                <p class="text-gray-300 mt-3 text-lg">Associations</p>
            </div>

            <div class="card p-8 rounded-2xl text-center glow transition transform hover:scale-105">
                <h3 class="text-5xl font-bold text-yellow-400 animate-pulse">LIVE</h3>
                <p class="text-gray-300 mt-3 text-lg">En direct</p>
            </div>
        </div>

        <!-- GRAPHIQUES -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-16">
            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Répartition des dons</h2>
                <canvas id="donutChart"></canvas>
            </div>

            <div class="card p-10 rounded-3xl glow">
                <h2 class="text-3xl font-bold text-center mb-8 neon font-orbitron">Progression 2025</h2>
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <!-- TABLEAU DES DONS -->
        <div class="card rounded-3xl p-10 glow mb-20">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <h2 class="text-4xl font-bold neon font-orbitron">Tous les dons • <?= $nombreDons ?></h2>
                <a href="../frontoffice/don.php" class="bg-gradient-to-r from-cyan-500 to-emerald-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                    ➕ Nouveau don
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-cyan-500 text-cyan-400">
                            <th class="py-4 px-6">Date</th>
                            <th class="py-4 px-6">Donateur</th>
                            <th class="py-4 px-6">Montant</th>
                            <th class="py-4 px-6">Association</th>
                            <th class="py-4 px-6 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($dons as $d): ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50 transition">
                            <td class="py-5 px-6"><?= date('d/m/Y', strtotime($d['date_don'])) ?></td>
                            <td class="py-5 px-6 font-medium">
                                <?= htmlspecialchars(($d['prenom']??'') . ' ' . ($d['nom']??'')) ?>
                            </td>
                            <td class="py-5 px-6 text-2xl font-bold text-emerald-400">
                                <?= number_format($d['montant'], 2) ?> €
                            </td>
                            <td class="py-5 px-6 text-cyan-400 font-medium">
                                <?= htmlspecialchars($d['association_nom'] ?? '—') ?>
                            </td>
                            <td class="py-5 px-6 text-center">
                                <div class="flex justify-center gap-6">
                                    <a href="edit.php?id=<?= $d['id_don'] ?>" class="text-yellow-400 hover:text-yellow-300">
                                        <i data-feather="edit-2"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $d['id_don'] ?>" onclick="return confirm('Supprimer ce don ?')" class="text-red-500 hover:text-red-400">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

        <!-- TABLEAU DES CHALLENGES AVEC ÉDITION INLINE -->
        <div class="card rounded-3xl p-10 glow mt-20">
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
                <h2 class="text-4xl font-bold neon font-orbitron">Challenges • <?= count($challenges) ?></h2>
                <a href="../frontoffice/don.php#modalChallenge" class="bg-gradient-to-r from-purple-500 to-cyan-500 px-8 py-4 rounded-full text-xl font-bold hover:scale-110 transition">
                    ➕ Nouveau Challenge
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-purple-500 text-purple-300">
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Nom du Challenge</th>
                            <th class="py-4 px-6">Association</th>
                            <th class="py-4 px-6">Objectif (€)</th>
                            <th class="py-4 px-6">Progression (€)</th>
                            <th class="py-4 px-6">Pourcentage</th>
                            <th class="py-4 px-6">Récompense</th>
                            <th class="py-4 px-6 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="challengesTableBody">
                        <?php foreach ($challenges as $c): ?>
                        <?php 
                            $objectif = floatval($c['objectif'] ?? 0);
                            $progression = floatval($c['progression'] ?? 0);
                            $pourcentage = $objectif > 0 ? min(100, round(($progression / $objectif) * 100, 2)) : 0;
                        ?>
                        <tr class="border-b border-gray-800 hover:bg-gray-900/50 transition" data-challenge-id="<?= $c['id_challenge'] ?>">
                            
                            <!-- ID -->
                            <td class="py-5 px-6 text-gray-400">
                                #<?= htmlspecialchars($c['id_challenge']) ?>
                            </td>

                            <!-- Nom du Challenge -->
                            <td class="py-5 px-6 text-cyan-300 font-medium">
                                <?= htmlspecialchars($c['name']) ?>
                            </td>

                            <!-- Association -->
                            <td class="py-5 px-6 text-purple-400">
                                <?= htmlspecialchars($c['association_nom'] ?? 'Non définie') ?>
                            </td>

                            <!-- Objectif (Non éditable) -->
                            <td class="py-5 px-6 text-emerald-400 font-bold">
                                <?= number_format($objectif, 2) ?> €
                            </td>

                            <!-- Progression (Éditable) -->
                            <td class="py-5 px-6">
                                <div class="editable-cell text-yellow-400 font-bold" 
                                     data-field="progression" 
                                     data-value="<?= $progression ?>"
                                     title="Cliquez pour modifier">
                                    <span class="display-value"><?= number_format($progression, 2) ?> €</span>
                                </div>
                            </td>

                            <!-- Pourcentage avec barre de progression -->
                            <td class="py-5 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-24 bg-gray-700 rounded-full h-2.5">
                                        <div class="progress-bar bg-gradient-to-r from-purple-500 to-cyan-500 h-2.5 rounded-full transition-all duration-500" 
                                             style="width: <?= $pourcentage ?>%"></div>
                                    </div>
                                    <span class="percentage-display text-sm font-medium"><?= $pourcentage ?>%</span>
                                </div>
                            </td>

                            <!-- Récompense -->
                            <td class="py-5 px-6 text-gray-300">
                                <?= htmlspecialchars($c['recompense']) ?>
                            </td>

                            <!-- Actions -->
                            <td class="py-5 px-6 text-center">
                                <div class="flex justify-center gap-6">
                                    <a href="../backoffice/editchallenge.php?id=<?= $c['id_challenge'] ?>" 
                                       class="text-yellow-400 hover:text-yellow-300 transition"
                                       title="Modifier">
                                        <i data-feather="edit-2"></i>
                                    </a>

                                    <a href="../backoffice/deletechallenge.php?id=<?= $c['id_challenge'] ?>" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce challenge ?')" 
                                       class="text-red-500 hover:text-red-400 transition"
                                       title="Supprimer">
                                        <i data-feather="trash-2"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if (empty($challenges)): ?>
                        <tr>
                            <td colspan="8" class="py-10 text-center text-gray-400 text-lg">
                                🎮 Aucun challenge pour le moment. Créez-en un !
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="text-center py-8 text-gray-500 border-t border-gray-800 mt-20">
        <p>Copyright © 2025 <span class="text-cyan-400 font-bold">Play to Help</span> - Gaming pour l'Humanitaire</p>
        <p class="text-sm mt-2">Dashboard Admin • Tous droits réservés</p>
    </footer>

    <!-- GRAPHIQUES -->
    <script>
        feather.replace();

        // Doughnut
        new Chart(document.getElementById('donutChart'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_keys($statsAssoc)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($statsAssoc)) ?>,
                    backgroundColor: ['#22d3ee','#10b981','#a78bfa','#f59e0b','#f87171','#06b6d4'],
                    borderColor: '#0f172a',
                    borderWidth: 4,
                    hoverOffset: 20
                }]
            },
            options: { 
                plugins: { 
                    legend: { 
                        labels: { 
                            color: '#e2e8f0',
                            font: {
                                size: 14,
                                family: 'Space Mono'
                            }
                        } 
                    } 
                } 
            }
        });

        // Line Chart
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'],
                datasets: [{
                    label: 'Dons (€)',
                    data: <?= json_encode($monthly) ?>,
                    borderColor: '#22d3ee',
                    backgroundColor: 'rgba(34, 211, 238, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 6,
                    pointBackgroundColor: '#22d3ee',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: { 
                plugins: { 
                    legend: { 
                        display: false 
                    } 
                },
                scales: {
                    y: {
                        ticks: {
                            color: '#e2e8f0',
                            callback: function(value) {
                                return value + ' €';
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#e2e8f0'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // ===== ÉDITION INLINE DE LA PROGRESSION =====
        document.querySelectorAll('.editable-cell').forEach(cell => {
            cell.addEventListener('click', function() {
                if (this.querySelector('.edit-input')) return; // Déjà en édition
                
                const field = this.dataset.field;
                const currentValue = parseFloat(this.dataset.value);
                const displayValue = this.querySelector('.display-value');
                
                // Créer l'input
                const input = document.createElement('input');
                input.type = 'number';
                input.step = '0.01';
                input.min = '0';
                input.value = currentValue;
                input.className = 'edit-input';
                
                // Créer les boutons
                const buttonsDiv = document.createElement('div');
                buttonsDiv.className = 'flex gap-2 mt-2';
                
                const saveBtn = document.createElement('button');
                saveBtn.innerHTML = '✓ Sauver';
                saveBtn.className = 'save-btn';
                
                const cancelBtn = document.createElement('button');
                cancelBtn.innerHTML = '✗ Annuler';
                cancelBtn.className = 'cancel-btn';
                
                buttonsDiv.appendChild(saveBtn);
                buttonsDiv.appendChild(cancelBtn);
                
                // Remplacer le contenu
                displayValue.style.display = 'none';
                this.appendChild(input);
                this.appendChild(buttonsDiv);
                input.focus();
                input.select();
                
                // Fonction pour restaurer l'affichage
                const restore = () => {
                    input.remove();
                    buttonsDiv.remove();
                    displayValue.style.display = 'inline';
                };
                
                // Annuler
                cancelBtn.addEventListener('click', restore);
                
                // Sauvegarder
                saveBtn.addEventListener('click', async () => {
                    const newValue = parseFloat(input.value);
                    
                    if (isNaN(newValue) || newValue < 0) {
                        alert('Valeur invalide! La progression doit être >= 0');
                        return;
                    }
                    
                    const row = cell.closest('tr');
                    const challengeId = row.dataset.challengeId;
                    
                    // Désactiver le bouton
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '⏳ Sauvegarde...';
                    
                    try {
                        const response = await fetch('update_progression.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id=${challengeId}&progression=${newValue}`
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            // Mettre à jour l'affichage
                            cell.dataset.value = newValue;
                            displayValue.textContent = newValue.toFixed(2) + ' €';
                            
                            // Mettre à jour la barre de progression et le pourcentage
                            updateProgressBar(row, newValue, data.objectif);
                            
                            // Restaurer l'affichage
                            restore();
                            
                            // Animation de succès
                            cell.style.background = 'rgba(16, 185, 129, 0.3)';
                            setTimeout(() => {
                                cell.style.background = '';
                            }, 1000);
                        } else {
                            alert('Erreur: ' + data.message);
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = '✓ Sauver';
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                        alert('Erreur réseau. Veuillez réessayer.');
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '✓ Sauver';
                    }
                });
                
                // Sauvegarder avec Enter
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        saveBtn.click();
                    } else if (e.key === 'Escape') {
                        cancelBtn.click();
                    }
                });
            });
        });
        
        // Fonction pour mettre à jour la barre de progression
        function updateProgressBar(row, progression, objectif) {
            const pourcentage = objectif > 0 ? Math.min(100, Math.round((progression / objectif) * 100 * 100) / 100) : 0;
            
            const progressBar = row.querySelector('.progress-bar');
            const percentageDisplay = row.querySelector('.percentage-display');
            
            // Mettre à jour la largeur
            progressBar.style.width = pourcentage + '%';
            percentageDisplay.textContent = pourcentage + '%';
            
            // Changer la couleur selon le pourcentage
            if (pourcentage >= 100) {
                progressBar.className = 'progress-bar bg-gradient-to-r from-green-500 to-emerald-500 h-2.5 rounded-full transition-all duration-500';
            } else if (pourcentage >= 75) {
                progressBar.className = 'progress-bar bg-gradient-to-r from-cyan-500 to-blue-500 h-2.5 rounded-full transition-all duration-500';
            } else if (pourcentage >= 50) {
                progressBar.className = 'progress-bar bg-gradient-to-r from-purple-500 to-cyan-500 h-2.5 rounded-full transition-all duration-500';
            } else {
                progressBar.className = 'progress-bar bg-gradient-to-r from-orange-500 to-yellow-500 h-2.5 rounded-full transition-all duration-500';
            }
        }
    </script>

</body>
</html>
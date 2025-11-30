<?php
require_once '../../config/db.php';
require_once '../../controllers/ChallengeController.php';

$associations = config::getConnexion()->query("SELECT id_association, name FROM association ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Challenge - Backoffice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>.is-invalid { border: 2px solid #ef4444 !important; }</style>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Space+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/challenge.css">
</head>
<body class="bg-gradient-to-br from-purple-900 to-cyan-900 min-h-screen flex items-center justify-center p-6">
<div class="max-w-4xl w-full bg-black/80 backdrop-blur-xl rounded-3xl border-2 border-cyan-500 p-10 shadow-2xl">
    <h1 class="text-5xl font-bold text-center text-cyan-400 mb-10">CRÉER UN CHALLENGE</h1>

    <form id="formChallenge" class="space-y-8">
        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <label class="block text-cyan-300 text-xl mb-3">Association</label>
                <select name="id_association" class="w-full px-6 py-4 bg-gray-900 border border-cyan-600 rounded-xl">
                    <option value="">Choisir...</option>
                    <?php foreach($associations as $a): ?>
                        <option value="<?= $a['id_association'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-cyan-300 text-xl mb-3">Titre du défi</label>
                <input type="text" name="name" class="w-full px-6 py-4 bg-gray-900 border border-cyan-600 rounded-xl" placeholder="50 kills sans mourir">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <label class="block text-cyan-300 text-xl mb-3">Objectif (€)</label>
                <input type="number" step="0.01" name="objectif" class="w-full px-6 py-4 bg-gray-900 border border-cyan-600 rounded-xl" placeholder="300">
            </div>
            <div>
                <label class="block text-cyan-300 text-xl mb-3">Récompense</label>
                <input type="text" name="recompense" class="w-full px-6 py-4 bg-gray-900 border border-cyan-600 rounded-xl" placeholder="Shoutout + skin exclusive">
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="bg-gradient-to-r from-cyan-500 to-purple-600 hover:from-purple-600 hover:to-cyan-500 text-white font-bold text-2xl px-16 py-6 rounded-full transition transform hover:scale-110">
                LANCER LE CHALLENGE
            </button>
        </div>
        <div id="result" class="mt-6 text-center text-2xl"></div>
    </form>
</div>

<script>
document.getElementById('formChallenge').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    const association = form.id_association.value;
    const name = form.name.value.trim();
    const objectif = parseFloat(form.objectif.value);
    const recompense = form.recompense.value.trim();

    if (!association) { form.id_association.classList.add('is-invalid'); form.id_association.focus(); return; }
    if (!name) { form.name.classList.add('is-invalid'); form.name.focus(); return; }
    if (!objectif || objectif <= 0) { form.objectif.classList.add('is-invalid'); form.objectif.focus(); return; }
    if (!recompense) { form.recompense.classList.add('is-invalid'); form.recompense.focus(); return; }

    const btn = form.querySelector('button');
    btn.disabled = true;
    btn.innerHTML = "Création...";

    const formData = new FormData(form);
    formData.append('action', 'add_challenge_backoffice');

    try {
        const res = await fetch('../../../controllers/ChallengeController.php', {
            method: 'POST',
            body: formData
        });
        document.getElementById('result').innerHTML = '<div class="text-green-400">Challenge créé avec succès !</div>';
        form.reset();
        setTimeout(() => location.href = 'index.php', 2000);
    } catch(err) {
        document.getElementById('result').innerHTML = '<div class="text-red-500">Erreur</div>';
    }
    btn.disabled = false;
    btn.innerHTML = "LANCER LE CHALLENGE";
});
</script>
</body>
</html>
<?php
require 'db.php';
header("Content-Type: application/json");

$action = $_GET['action'] ?? '';

switch($action) {

    case 'get_all':
        $search = $_GET['search'] ?? '';
        $sql = "SELECT p.*, f.nom as forum_nom, u.prenom, u.nom as auteur_nom,
                       (SELECT COUNT(*) FROM reponse r WHERE r.id_publication = p.id_publication AND r.supprimee = 0) as nb_reponses
                FROM publication p
                JOIN forum f ON p.id_forum = f.id_forum
                JOIN utilisateur u ON p.id_auteur = u.id_user
                WHERE p.supprimee = 0";
        if ($search) {
            $sql .= " AND (u.prenom LIKE ? OR u.nom LIKE ? OR p.titre LIKE ? OR p.contenu LIKE ?)";
        }
        $sql .= " ORDER BY p.date_publication DESC";

        $stmt = $pdo->prepare($sql);
        if ($search) {
            $like = "%$search%";
            $stmt->execute([$like, $like, $like, $like]);
        } else {
            $stmt->execute();
        }
        $posts = $stmt->fetchAll();

        // Ajouter les réponses
        foreach($posts as &$post) {
            $stmt2 = $pdo->prepare("SELECT r.*, u.prenom, u.nom FROM reponse r JOIN utilisateur u ON r.id_auteur = u.id_user WHERE r.id_publication = ? AND r.supprimee = 0 ORDER BY r.date_reponse");
            $stmt2->execute([$post['id_publication']]);
            $post['answers'] = $stmt2->fetchAll();
        }

        // Stats
        $totalPosts = $pdo->query("SELECT COUNT(*) FROM publication WHERE supprimee = 0")->fetchColumn();
        $totalAnswers = $pdo->query("SELECT COUNT(*) FROM reponse WHERE supprimee = 0")->fetchColumn();
        $bannedCount = $pdo->query("SELECT COUNT(*) FROM banned_users")->fetchColumn();

        echo json_encode([
            "posts" => $posts,
            "stats" => ["totalPosts" => $totalPosts, "totalAnswers" => $totalAnswers, "bannedCount" => $bannedCount]
        ]);
        break;

    case 'delete_post':
        $id = $_POST['id'] ?? 0;
        $pdo->prepare("UPDATE publication SET supprimee = 1 WHERE id_publication = ?")->execute([$id]);
        echo json_encode(["success" => true]);
        break;

    case 'ban_user':
        $username = $_POST['username'] ?? '';
        $pdo->prepare("INSERT IGNORE INTO banned_users (username) VALUES (?)")->execute([$username]);
        echo json_encode(["success" => true]);
        break;

    case 'unban_user':
        $username = $_POST['username'] ?? '';
        $pdo->prepare("DELETE FROM banned_users WHERE username = ?")->execute([$username]);
        echo json_encode(["success" => true]);
        break;

    case 'clear_all':
        if ($_POST['confirm'] === 'OUI_ADMIN_2025') {
            $pdo->exec("DELETE FROM reponse");
            $pdo->exec("DELETE FROM publication");
            $pdo->exec("DELETE FROM banned_users");
            echo json_encode(["success" => true]);
        }
        break;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Add Book</title>
</head>
<body>
    <h1>Ajouter un Livre</h1>
    <form action="Verification.php" method="POST">
        <label>Title :</label>
        <input type="text" name="title" required><br><br>

        <label>Author :</label>
        <input type="text" name="author" required><br><br>

        <label>Publication Date :</label>
        <input type="date" name="publicationDate" required><br><br>

        <label>Language :</label>
        <select name="language">
            <option value="FR">FR</option>
            <option value="AN">AN</option>
        </select><br><br>

        <label>Status :</label>
        <select name="status">
            <option value="Disponible">Disponible</option>
            <option value="Indisponible">Indisponible</option>
        </select><br><br>

        <label>Copies :</label>
        <input type="number" name="copies" min="1" required><br><br>

        <label>Category :</label>
        <select name="category">
            <option value="Science">Science</option>
            <option value="Literature">Literature</option>
            <option value="Technology">Technology</option>
            <option value="History">History</option>
            <option value="Arts">Arts</option>
        </select><br><br>

        <button type="submit">Add Book</button>
    </form>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

// Récupérer les utilisateurs
$query = $pdo->query('SELECT id, nom, prenom, photo_profil FROM utilisateurs');
$users = $query->fetchAll(PDO::FETCH_ASSOC);

// Assurez-vous que la variable $users est un tableau
if ($users === false) {
    $users = []; // Si la requête échoue, initialisez $users comme tableau vide
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utilisateurs</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <h1>Liste des Utilisateurs</h1>
        <a href="add_user.php" class="btn btn-success mb-3">Ajouter un utilisateur</a> <!-- Nouveau bouton pour ajouter un utilisateur -->
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['prenom']) ?></td>
                        <td>
                            <?php if (!empty($user['photo_profil'])): ?>
                                <img src="<?= htmlspecialchars($user['photo_profil']) ?>" alt="Photo de profil" class="img-thumbnail" width="100">
                            <?php else: ?>
                                <p>Pas de photo</p>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_user.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-warning">Modifier</a>
                            <a href="delete_user.php?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-danger">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

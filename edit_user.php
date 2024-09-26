<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirection si l'utilisateur n'est pas connecté
    exit();
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $user_id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $photo_profil = '';
    $password = $_POST['password'];

    // Gestion du téléchargement de l'image
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] == 0) {
        $photo_profil = 'uploads/' . basename($_FILES['photo_profil']['name']);
        move_uploaded_file($_FILES['photo_profil']['tmp_name'], $photo_profil);
    } else {
        // Si aucune nouvelle image n'est téléchargée, conserver l'ancienne image
        $photo_profil = $_POST['existing_photo_profil'];
    }

    // Préparer la requête de mise à jour
    if (!empty($password)) {
        // Si un mot de passe est fourni, le hacher et mettre à jour
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $query = $pdo->prepare('UPDATE utilisateurs SET nom = ?, prenom = ?, photo_profil = ?, password = ? WHERE id = ?');
        $query->bindParam(1, $nom, PDO::PARAM_STR);
        $query->bindParam(2, $prenom, PDO::PARAM_STR);
        $query->bindParam(3, $photo_profil, PDO::PARAM_STR);
        $query->bindParam(4, $password_hash, PDO::PARAM_STR);
        $query->bindParam(5, $user_id, PDO::PARAM_INT);
    } else {
        // Sinon, mettre à jour sans changer le mot de passe
        $query = $pdo->prepare('UPDATE utilisateurs SET nom = ?, prenom = ?, photo_profil = ? WHERE id = ?');
        $query->bindParam(1, $nom, PDO::PARAM_STR);
        $query->bindParam(2, $prenom, PDO::PARAM_STR);
        $query->bindParam(3, $photo_profil, PDO::PARAM_STR);
        $query->bindParam(4, $user_id, PDO::PARAM_INT);
    }

    $query->execute();

    header('Location: users.php'); // Redirection vers la liste des utilisateurs après la mise à jour
    exit();
}

// Récupérer les détails de l'utilisateur à éditer
$user_id = $_GET['id'];
$query = $pdo->prepare('SELECT * FROM utilisateurs WHERE id = ?');
$query->bindParam(1, $user_id, PDO::PARAM_INT);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Utilisateur non trouvé');
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un Utilisateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Modifier l'Utilisateur</h2>
        <form action="edit_user.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
            <input type="hidden" name="existing_photo_profil" value="<?= htmlspecialchars($user['photo_profil']) ?>">
            
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom']) ?>" required>
            </div>
            <div class="mb-3">
    <label for="login" class="form-label">Login</label>
    <input type="text" id="login" name="login" class="form-control" value="<?= htmlspecialchars($user['login']) ?>" required>
</div>

            <div class="mb-3">
                <label for="photo_profil" class="form-label">Photo de Profil</label>
                <input type="file" id="photo_profil" name="photo_profil" class="form-control">
                <?php if (!empty($user['photo_profil'])): ?>
                    <img src="<?= htmlspecialchars($user['photo_profil']) ?>" alt="<?= htmlspecialchars($user['nom']) ?>" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de Passe (laisser vide si vous ne souhaitez pas modifier)</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="users.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

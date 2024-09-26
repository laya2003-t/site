<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirige vers la page de connexion si la session n'est pas active
    exit();
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login']; // Nouveau champ pour le nom d'utilisateur
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $photo_profil = '';
    $password = $_POST['password'];

    // Gérer le téléchargement de l'image
    if (isset($_FILES['photo_profil']) && $_FILES['photo_profil']['error'] == 0) {
        $photo_profil = 'uploads/' . basename($_FILES['photo_profil']['name']);
        move_uploaded_file($_FILES['photo_profil']['tmp_name'], $photo_profil);
    }

    // Hacher le mot de passe
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Préparer et exécuter la requête d'insertion
    $query = $pdo->prepare('INSERT INTO utilisateurs (login, nom, prenom, photo_profil, password) VALUES (:login, :nom, :prenom, :photo_profil, :password)');
    $query->bindParam(':login', $login, PDO::PARAM_STR);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
    $query->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $query->bindParam(':photo_profil', $photo_profil, PDO::PARAM_STR);
    $query->bindParam(':password', $password_hash, PDO::PARAM_STR);
    $query->execute();

    header('Location: users.php'); // Redirige vers la liste des utilisateurs après l'ajout
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un Utilisateur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Ajouter un Utilisateur</h2>
        <form action="add_user.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="login" class="form-label">Nom d'utilisateur</label>
                <input type="text" id="login" name="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" id="login" name="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="photo_profil" class="form-label">Photo de Profil</label>
                <input type="file" id="photo_profil" name="photo_profil" class="form-control">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de Passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="users.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
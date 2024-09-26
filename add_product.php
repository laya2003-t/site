<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $libelle = isset($_POST['libelle']) ? trim($_POST['libelle']) : '';
    $quantite = isset($_POST['quantite']) ? (int) $_POST['quantite'] : 0;
    $image = '';

    // Gestion de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Préparation de la requête d'insertion
    $query = $pdo->prepare('INSERT INTO produits (nom, description, libelle, image, quantite) VALUES (?, ?, ?, ?, ?)');
    $query->bindParam(1, $nom, PDO::PARAM_STR);
    $query->bindParam(2, $description, PDO::PARAM_STR);
    $query->bindParam(3, $libelle, PDO::PARAM_STR);
    $query->bindParam(4, $image, PDO::PARAM_STR);
    $query->bindParam(5, $quantite, PDO::PARAM_INT);

    if ($query->execute()) {
        header('Location: products.php');
        exit();
    } else {
        $errorMessage = 'Échec de l\'ajout du produit. Veuillez réessayer.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Ajouter un produit</h2>
        
        <?php if (isset($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form action="add_product.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom du produit</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé</label>
                <input type="text" id="libelle" name="libelle" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="image2" class="form-label">Image</label>
                <input type="file" id="image2" name="image2" class="form-control">
            </div>
            <div class="mb-3">
                <label for="quantite" class="form-label">Quantité</label>
                <input type="number" id="quantite" name="quantite" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

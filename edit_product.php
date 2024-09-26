<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

// Vérifiez que l'ID du produit est passé et est valide
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: products.php');
    exit();
}

$product_id = intval($_GET['id']);

// Récupérer les détails du produit
$query = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$query->bindParam(1, $product_id, PDO::PARAM_INT);
$query->execute();
$product = $query->fetch(PDO::FETCH_ASSOC);

// Vérifiez si le produit a été trouvé
if (!$product) {
    header('Location: products.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $libelle = $_POST['libelle'];
    $quantite = $_POST['quantite'];
    $login = $_POST['login'];
    $image = $product['image']; // Conserver l'image actuelle par défaut

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $query = $pdo->prepare('UPDATE produits SET nom = ?, description = ?, libelle = ?, image = ?, quantite = ?, login = ? WHERE id = ?');
    $query->bindParam(1, $nom, PDO::PARAM_STR);
    $query->bindParam(2, $description, PDO::PARAM_STR);
    $query->bindParam(3, $libelle, PDO::PARAM_STR);
    $query->bindParam(4, $image, PDO::PARAM_STR);
    $query->bindParam(5, $quantite, PDO::PARAM_INT);
    $query->bindParam(6, $login, PDO::PARAM_STR);
    $query->bindParam(7, $product_id, PDO::PARAM_INT);
    $query->execute();

    header('Location: products.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un produit</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Modifier le produit</h2>
        <form action="edit_product.php?id=<?= htmlspecialchars($product_id) ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom du produit</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($product['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé</label>
                <input type="text" id="libelle" name="libelle" class="form-control" value="<?= htmlspecialchars($product['libelle']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" id="image" name="image" class="form-control">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>" class="img-thumbnail mt-2" width="100">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="quantite" class="form-label">Quantité</label>
                <input type="number" id="quantite" name="quantite" class="form-control" value="<?= htmlspecialchars($product['quantite']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" id="login" name="login" class="form-control" value="<?= htmlspecialchars($product['login']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier un produit</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Modifier le produit</h2>
        <form action="edit_product.php?id=<?= htmlspecialchars($product['id']) ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom du produit</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($product['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="libelle" class="form-label">Libellé</label>
                <input type="text" id="libelle" name="libelle" class="form-control" value="<?= htmlspecialchars($product['libelle']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" id="image" name="image" class="form-control">
                <?php if ($product['image']): ?>
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>" class="img-thumbnail mt-2" width="100">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="quantite" class="form-label">Quantité</label>
                <input type="number" id="quantite" name="quantite" class="form-control" value="<?= htmlspecialchars($product['quantite']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

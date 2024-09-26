<?php
session_start();
require 'db.php';

// Récupérer l'ID du produit depuis l'URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Préparer et exécuter la requête pour obtenir les détails du produit
$query = $pdo->prepare('SELECT * FROM produits WHERE id = ?');
$query->bindParam(1, $product_id, PDO::PARAM_INT);
$query->execute();
$product = $query->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    // Si le produit n'existe pas, rediriger vers la page d'accueil ou afficher une erreur
    header('Location: shop.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détail du Produit</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <h1><?= htmlspecialchars($product['nom']) ?></h1>
        <div class="row">
            <div class="col-md-6">
                <?php
                // Définir le chemin vers l'image
                $imagePath = !empty($product['image']) ? htmlspecialchars($product['image']) : 'images/default-image.jpg';
                ?>
                <img src="<?= $imagePath ?>" class="img-fluid" alt="Image du produit">
            </div>
            <div class="col-md-6">
                <p><strong>Description:</strong> <?= htmlspecialchars($product['description']) ?></p>
                <p><strong>Quantité:</strong> <?= htmlspecialchars($product['quantite']) ?></p>
                <p><strong>Prix:</strong> <?= htmlspecialchars($product['prix']) ?> €</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="add_to_cart.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-primary">Ajouter au panier</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-info">Se connecter pour acheter</a>
                <?php endif; ?>
            </div>
        </div>
        <a href="shop.php" class="btn btn-secondary mt-3">Retour à la boutique</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

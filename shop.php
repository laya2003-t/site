<?php
session_start();
// Inclure la connexion à la base de données
require 'db.php';

// Charger les produits depuis la base de données
$stmt = $pdo->query("SELECT * FROM produits");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Boutique en Ligne</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main class="container mt-4">
        <header>
            <h1>Boutique en Ligne</h1>
        </header>
        <section>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <article class="col-md-4 mb-4">
                        <div class="card">
                            <?php
                            // Définir le chemin vers l'image
                            $imagePath = !empty($product['image']) ? htmlspecialchars($product['image']) : 'images/default-image.jpg';
                            ?>
                            <img src="<?= $imagePath ?>" class="card-img-top img-thumbnail" alt="Image du produit <?= htmlspecialchars($product['nom']) ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['nom']) ?></h5>
                                <p class="card-text"><strong>Prix:</strong> <?= htmlspecialchars($product['prix']) ?> €</p>
                                <a href="product_detail.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-info">Voir Détails</a>
                                <a href="add_to_cart.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-primary">Ajouter au panier</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
        <footer>
            <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
            <a href="cart.php" class="btn btn-secondary">Voir le panier</a>
        </footer>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

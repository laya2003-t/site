<?php
session_start();

// Inclure la connexion à la base de données
require 'db.php';

// Vérifier si l'ID de la commande est passé dans l'URL
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    header('Location: shop.php');
    exit;
}

// Récupérer l'ID de la commande
$orderId = intval($_GET['order_id']);

// Récupérer les détails de la commande depuis la base de données
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: shop.php');
    exit;
}

// Récupérer les produits de la commande
$stmt = $pdo->prepare("SELECT p.nom, cp.quantite, cp.prix FROM commande_produits cp JOIN produits p ON cp.produit_id = p.id WHERE cp.commande_id = ?");
$stmt->execute([$orderId]);
$orderProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = array_sum(array_map(function($product) {
    return $product['quantite'] * $product['prix'];
}, $orderProducts));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation de Commande</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <h1>Confirmation de Commande</h1>
        <div class="alert alert-success">
            <h4>Merci pour votre commande !</h4>
            <p>Votre commande a été passée avec succès.</p>
            <p><strong>ID de la commande :</strong> <?= htmlspecialchars($orderId) ?></p>
            <p><strong>Total :</strong> <?= htmlspecialchars($total) ?> €</p>
        </div>
        <h3>Détails de la Commande</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom du Produit</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderProducts as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['nom']) ?></td>
                        <td><?= htmlspecialchars($product['quantite']) ?></td>
                        <td><?= htmlspecialchars($product['prix']) ?> €</td>
                        <td><?= htmlspecialchars($product['quantite'] * $product['prix']) ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="shop.php" class="btn btn-primary">Retour à la Boutique</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

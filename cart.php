<?php
session_start();
require 'db.php';

// Initialiser le panier s'il n'existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Calculer le total du panier
$total = 0;

// Mettre à jour la quantité des produits dans le panier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart'])) {
    // Commencer une transaction
    var_dump($_POST);
    $pdo->beginTransaction();
    
    try {
        foreach ($_POST['quantity'] as $productId => $quantity) {
            $quantity = intval($quantity); // Convertir en entier
            
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
                // Supprimer des détails du panier si la quantité est <= 0
                if (isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                    $stmt = $pdo->prepare("DELETE FROM panier_details WHERE panier_id = (SELECT id FROM panier WHERE user_id = ?) AND produit_id = ?");
                    $stmt->execute([$userId, $productId]);
                }
            } else {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;

                // Mettre à jour la base de données si l'utilisateur est connecté
                if (isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];

                    // Vérifiez si le panier pour l'utilisateur existe
                    $stmt = $pdo->prepare("SELECT id FROM panier WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $panier = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($panier) {
                        $panierId = $panier['id'];

                        // Récupérer les détails du produit pour obtenir le nom et le prix
                        $stmt = $pdo->prepare("SELECT nom, prix FROM produits WHERE id = ?");
                        $stmt->execute([$productId]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($product) {
                            $productName = $product['nom'];
                            $productPrice = $product['prix'];

                            // Vérifiez si les détails du produit existent déjà
                            $stmt = $pdo->prepare("SELECT 1 FROM panier_details WHERE panier_id = ? AND produit_id = ?");
                            $stmt->execute([$panierId, $productId]);
                            $exists = $stmt->fetchColumn();

                            if ($exists) {
                                // Mettre à jour les détails du panier
                                $stmt = $pdo->prepare("UPDATE panier_details SET quantite = ?, prix = ? WHERE panier_id = ? AND produit_id = ?");
                                $stmt->execute([$quantity, $productPrice, $panierId, $productId]);
                            } else {
                                // Ajouter les détails du panier
                                $stmt = $pdo->prepare("INSERT INTO panier_details (panier_id, produit_id, nom, quantite, prix) VALUES (?, ?, ?, ?, ?)");
                                $stmt->execute([$panierId, $productId, $productName, $quantity, $productPrice]);
                            }
                        }
                    }
                }
            }
        }
        
        // Valider la transaction
        $pdo->commit();
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        echo "Une erreur est survenue : " . $e->getMessage();
    }
}

// Récupérer les produits du panier depuis la base de données
$productIds = array_keys($_SESSION['cart']);
if (!empty($productIds)) {
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer le total du panier
    foreach ($products as $product) {
        $productId = $product['id'];
        $quantity = isset($_SESSION['cart'][$productId]['quantity']) ? intval($_SESSION['cart'][$productId]['quantity']) : 0;
        $price = floatval($product['prix']);
        $total += $price * $quantity;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <h1>Mon Panier</h1>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <form action="cart.php" method="post">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $productId = $product['id'];
                            $quantity = isset($_SESSION['cart'][$productId]['quantity']) ? intval($_SESSION['cart'][$productId]['quantity']) : 0;
                            $price = floatval($product['prix']);
                            $totalPrice = $price * $quantity;
                            ?>
                            <tr>
                                <input type="hidden" name="">
                                <td><?= htmlspecialchars($product['nom']) ?></td>
                                <td>
                                    <input type="number" name="quantity[<?= htmlspecialchars($productId) ?>]" value="<?= htmlspecialchars($quantity) ?>" min="0" class="form-control" style="width: 100px;">
                                </td>
                                <td><?= htmlspecialchars($price) ?> €</td>
                                <td><?= htmlspecialchars($totalPrice) ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="update_cart" class="btn btn-primary">Mettre à jour le panier</button>
            </form>
            <h3>Total : <?= htmlspecialchars($total) ?> €</h3>
            <a href="checkout.php" class="btn btn-success">Passer à la caisse</a>
        <?php endif; ?>
        <a href="shop.php" class="btn btn-secondary">Retour à la boutique</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

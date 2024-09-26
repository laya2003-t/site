<?php
session_start();
require 'db.php';

function addToCart($productId, $userId, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Vérifier si le panier existe
        $stmt = $pdo->prepare("SELECT id FROM panier WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            // Créer un nouveau panier
            $stmt = $pdo->prepare("INSERT INTO panier (nom, user_id) VALUES (?, ?)");
            $stmt->execute(['Panier de ' . $userId, $userId]);
            $cartId = $pdo->lastInsertId();
        } else {
            $cartId = $cart['id'];
        }

        // Ajouter ou mettre à jour le produit dans la session
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += 1;

            // Mettre à jour la quantité dans panier_details
            $stmt = $pdo->prepare("UPDATE panier_details SET quantite = quantite + 1 WHERE panier_id = ? AND produit_id = ?");
            $stmt->execute([$cartId, $productId]);
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['nom'],
                'price' => $product['prix'],
                'quantity' => 1
            ];

            // Ajouter le produit dans panier_details
            $stmt = $pdo->prepare("INSERT INTO panier_details (panier_id, produit_id, nom, quantite, prix) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$cartId, $product['id'], $product['nom'], 1, $product['prix']]);
        }

        header("Location: cart.php");
        exit();
    } else {
        echo "Produit non trouvé.";
    }
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = intval($_GET['id']);
    $userId = 1; // Remplacez ceci par l'ID de l'utilisateur connecté
    addToCart($productId, $userId, $pdo);
} else {
    echo "ID du produit non fourni ou invalide.";
}
?>

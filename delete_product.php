<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirige vers la page de connexion si la session n'est pas active
    exit();
}

require 'db.php';

// Vérifiez si l'identifiant du produit est passé en paramètre GET
if (isset($_GET['id'])) {
    $product_id = (int) $_GET['id'];

    // Préparer et exécuter la requête de suppression
    $query = $pdo->prepare('DELETE FROM produits WHERE id = ?');
    $query->bindParam(1, $product_id, PDO::PARAM_INT);
    $query->execute();

    // Rediriger vers la liste des produits après la suppression
    header('Location: products.php');
    exit();
} else {
    // Si aucun identifiant de produit n'est spécifié, rediriger vers la liste des produits
    header('Location: products.php');
    exit();
}
?>

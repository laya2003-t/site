<?php
session_start();
// Inclure la connexion à la base de données
require 'db.php';

// Récupérer l'ID de la commande depuis l'URL
$commande_id = $_GET['id'] ?? null;

if ($commande_id) {
    // Charger les détails de la commande
    $stmt = $pdo->prepare("SELECT * FROM `commande_produits` INNER JOIN produits ON produits.id=commande_produits.produit_id WHERE commande_id = ?");
    $stmt->execute([$commande_id]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Charger les informations de la commande
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
    $stmt->execute([$commande_id]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    die('Commande non trouvée.');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Détails de la Commande</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <h1>Détails de la Commande #<?= htmlspecialchars($commande['id']) ?></h1>
        <p><strong>Date:</strong> <?= htmlspecialchars($commande['date_commande']) ?></p>
        <p><strong>Client:</strong> <?= htmlspecialchars($commande['utilisateur_id']) ?></p>
        <p><strong>Montant Total:</strong> <?= htmlspecialchars($commande['total']) ?> €</p>
        <p><strong>Statut:</strong> <?= htmlspecialchars($commande['statut']) ?></p>

        <h2>Détails des Produits</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Prix Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($details as $detail): ?>
                    <tr>
                        <td><?= htmlspecialchars($detail['nom']) ?></td>
                        <td><?= htmlspecialchars($detail['quantite']) ?></td>
                        <td><?= htmlspecialchars($detail['prix']) ?> €</td>
                        <td><?= htmlspecialchars($detail['quantite'] * floatval($detail['prix'])) ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="commandes.php" class="btn btn-primary">Retour aux commandes</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

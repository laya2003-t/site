<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Inclure la connexion à la base de données
require 'db.php';

// Préparer et exécuter la requête pour obtenir la liste des commandes
$stmt = $pdo->query("SELECT c.id, c.date_commande, c.total, u.nom AS username
                      FROM commandes c
                      JOIN utilisateurs u ON c.utilisateur_id = u.id
                      ORDER BY c.date_commande DESC");
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des Commandes</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <h1>Liste des Commandes</h1>
        <?php if (count($commandes) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Montant Total</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td><?= htmlspecialchars($commande['id']) ?></td>
                            <td><?= htmlspecialchars($commande['date_commande']) ?></td>
                            <td><?= htmlspecialchars($commande['username']) ?></td>
                            <td><?= htmlspecialchars($commande['total']) ?> €</td>
                            <td><a href="commande_details.php?id=<?= htmlspecialchars($commande['id']) ?>" class="btn btn-info btn-sm">Voir Détails</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucune commande trouvée.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary mt-4">Retour à l'accueil</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

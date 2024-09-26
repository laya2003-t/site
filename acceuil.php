<?php
session_start();
// Inclure la connexion à la base de données
require 'db.php';

// Préparer la requête SQL pour obtenir la somme des commandes par jour
$query = $pdo->query("
    SELECT DATE(date_commande) AS jour, SUM(total) AS somme_totale
    FROM commandes
    GROUP BY DATE(date_commande)
    ORDER BY DATE(date_commande)
");
$commandes_par_jour = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Somme des Commandes par Jour</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <main class="container mt-4">
        <header>
            <h1>Somme des Commandes par Jour</h1>
        </header>
        <section>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Somme Totale (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes_par_jour as $commande): ?>
                        <tr>
                            <td><?= htmlspecialchars($commande['jour']) ?></td>
                            <td><?= htmlspecialchars(number_format($commande['somme_totale'], 0, ',', ' ')) ?> FCFA</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <footer>
            <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
        </footer>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

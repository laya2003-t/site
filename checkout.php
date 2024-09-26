<?php
session_start();
require 'db.php';
require 'functions.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Initialiser $panier_items
$panier_items = [];

// Vérifier si un panier est stocké dans la session pour les utilisateurs non connectés
if (!$is_logged_in) {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $cart = $_SESSION['cart'];
        foreach ($cart as $product_id => $details) {
            $query = $pdo->prepare('SELECT id, nom, prix FROM produits WHERE id = ?');
            $query->bindParam(1, $product_id, PDO::PARAM_INT);
            $query->execute();
            $product = $query->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                $panier_items[] = [
                    'id' => $product['id'],  // Ajout de l'ID du produit
                    'nom' => $product['nom'],
                    'quantite' => $details['quantite'],
                    'prix' => $product['prix']
                ];
            }
        }
    }
} else {
    // Si l'utilisateur est connecté, récupérer le panier à partir de la base de données
    $query = $pdo->prepare('
        SELECT pr.id, pr.nom, pd.quantite, pd.prix
        FROM panier p
        JOIN panier_details pd ON p.id = pd.panier_id
        JOIN produits pr ON pd.produit_id = pr.id
        WHERE p.user_id = ?');
    $query->bindParam(1, $user_id, PDO::PARAM_INT);
    $query->execute();
    $panier_items = $query->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processus de commande
    if (empty($panier_items)) {
        die('Votre panier est vide.');
    }

    $total = array_sum(array_map(function($item) {
        return $item['quantite'] * $item['prix'];
    }, $panier_items));

    if ($is_logged_in) {
        // Définir les variables de commande
        $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
        $adresse = isset($_POST['adresse']) ? $_POST['adresse'] : '';
        $ville = isset($_POST['ville']) ? $_POST['ville'] : '';
        $code_postal = isset($_POST['code_postal']) ? $_POST['code_postal'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $date_commande = date('Y-m-d H:i:s'); // Date actuelle

        // Enregistrer la commande dans la base de données pour un utilisateur connecté
        $pdo->beginTransaction();

        // Adapter la requête d'insertion
        $stmt = $pdo->prepare('INSERT INTO commandes (nom, adresse, ville, code_postal, email, total, date_commande, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$nom, $adresse, $ville, $code_postal, $email, $total, $date_commande, $user_id]);
        $commande_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare('INSERT INTO commande_produits (commande_id, produit_id, quantite, prix) VALUES (?, ?, ?, ?)');
        foreach ($panier_items as $item) {
            $stmt->execute([$commande_id, $item['id'], $item['quantite'], $item['prix']]);  // Utilisation de 'id' pour le produit_id
        }

        $pdo->commit();

        // Vider le panier
        $query = $pdo->prepare('DELETE FROM panier WHERE user_id = ?');
        $query->execute([$user_id]);

        $message = 'Votre commande a été passée avec succès. ID de la commande : ' . htmlspecialchars($commande_id);
    } else {
        // Traitement pour les utilisateurs non connectés
        $message = 'Nous avons reçu votre commande. Merci de vous inscrire ou de vous connecter pour suivre vos commandes.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passer Commande</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <h1>Passer Commande</h1>
        
        <?php if (!empty($panier_items)): ?>
            <!-- Afficher le contenu du panier -->
            <h2>Contenu du Panier</h2>
            <ul class="list-group">
                <?php foreach ($panier_items as $item): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($item['nom']) ?> - <?= htmlspecialchars($item['quantite']) ?> x <?= formatFCFA(htmlspecialchars($item['prix'])) ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h3 class="mt-4">Total : <?= formatFCFA(array_sum(array_map(function($item) {
                return $item['quantite'] * $item['prix'];
            }, $panier_items))) ?></h3>

            <form action="checkout.php" method="post" class="mt-4">
                <!-- Assurez-vous que ces champs existent dans votre formulaire HTML -->
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="adresse" placeholder="Adresse" required>
                <input type="text" name="ville" placeholder="Ville" required>
                <input type="text" name="code_postal" placeholder="Code Postal" required>
                <input type="email" name="email" placeholder="Email" required>
                <button type="submit" class="btn btn-success">Confirmer la Commande</button>
            </form>
        <?php else: ?>
            <p>Votre panier est vide.</p>
        <?php endif; ?>

        <?php if (isset($message)): ?>
            <div class="alert alert-info mt-4">
                <?= htmlspecialchars($message) ?>
                <!-- Ajouter un bouton pour retourner à la boutique -->
                <a href="index.php" class="btn btn-primary mt-3">Retour à la Boutique</a>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html

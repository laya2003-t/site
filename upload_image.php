<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    $imageFile = $_FILES['image'];

    // Vérifiez les erreurs d'upload
    if ($imageFile['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'images/';
        $uploadFile = $uploadDir . basename($imageFile['name']);

        // Assurez-vous que le répertoire de destination existe et est accessible en écriture
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Déplacez le fichier uploadé vers le répertoire de destination
        if (move_uploaded_file($imageFile['tmp_name'], $uploadFile)) {
            // Mettre à jour le chemin de l'image dans la base de données
            $stmt = $pdo->prepare("UPDATE produits SET image = :image WHERE id = :id");
            $stmt->execute(['image' => $uploadFile, 'id' => $productId]);
            echo "Image téléchargée et mise à jour avec succès.";
        } else {
            echo "Erreur lors du déplacement du fichier.";
        }
    } else {
        echo "Erreur d'upload : " . $imageFile['error'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Uploader une Image</title>
</head>
<body>
    
    <form action="upload_image.php" method="post" enctype="multipart/form-data">
        <label for="product_id">ID du Produit:</label>
        <input type="text" name="product_id" id="product_id" required><br>
        <label for="image">Choisir une image:</label>
        <input type="file" name="image" id="image" required><br>
        <input type="submit" value="Télécharger l'image">
    </form>
    <form action="upload_image.php" method="post" enctype="multipart/form-data">
        <label for="product_id">ID du Produit:</label>br>
        <input type="text" name="product_id" id="product_id" required><br>
        <label for="image">Choisir une image:</label>
        <input type="file" name="image2" id="image2" required><
        <input type="submit" value="Télécharger l'image">
    </form>
    <form action="upload_image.php" method="post" enctype="multipart/form-data">
        <label for="product_id">ID du Produit:</label>
        <input type="text" name="product_id" id="product_id" required><br>
        <label for="image">Choisir une image:</label>
        <input type="file" name="image3" id="image3" required><br>
        <input type="submit" value="Télécharger l'image">
    </form>
    <form action="upload_image.php" method="post" enctype="multipart/form-data">
        <label for="product_id">ID du Produit:</label>
        <input type="text" name="product_id" id="product_id" required><br>
        <label for="image">Choisir une image:</label>
        <input type="file" name="image4" id="image4" required><br>
        <input type="submit" value="Télécharger l'image">
    </form>
    <form action="upload_image.php" method="post" enctype="multipart/form-data">
        <label for="product_id">ID du Produit:</label>
        <input type="text" name="product_id" id="product_id" required><br>
        <label for="image">Choisir une image:</label>
        <input type="file" name="image5" id="image5" required><br>
        <input type="submit" value="Télécharger l'image">
    </form>
    
</body>
</html>

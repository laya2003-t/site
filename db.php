<?php

$host = 'localhost'; // L'adresse du serveur MySQL, souvent 'localhost'
$db   = 'gestion_produits_utilisateurs'; // Le nom de votre base de données
$user = 'root'; // Le nom d'utilisateur MySQL, souvent 'root' pour les installations locales
$pass = ''; // Le mot de passe de l'utilisateur MySQL

try {
    // Créer une connexion PDO à la base de données
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);

    // Configurer PDO pour lancer des exceptions en cas d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}
?>

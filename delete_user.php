<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirection si l'utilisateur n'est pas connecté
    exit();
}

require 'db.php';

// Vérifier si l'ID de l'utilisateur est fourni dans la requête
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Préparer et exécuter la requête de suppression
    $query = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
    $query->bindParam(1, $user_id, PDO::PARAM_INT);
    $query->execute();

    // Rediriger vers la liste des utilisateurs après la suppression
    header('Location: users.php');
    exit();
} else {
    // Redirection en cas de mauvaise requête
    header('Location: users.php');
    exit();
}
?>

<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        // Traitement pour les utilisateurs connectés
        // Sauvegarder la commande avec l'ID utilisateur
    } elseif (isset($_POST['guest_order']) && $_POST['guest_order'] == '1') {
        // Traitement pour les commandes d'invité
        // Sauvegarder la commande sans lien avec un utilisateur spécifique
    } else {
        // Erreur ou redirection
        header('Location: checkout.php');
        exit;
    }

    // Rediriger ou afficher un message de succès
    header('Location: success.php');
    exit;
} else {
    // Si la requête n'est pas POST, rediriger ou afficher une erreur
    header('Location: checkout.php');
    exit;
}

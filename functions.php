<?php 
function formatFCFA($amount) {
    // Convertir la chaîne en nombre flottant si ce n'est pas déjà un nombre
    if (is_string($amount)) {
        $amount = floatval(str_replace(',', '.', $amount));
    }

    // Assurez-vous que $amount est un nombre
    if (!is_numeric($amount)) {
        return 'Invalid amount';
    }

    // Formater le montant en FCFA
    return number_format((float)$amount, 2, ',', ' ') . ' FCFA';
}


require 'db.php';

function totalUsers()
{
    global $pdo; // Assure-toi que $pdo est accessible globalement

    // Préparer et exécuter la requête pour obtenir les informations de l'utilisateur
    $query = $pdo->prepare('SELECT COUNT(id) AS TotalUsers FROM utilisateurs');
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC); // Assure-toi d'utiliser PDO::FETCH_ASSOC pour un tableau associatif

    return $result['TotalUsers'];
}
?>
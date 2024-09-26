<?php
// Mot de passe à hacher
$password = 'laya';

// Hacher le mot de passe
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Afficher le mot de passe haché
echo "Mot de passe haché : " . $hashed_password;
?>

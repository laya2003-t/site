<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']); 
    $password = trim($_POST['password']);
    
    if (empty($login) || empty($password)) {
        $error_message = 'Veuillez entrer votre nom d\'utilisateur et votre mot de passe.';
    } else {
        try {
            // Préparer la requête SQL pour obtenir les informations de l'utilisateur
            $query = $pdo->prepare('SELECT id, password FROM utilisateurs WHERE login = :login');
            $query->bindParam(':login', $login, PDO::PARAM_STR);
            $query->execute();
            $user = $query->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Vérifier le mot de passe avec password_verify
                if (password_verify($password, $user['password'])) {
                    // Authentifier l'utilisateur
                    $_SESSION['user_id'] = $user['id'];
                    header('Location: acceuil.php');
                    exit();
                } else {
                    $error_message = 'Mot de passe incorrect.';
                }
            } else {
                $error_message = 'Nom d\'utilisateur incorrect.';
            }
        } catch (PDOException $e) {
            $error_message = 'Erreur de connexion à la base de données : ' . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Lien vers le CSS de Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        <h4 class="card-title">Connexion</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <div class="form-group">
                                <label for="login">Nom d'utilisateur</label>
                                <input type="text" id="login" name="login" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <input type="password" id="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Se connecter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Lien vers les JS de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

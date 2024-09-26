-- Table des Utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
    photo_profil VARCHAR(255)
);

-- Table des Produits
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    libelle VARCHAR(255),
    image VARCHAR(255),
    quantite INT
);

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    adresse TEXT NOT NULL,
    ville VARCHAR(255) NOT NULL,
    code_postal VARCHAR(10) NOT NULL,
    email VARCHAR(255) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    utilisateur_id INT,
    statut ENUM('En attente', 'Validée', 'Annulée') DEFAULT 'En attente',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);
);

-- Table des produits dans les commandes
CREATE TABLE IF NOT EXISTS commande_produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix VARCHAR(255) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);
-- Table pour les actions du panier
CREATE TABLE panier_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    produit_id INT,
    action ENUM('Ajout', 'Suppression'),
    date_action DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

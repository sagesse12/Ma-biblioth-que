<?php
// 1. Connexion à la base de données (Notez le port 3307)
$servername = "127.0.0.1";
$name = $_POST['name']; 
$email = $_POST['email']; 
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$port = 3307; 

$conn = new mysqli('localhost', 'username', 'password', 
'mabibliotheque','3307');

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

// 2. Récupération et validation des données du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password_raw = $_POST['password'];

    // 3. Hachage du mot de passe pour la sécurité
    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

    // 4. Préparation de la requête SQL (Sécurité contre les injections SQL)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password_hashed);

    // 5. Exécution et redirection
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: login.html");
        exit();
    } else {
        echo "Erreur lors de l'inscription : " . $stmt->error;
    }
}
?>
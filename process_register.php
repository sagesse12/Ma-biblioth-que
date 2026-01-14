<?php
// 1. Connexion à la base de données (Notez le port 3307)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$servername = "127.0.0.1";
$name = $_POST['name']; 
$email = $_POST['email']; 
$password = $_POST['password'];
$confirmPassword =$_POST['confirmPassword'];
$port = 3307; 

if ($password !== $confirmPassword) {
        echo "<script> alert('Les mots de passe ne correspondent pas'); window.history.back(); </script>";
        exit();
    }

    /*if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script> alert('Email invalide'); window.history.back(); </script>";
        exit();
    }*/

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$conn = new mysqli('127.0.0.1', 'root', '', 'mabibliotheque', 3307);

// Vérifier la connexion
if ($conn->connect_error) {
        echo "<script> alert ('Erreur lors de la connexion à la base de données'); window.history.back(); </script>";
        exit();
    }



// 2. Récupération et validation des données du formulaire
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param ("s",$email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script> alert ('Cet email a déjà été utilisé, veuillez en choisir un autre'); window.location.href='login.html'; </script>";
        $check->close();
        $conn->close();
        exit();
    }

    // 4. Préparation de la requête SQL (Sécurité contre les injections SQL)
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    if ($stmt->execute()) {
        echo "<script> alert ('Inscription réussie!!!!!!!!!'); window.location.href='login.html'; </script>";
    }
    else {
        echo "<script> alert ('Erreur lors de l'inscription'); window.history.back(); </script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>
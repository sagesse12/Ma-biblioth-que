<?php
// 1. Initialisation et récupération des données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name']; 
    $email = $_POST['email']; 
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Vérification de la correspondance des mots de passe
    if ($password !== $confirmPassword) {
        echo "<script> alert('Les mots de passe ne correspondent pas'); window.history.back(); </script>";
        exit();
    }

    // Hachage sécurisé du mot de passe
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Remplacer le bloc de connexion par celui-ci pour tester :
mysqli_report(MYSQLI_REPORT_OFF); // Désactive les rapports automatiques
$conn = @new mysqli('127.0.0.1', 'root', '', 'mabibliotheques', 3307);

if ($conn->connect_error) {
    // Affichera l'erreur exacte fournie par MySQL
    die("Détail de l'erreur : " . $conn->connect_error); 
}
    // 2. Connexion à la base de données
    // IMPORTANT : Utilisez '127.0.0.1' au lieu de 'localhost' pour forcer l'usage du port 3307
    /*$conn = new mysqli('127.0.0.1', 'root', '', 'mabibliotheque', 3307);
 
    // Vérifier la connexion
    if ($conn->connect_error) {
        echo "<script> alert('Erreur lors de la connexion à la base de données'); window.history.back(); </script>";
        exit();
    }*/

    // 3. Vérification de l'existence de l'email (Requête préparée)
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script> alert('Cet email a déjà été utilisé, veuillez en choisir un autre'); window.location.href='login.html'; </script>";
        $check->close();
        $conn->close();
        exit();
    }
    $check->close();

    // 4. Insertion du nouvel utilisateur
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script> alert('Inscription réussie !'); window.location.href='login.html'; </script>";
    } else {
        echo "<script> alert('Erreur lors de l\'inscription'); window.history.back(); </script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>
<?php
session_start(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // CORRECTION 1 : Nom de la base sans 's' à la fin
    $conn = new mysqli('127.0.0.1', 'root', '', 'mabibliotheques', 3307);

    if ($conn->connect_error) {
        die("Erreur de connexion");
    }

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // CORRECTION 2 : Redirection vers la page de compte PHP
            echo "<script>
                alert('Connexion réussie ! Bienvenue " . addslashes($user['name']) . "'); 
                window.location.href='mon_compte.php'; 
            </script>";
        } else {
            echo "<script>alert('Mot de passe incorrect'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Aucun compte trouvé avec cet email'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
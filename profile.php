<?php
session_start();

// 1. Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "mabibliotheque",3307);
if ($conn->connect_error) die("Erreur de connexion à la base : " . $conn->connect_error);

// 3. Déconnexion si le bouton est cliqué
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// 4. Récupération des infos de l'utilisateur
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>
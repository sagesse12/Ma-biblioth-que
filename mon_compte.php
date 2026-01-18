<?php
session_start();

// Sécurité : si l'utilisateur n'est pas connecté, on le renvoie au login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

// Connexion à la base
$conn = new mysqli('127.0.0.1', 'root', '', 'mabibliotheques', 3307);

// 1. Récupérer les livres lus par l'utilisateur (votre espace perso)
$query_lus = "SELECT books.title, books.author 
              FROM books 
              INNER JOIN lectures ON books.id = lectures.book_id 
              WHERE lectures.user_id = ?";
$stmt_lus = $conn->prepare($query_lus);
$stmt_lus->bind_param("i", $user_id);
$stmt_lus->execute();
$result_lus = $stmt_lus->get_result();

// 2. Récupérer TOUS les livres de la bibliothèque (pour la page "Livres")
$result_all = $conn->query("SELECT title, author FROM books");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace - <?php echo htmlspecialchars($user_name); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Petit style rapide pour séparer les sections */
        .container { padding: 20px; font-family: sans-serif; }
        nav { background: #333; padding: 10px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 15px; }
        section { border-bottom: 1px solid #ccc; padding: 20px 0; }
        .welcome { color: #2c3e50; }
    </style>
</head>
<body>

    <nav>
        <a href="#accueil">Accueil</a>
        <a href="#bibliotheque">Bibliothèque complète</a>
        <a href="#mes-livres">Mes livres lus</a>
        <a href="logout.php" style="color: #ff4b4b;">Déconnexion</a>
    </nav>

    <div class="container">
        
        <section id="accueil">
            <h1 class="welcome">Ravi de vous revoir, <?php echo htmlspecialchars($user_name); ?> !</h1>
            <p>Bienvenue dans votre gestionnaire de bibliothèque personnel.</p>
        </section>

        <section id="mes-livres" style="background-color: #f9f9f9; padding: 15px;">
            <h2>📖 Mes lectures personnelles</h2>
            <?php if ($result_lus->num_rows > 0): ?>
                <ul>
                    <?php while($row = $result_lus->fetch_assoc()): ?>
                        <li><strong><?php echo htmlspecialchars($row['title']); ?></strong> par <?php echo htmlspecialchars($row['author']); ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Vous n'avez pas encore marqué de livres comme "lus".</p>
            <?php endif; ?>
        </section>

        <section id="bibliotheque">
            <h2>📚 Tous les livres disponibles</h2>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                <?php while($book = $result_all->fetch_assoc()): ?>
                    <div style="border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                        <strong><?php echo htmlspecialchars($book['title']); ?></strong><br>
                        <small><?php echo htmlspecialchars($book['author']); ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

    </div>

</body>
</html>
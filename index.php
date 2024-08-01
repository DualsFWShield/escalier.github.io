<?php
session_start();

// Réinitialiser les variables de session si on retourne à l'accueil
if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Gérer la soumission du formulaire des joueurs
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['players'])) {
    $players = array_map('trim', explode(',', $_POST['players']));
    foreach ($players as $player) {
        $_SESSION['players'][] = [
            'name' => $player,
            'score' => 0
        ];
    }
    header("Location: game.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu Escalier - Accueil</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Jeu Escalier</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="stats.php">Statistiques</a>
            <a href="rules.php">Règles du Jeu</a>
            <a href="settings.php">Paramètres</a>
        </nav>
    </header>
    <main>
        <h2>Bienvenue dans le Jeu Escalier</h2>
        <form method="post">
            <label for="players">Entrez les noms des joueurs (séparés par des virgules) :</label>
            <input type="text" name="players" id="players" required>
            <button type="submit">Démarrer la Partie</button>
        </form>
    </main>
</body>
</html>

<?php
// index.php
session_start();

// Réinitialiser les variables de session pour commencer une nouvelle partie
unset($_SESSION['players']);
unset($_SESSION['current_round']);
unset($_SESSION['current_player_index']);
unset($_SESSION['rounds']);
unset($_SESSION['predictions']);
unset($_SESSION['actual_plis']);
unset($_SESSION['round_results']);
unset($_SESSION['error_rounds']);
unset($_SESSION['correct_rounds']);
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
        <h1>Jeu Escalier - Accueil</h1>
    </header>
    <main>
        <h2>Bienvenue dans le jeu Escalier !</h2>
        <a href="game.php">Démarrer une nouvelle partie</a>
    </main>
</body>
</html>

<?php
// rules.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu Escalier - Règles</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Jeu Escalier - Règles</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="game.php">Partie</a>
            <a href="stats.php">Statistiques</a>
        </nav>
    </header>
    <main>
        <h2>Règles du Jeu</h2>
        <p>Il y a 20 manches et le numéro de la manche équivaut au nombre de plis maximum au total qui peut être fait durant cette manche.</p>
        <p>Les manches font de 1 à 10 puis de 10 à 1, donc dans l'ordre les manches et plis pouvant être fait sont : 1, 2, 3, ..., 10, 10, 9, ..., 1.</p>
        <p>Les joueurs doivent pronostiquer chacun leur tour sur le nombre de plis qu'ils pensent faire sur la manche et, à la fin de la manche, on comptabilise le nombre réel de plis pour chaque joueur.</p>
        <p>Un pli vaut 5 points : Lorsqu'un joueur gagne un pli, il marque 5 points. Les points gagnés pour chaque pli sont cumulés.</p>
        <p>Pénalité pour écart avec le pronostic :</p>
        <ul>
            <li>Si le joueur gagne exactement le nombre de plis qu'il a pronostiqué, il marque 5 points supplémentaires.</li>
            <li>Si le joueur gagne moins de plis qu'il a pronostiqué, il perd 5 points plus 5 points pour chaque pli d'écart.</li>
            <li>Si le joueur gagne plus de plis qu'il a pronostiqué, il perd 5 points plus 5 points pour chaque pli d'écart.</li>
        </ul>
        <p>Exemples de comptage des points :</p>
        <ul>
            <li>Si François a pronostiqué 2 plis et gagne 2 plis : 2 x 5 = 10 points + 5 points pour le pronostic correct = 15 points.</li>
            <li>Si François a pronostiqué 2 plis et gagne 1 pli : -5 points pour l'écart + 5 points de pénalité = -10 points.</li>
            <li>Si François a pronostiqué 2 plis et gagne 0 plis : -10 points pour l'écart + 5 points de pénalité = -15 points.</li>
            <li>Si François a pronostiqué 2 plis et gagne 3 plis : -5 points pour l'écart + 5 points de pénalité = -10 points.</li>
            <li>Si François a pronostiqué 2 plis et gagne 6 plis : -20 points pour l'écart + 5 points de pénalité = -25 points.</li>
        </ul>
    </main>
</body>
</html>

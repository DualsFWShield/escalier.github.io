<?php
session_start();

// Vérifier si la partie est terminée et si les résultats des manches existent
if (!isset($_SESSION['players']) || !isset($_SESSION['round_results'])) {
    header('Location: index.php');
    exit();
}

// Calculer le nombre de manches correctes et erronées pour chaque joueur
$players_stats = [];
foreach ($_SESSION['players'] as $player) {
    $players_stats[$player['name']] = [
        'correct' => 0,
        'errors' => 0
    ];
}

foreach ($_SESSION['round_results'] as $round_result) {
    foreach ($round_result['results'] as $player_name => $result) {
        if ($result['correct']) {
            $players_stats[$player_name]['correct']++;
        } else {
            $players_stats[$player_name]['errors']++;
        }
    }
}

// Calculer les scores finaux
$final_scores = [];
foreach ($_SESSION['players'] as $player) {
    $final_scores[$player['name']] = $player['score'];
}
arsort($final_scores); // Trier les scores du plus élevé au plus bas

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu Escalier - Statistiques</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Jeu Escalier - Statistiques</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="stats.php">Statistiques</a>
            <a href="rules.php">Règles du Jeu</a>
        </nav>
    </header>
    <main>
        <h2>Tableau Final</h2>
        
        <h3>Classement des Joueurs</h3>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($final_scores as $name => $score): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($name); ?></td>
                        <td><?php echo $score; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Détails des Manches</h3>
        <table>
            <thead>
                <tr>
                    <th>Manche</th>
                    <th>Joueur</th>
                    <th>Prédiction</th>
                    <th>Plis Réels</th>
                    <th>Points</th>
                    <th>Correct</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['round_results'] as $round_result): ?>
                    <?php foreach ($round_result['results'] as $player_name => $result): ?>
                        <tr>
                            <td><?php echo $round_result['round'] + 1; ?></td>
                            <td><?php echo htmlspecialchars($player_name); ?></td>
                            <td><?php echo $result['prediction']; ?></td>
                            <td><?php echo $result['actual']; ?></td>
                            <td><?php echo $result['points']; ?></td>
                            <td><?php echo $result['correct'] ? 'Oui' : 'Non'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Statistiques par Joueur</h3>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Manches Correctes</th>
                    <th>Manches Erronées</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players_stats as $player_name => $stats): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($player_name); ?></td>
                        <td><?php echo $stats['correct']; ?></td>
                        <td><?php echo $stats['errors']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form action="index.php" method="post">
            <button type="submit" name="new_game">Nouvelle Partie</button>
        </form>
    </main>
</body>
</html>

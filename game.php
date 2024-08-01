<?php
session_start();

// Initialisation des variables de session si ce n'est pas encore fait
if (!isset($_SESSION['players'])) {
    $_SESSION['players'] = [];
    $_SESSION['current_round'] = 0;
    $_SESSION['current_player_index'] = 0;
    $_SESSION['rounds'] = array_merge(range(1, 10), range(10, 1)); // Liste des manches
    $_SESSION['round_results'] = []; // Pour stocker les résultats des manches
}

// Fonction pour calculer les scores
function calculateScores($players, $predictions, $actualPlis, $currentRound) {
    $round_result = [];
    foreach ($players as $index => &$player) {
        $predictedPlis = intval($predictions[$index]);
        $actualPlisWon = intval($actualPlis[$index]);

        // Assurez-vous que le nombre de plis réels est dans la plage permise
        if ($actualPlisWon > $currentRound) {
            $actualPlisWon = $currentRound;
        }

        // Points pour les plis gagnés et calcul des pénalités
        if ($predictedPlis === $actualPlisWon) {
            $player['score'] += $actualPlisWon * 5;
            $player['score'] += 5;
            $correct = true;
        } else {
            $difference = abs($predictedPlis - $actualPlisWon);
            $penalty = $difference * 5 + 5;
            $player['score'] -= $penalty;
            $correct = false;
        }

        $round_result[$players[$index]['name']] = [
            'prediction' => $predictedPlis,
            'actual' => $actualPlisWon,
            'points' => $actualPlisWon * 5 + ($predictedPlis === $actualPlisWon ? 5 : -($difference * 5 + 5)),
            'correct' => $correct
        ];
    }
    return [$players, $round_result];
}

// Gérer la soumission du formulaire pour la fin de la manche
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['end_round'])) {
    $predictions = $_POST['predictions'];
    $actualPlis = $_POST['actual_plis'];
    $currentRound = $_SESSION['rounds'][$_SESSION['current_round']];

    // Vérifiez les prédictions et les résultats
    $totalActualPlis = array_sum($actualPlis);
    $valid = true;

    // Validation des prédictions
    foreach ($predictions as $prediction) {
        if (intval($prediction) > $currentRound) {
            $error = "Les prédictions doivent être inférieures ou égales au nombre maximum de plis pour la manche.";
            $valid = false;
            break;
        }
    }

    // Validation du total des plis réels
    if ($totalActualPlis !== $currentRound) {
        $error = "Le total des plis réels doit être égal au nombre maximum de plis pour la manche.";
        $valid = false;
    }

    if ($valid) {
        // Calcul des scores et stockage des résultats de la manche
        list($_SESSION['players'], $round_result) = calculateScores($_SESSION['players'], $predictions, $actualPlis, $currentRound);
        $_SESSION['round_results'][] = [
            'round' => $_SESSION['current_round'],
            'results' => $round_result
        ];

        // Avancer à la manche suivante
        $_SESSION['current_round']++;
        $_SESSION['current_player_index'] = ($_SESSION['current_player_index'] + 1) % count($_SESSION['players']);

        // Vérifiez si la partie est terminée
        if ($_SESSION['current_round'] >= count($_SESSION['rounds'])) {
            header('Location: stats.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu Escalier - Partie</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Récupérer les données de la partie sauvegardée
            const savedGame = JSON.parse(localStorage.getItem("savedGame"));
            if (savedGame) {
                // Restauration des données de la session
                <?php
                if (isset($savedGame['players'])) {
                    $_SESSION['players'] = $savedGame['players'];
                }
                if (isset($savedGame['current_round'])) {
                    $_SESSION['current_round'] = $savedGame['current_round'];
                }
                if (isset($savedGame['current_player_index'])) {
                    $_SESSION['current_player_index'] = $savedGame['current_player_index'];
                }
                if (isset($savedGame['round_results'])) {
                    $_SESSION['round_results'] = $savedGame['round_results'];
                }
                ?>
            }
        });

        function saveGame() {
            const players = <?php echo json_encode($_SESSION['players']); ?>;
            const currentRound = <?php echo $_SESSION['current_round']; ?>;
            const currentPlayerIndex = <?php echo $_SESSION['current_player_index']; ?>;
            const roundResults = <?php echo json_encode($_SESSION['round_results']); ?>;

            const gameData = {
                players: players,
                current_round: currentRound,
                current_player_index: currentPlayerIndex,
                round_results: roundResults
            };

            localStorage.setItem("savedGame", JSON.stringify(gameData));
        }

        function clearSavedGame() {
            localStorage.removeItem("savedGame");
        }

        window.onbeforeunload = saveGame;
    </script>
</head>
<body>
    <header>
        <h1>Jeu Escalier - Partie</h1>
        <nav>
            <a href="index.php" onclick="clearSavedGame()">Accueil</a>
            <a href="stats.php">Statistiques</a>
            <a href="rules.php">Règles du Jeu</a>
        </nav>
    </header>
    <main>
        <h2>Manche <?php echo $_SESSION['current_round'] + 1; ?> / 20</h2>
        <p>Manche avec un maximum de plis : <?php echo $_SESSION['rounds'][$_SESSION['current_round']]; ?></p>
        <p>Joueur qui doit pronostiquer en premier : <?php echo $_SESSION['players'][$_SESSION['current_player_index']]['name']; ?></p>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post">
            <h3>Pronostics</h3>
            <?php foreach ($_SESSION['players'] as $index => $player): ?>
                <div>
                    <label><?php echo htmlspecialchars($player['name']); ?> : </label>
                    <input type="number" name="predictions[]" min="0" max="<?php echo $_SESSION['rounds'][$_SESSION['current_round']]; ?>" placeholder="Prédiction" value="0" required>
                </div>
            <?php endforeach; ?>
            <h3>Résultats</h3>
            <?php foreach ($_SESSION['players'] as $index => $player): ?>
                <div>
                    <label><?php echo htmlspecialchars($player['name']); ?> : </label>
                    <input type="number" name="actual_plis[]" min="0" max="<?php echo $_SESSION['rounds'][$_SESSION['current_round']]; ?>" placeholder="Plis Réels" value="0" required>
                </div>
            <?php endforeach; ?>
            <button type="submit" name="end_round">Terminer la Manche</button>
        </form>

        <!-- Scores Table -->
        <h3>Tableau des Scores</h3>
        <table id="scores-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['players'] as $player): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($player['name']); ?></td>
                        <td><?php echo $player['score']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
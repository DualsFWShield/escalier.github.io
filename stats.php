<?php
session_start();

// Fonction pour récupérer les parties sauvegardées depuis localStorage
function getSavedGames() {
    if (isset($_COOKIE['savedGames'])) {
        return json_decode($_COOKIE['savedGames'], true);
    }
    return [];
}

// Sauvegarder les parties en session
$savedGames = getSavedGames();
$_SESSION['savedGames'] = $savedGames;

// Fonction pour générer un tableau de stats des joueurs
function generatePlayerStats($players) {
    $stats = [];
    foreach ($players as $player) {
        $stats[$player['name']] = [
            'correct_rounds' => 0,
            'incorrect_rounds' => 0,
            'total_points' => $player['score']
        ];
    }
    return $stats;
}

// Génération des données pour les graphiques
$playerStats = generatePlayerStats($_SESSION['players']);

// Fonction d'exportation des statistiques au format JSON
if (isset($_POST['export'])) {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="stats.json"');
    echo json_encode(['players' => $_SESSION['players'], 'round_results' => $_SESSION['round_results']]);
    exit();
}

// Fonction d'importation des statistiques au format JSON
if (isset($_POST['import'])) {
    if (isset($_FILES['imported_file']) && $_FILES['imported_file']['error'] == 0) {
        $importedData = json_decode(file_get_contents($_FILES['imported_file']['tmp_name']), true);
        $_SESSION['players'] = $importedData['players'];
        $_SESSION['round_results'] = $importedData['round_results'];
    }
}

// Calcul des statistiques par joueur
foreach ($_SESSION['round_results'] as $round) {
    foreach ($round['results'] as $playerName => $result) {
        if ($result['correct']) {
            $playerStats[$playerName]['correct_rounds']++;
        } else {
            $playerStats[$playerName]['incorrect_rounds']++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu Escalier - Statistiques</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('playerStatsChart').getContext('2d');
            const playerNames = <?php echo json_encode(array_keys($playerStats)); ?>;
            const correctRounds = <?php echo json_encode(array_column($playerStats, 'correct_rounds')); ?>;
            const incorrectRounds = <?php echo json_encode(array_column($playerStats, 'incorrect_rounds')); ?>;
            const totalPoints = <?php echo json_encode(array_column($playerStats, 'total_points')); ?>;

            let chartType = 'bar';

            const config = {
                type: chartType,
                data: {
                    labels: playerNames,
                    datasets: [
                        {
                            label: 'Manches Correctes',
                            data: correctRounds,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Manches Erronées',
                            data: incorrectRounds,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Total Points',
                            data: totalPoints,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };

            let chart = new Chart(ctx, config);

            document.getElementById('chartType').addEventListener('change', function() {
                chart.destroy();
                config.type = this.value;
                chart = new Chart(ctx, config);
            });
        });
    </script>
</head>
<body>
    <header>
        <h1>Jeu Escalier - Statistiques</h1>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="game.php">Retour au Jeu</a>
            <a href="rules.php">Règles du Jeu</a>
        </nav>
    </header>
    <main>
        <h2>Statistiques des Joueurs</h2>
        <label for="chartType">Type de graphique :</label>
        <select id="chartType">
            <option value="bar">Barres</option>
            <option value="line">Lignes</option>
            <option value="pie">Camembert</option>
            <option value="doughnut">Donut</option>
        </select>
        <canvas id="playerStatsChart" width="400" height="200"></canvas>

        <h2>Détails des Manches</h2>
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
                <?php foreach ($_SESSION['round_results'] as $round): ?>
                    <?php foreach ($round['results'] as $playerName => $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($round['round'] + 1); ?></td>
                            <td><?php echo htmlspecialchars($playerName); ?></td>
                            <td><?php echo htmlspecialchars($result['prediction']); ?></td>
                            <td><?php echo htmlspecialchars($result['actual']); ?></td>
                            <td><?php echo htmlspecialchars($result['points']); ?></td>
                            <td><?php echo $result['correct'] ? 'Oui' : 'Non'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Export et Import de Statistiques</h2>
        <form method="post" enctype="multipart/form-data">
            <button type="submit" name="export">Exporter les Statistiques</button>
        </form>
        <form method="post" enctype="multipart/form-data">
            <label for="imported_file">Importer un fichier JSON :</label>
            <input type="file" name="imported_file" accept=".json" required>
            <button type="submit" name="import">Importer les Statistiques</button>
        </form>
    </main>
</body>
</html>
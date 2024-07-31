<?php
session_start();

if (isset($_POST['player1_name'], $_POST['player2_name'], $_POST['player3_name'], $_POST['player4_name'], $_POST['player5_name'])) {
    $_SESSION['player1_name'] = $_POST['player1_name'];
    $_SESSION['player2_name'] = $_POST['player2_name'];
    $_SESSION['player3_name'] = $_POST['player3_name'];
    $_SESSION['player4_name'] = $_POST['player4_name'];
    $_SESSION['player5_name'] = $_POST['player5_name'];
    header('Location: game.php');
    exit;
}

if (!isset($_SESSION['player1_name'], $_SESSION['player2_name'], $_SESSION['player3_name'], $_SESSION['player4_name'], $_SESSION['player5_name'])) {
    header('Location: index.html');
    exit;
}

$player1_name = $_SESSION['player1_name'];
$player2_name = $_SESSION['player2_name'];
$player3_name = $_SESSION['player3_name'];
$player4_name = $_SESSION['player4_name'];
$player5_name = $_SESSION['player5_name'];

$rounds = 20; // 20 rounds in total
$current_round = 1; // current round

// Initialize game data
$game_data = array(
    'players' => array(
        $player1_name,
        $player2_name,
        $player3_name,
        $player4_name,
        $player5_name
    ),
    'predictions' => array_fill(0, 5, array_fill(0, $rounds, 0)),
    'scores' => array_fill(0, 5, 0)
);

// Save game data to local storage
if (!isset($_SESSION['game_data'])) {
    $_SESSION['game_data'] = $game_data;
} else {
    $game_data = $_SESSION['game_data'];
}

// Restore game data from local storage
if (isset($_SESSION['game_data'])) {
    $game_data = $_SESSION['game_data'];
}

// Calculate scores based on the rules
function calculate_scores($predictions, $actual_plis) {
    $scores = array_fill(0, 5, 0);
    for ($i = 0; $i < 5; $i++) {
        $plis_gagnes = rand(0, 10); // génère un nombre aléatoire de plis gagnés
        $points_plis = $plis_gagnes * 5;
        if ($plis_gagnes == $predictions[$i]) {
            $points_bonus = 5;
        } elseif ($plis_gagnes < $predictions[$i]) {
            $points_bonus = -5;
        } else {
            $points_bonus = -5 * ($plis_gagnes - $predictions[$i]) - 5;
        }
        $total_points = $points_plis + $points_bonus;
        $scores[$i] = $total_points;
    }
    return $scores;
}

// Display game state
echo '<h1>Game State</h1>';
echo '<p>Current Round: '. $current_round. '</p>';
echo '<p>Players: ';
foreach ($game_data['players'] as $player) {
    echo $player. ', ';
}
echo '</p>';
echo '<p>Predictions: ';
for ($i = 0; $i < 5; $i++) {
    echo $game_data['predictions'][$i][$current_round - 1]. ', ';
}
echo '</p>';
echo '<p>Scores: ';
for ($i = 0; $i < 5; $i++) {
    echo $game_data['scores'][$i]. ', ';
}
echo '</p>';

// Form to submit predictions
echo '<form action="game.php" method="post">';
for ($i = 0; $i < 5; $i++) {
    echo '<label for="prediction_'.$i.'">Prediction for '. $game_data['players'][$i]. '</label>';
    echo '<input type="number" id="prediction_'.$i.'" name="prediction_'.$i.'" required><br><br>';
}
echo '<input type="submit" value="Submit Predictions">';
echo '</form>';

// Process predictions
if (isset($_POST['prediction_0'], $_POST['prediction_1'], $_POST['prediction_2'], $_POST['prediction_3'], $_POST['prediction_4'])) {
    for ($i = 0; $i < 5; $i++) {
        $game_data['predictions'][$i][$current_round - 1] = $_POST['prediction_'.$i];
    }
        $actual_plis = rand(0, 10); // génère un nombre aléatoire de plis gagnés
    $game_data['scores'] = calculate_scores($game_data['predictions'], $actual_plis);
    $current_round++;
    if ($current_round > $rounds) {
        // Game over, display final scores
        echo '<h1>Game Over!</h1>';
        echo '<p>Final Scores: ';
        for ($i = 0; $i < 5; $i++) {
            echo $game_data['players'][$i]. ': '. $game_data['scores'][$i]. ', ';
        }
        echo '</p>';
    } else {
        // Save game data to local storage
        $_SESSION['game_data'] = $game_data;
    }
}

// Save game data to local storage
$_SESSION['game_data'] = $game_data;

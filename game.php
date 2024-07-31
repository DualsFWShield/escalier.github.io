<?php
session_start();

if (isset($_POST['player1_name'], $_POST['player2_name'], $_POST['plis_pronostiques'])) {
    $_SESSION['player1_name'] = $_POST['player1_name'];
    $_SESSION['player2_name'] = $_POST['player2_name'];
    $_SESSION['plis_pronostiques'] = $_POST['plis_pronostiques'];
    header('Location: game.php');
    exit;
}

if (!isset($_SESSION['player1_name'], $_SESSION['player2_name'], $_SESSION['plis_pronostiques'])) {
    header('Location: index.html');
    exit;
}

$player1_name = $_SESSION['player1_name'];
$player2_name = $_SESSION['player2_name'];
$plis_pronostiques = $_SESSION['plis_pronostiques'];

$plis_gagnes = rand(0, 10); // génère un nombre aléatoire de plis gagnés

$points = calculate_points($plis_gagnes, $plis_pronostiques);

function calculate_points($plis_gagnes, $plis_pronostiques) {
    // Un pli vaut 5 points
    $points_plis = $plis_gagnes * 5;
    
    // Pénalité pour écart avec le pronostic
    if ($plis_gagnes == $plis_pronostiques) {
        $points_bonus = 5;
    } elseif ($plis_gagnes < $plis_pronostiques) {
        $points_bonus = -5;
    } else {
        $points_bonus = -5 * ($plis_gagnes - $plis_pronostiques) - 5;
    }
    
    // Cumul des points
    $total_points = $points_plis + $points_bonus;
    
    // Limite de points
    if ($total_points < 5) {
        $total_points = 5;
    }
    
    return $total_points;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L'Escalier - Résultat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>L'Escalier - Résultat</h1>
    </header>
    <main>
        <section>
            <h2>Résultat</h2>
            <p>Le joueur 1, <?php echo $player1_name; ?>, a gagné <?php echo $plis_gagnes; ?> plis.</p>
            <p>Le pronostic était de <?php echo $plis_pronostiques; ?> plis.</p>
            <p>Le score est de <?php echo $total_points; ?> points.</p>
        </section>
    </main>
</body>
</html>

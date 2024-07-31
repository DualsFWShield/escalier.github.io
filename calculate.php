<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plis = $_POST["plis"];
    $points = $_POST["points"];

    // Logique pour calculer les points en fonction des plis
    $points_calculés = calculatePoints($plis, $points);

    echo "<script>document.getElementById('result').innerHTML = 'Vous avez $points_calculés points!';</script>";
}

function calculatePoints($plis, $points) {
    // Logique pour calculer les points en fonction des plis
    // Par exemple, on peut utiliser des règles de calcul comme :
    // - 1 point par pli gagné
    // - 2 points par pli perdu
    // - 3 points par pli annulé
    $points_calculés = 0;
    $plis_array = explode("\n", $plis);
    foreach ($plis_array as $pli) {
        if (strpos($pli, "gagné")!== false) {
            $points_calculés += 1;
        } elseif (strpos($pli, "perdu")!== false) {
            $points_calculés += 2;
        } elseif (strpos($pli, "annulé")!== false) {
            $points_calculés += 3;
        }
    }
    return $points_calculés;
}

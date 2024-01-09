<?php
include 'db.php';

if(isset($_POST['equipeId']) && isset($_POST['points'])) {
    $equipeId = $_POST['equipeId'];
    $points = $_POST['points'];

    // Mettre à jour les points dans la base de données
    $sql = "UPDATE equipes SET score = score + $points WHERE id = $equipeId";
    $conn->query($sql);

    // Récupérer le nouveau score
    $sql = "SELECT score FROM equipes WHERE id = $equipeId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['score'];
    } else {
        echo "Erreur lors de la récupération du score mis à jour.";
    }
} else {
    echo "Paramètres manquants.";
}

$conn->close();
?>

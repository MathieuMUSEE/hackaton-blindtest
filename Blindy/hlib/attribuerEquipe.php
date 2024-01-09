<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $joueurId = $_POST["joueurId"];
    $equipeId = $_POST["equipeId"];

    $sql = "UPDATE file_attente SET equipe_id = $equipeId WHERE id = $joueurId";

    if ($conn->query($sql) === TRUE) {
        echo "Équipe attribuée avec succès.";
    } else {
        echo "Erreur lors de l'attribution de l'équipe : " . $conn->error;
    }
}

$conn->close();
?>

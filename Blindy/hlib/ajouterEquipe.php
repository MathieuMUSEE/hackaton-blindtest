<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez le nom de l'équipe soumis dans le formulaire
    $nomEquipe = $_POST["nomEquipe"];

    // Ajoutez l'équipe à la base de données
    $sql = "INSERT INTO equipes (nom_equipe, score) VALUES ('$nomEquipe', 0)"; // Suppression de '$id' car il est AUTO_INCREMENT
    if ($conn->query($sql) === TRUE) {
        echo "Équipe ajoutée avec succès.";
    } else {
        echo "Erreur lors de l'ajout de l'équipe : " . $conn->error;
    }
}
$conn->close();
?>

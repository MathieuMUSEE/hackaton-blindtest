<?php
include 'db.php';

// Récupérez la liste des joueurs en attente
$sqlFileAttente = "SELECT * FROM file_attente";
$resultFileAttente = $conn->query($sqlFileAttente);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File d'Attente</title>
</head>
<body>

<h1>File d'Attente</h1>

<?php
// Affichez la liste des joueurs en attente
if ($resultFileAttente->num_rows > 0) {
    echo "<p>Joueurs en attente :</p>";
    echo "<ul>";
    while ($row = $resultFileAttente->fetch_assoc()) {
        echo "<li>" . $row['pseudo'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Aucun joueur en attente.</p>";
}
?>

</body>
</html>

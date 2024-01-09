<?php
include 'db.php';

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérez le pseudo et le code soumis dans le formulaire
    $pseudo = $_POST["pseudo"];
    $code = $_POST["code"];

    // Vérifiez le pseudo et le code dans la base de données
    $sql = "SELECT * FROM codes WHERE pseudo = '$pseudo' AND code = '$code'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Pseudo et code corrects, redirigez vers la page pannel.php
        header("Location: pannel.php");
        exit();
    } else {
        // Ajoutez le nouveau joueur à la file d'attente
        $sqlInsert = "INSERT INTO file_attente (pseudo) VALUES ('$pseudo')";
        $conn->query($sqlInsert);

        // Redirigez vers la page choose_team.php
        header("Location: choose_team.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrer le Code</title>
</head>
<body>

<h1>Entrer le Code</h1>

<!-- Formulaire pour entrer le pseudo et le code -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="pseudo">Votre Pseudo :</label>
    <input type="text" id="pseudo" name="pseudo" required>

    <label for="code">Code à 4 chiffres :</label>
    <input type="text" id="code" name="code" maxlength="4" pattern="\d{4}" required>

    <button type="submit">Entrer</button>
</form>

</body>
</html>

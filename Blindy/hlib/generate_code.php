<?php
// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Générez un code à 4 chiffres aléatoirement
    $code = rand(1000, 9999);

    // Récupérez le pseudo soumis dans le formulaire
    $pseudo = $_POST["pseudo"];

    // Enregistrez le code et le pseudo dans la base de données
    include 'db.php';
    
    $sql = "INSERT INTO codes (code, pseudo) VALUES ('$code', '$pseudo')";
    
    if ($conn->query($sql) === TRUE) {
        // Affichez le code généré
        echo "<h2>Code généré :</h2>";
        echo "<p style='font-size: 24px; color: green;' id='generatedCode'>$code</p>";
        echo "<p id='copyMessage'>Merci de copier le code.</p>";

        // Ajoutez le script JavaScript pour gérer le compte à rebours
        echo "<script>
            var countdown = 10;
            var copyMessage = document.getElementById('copyMessage');
            
            function updateCountdown() {
                copyMessage.innerHTML = 'Merci de copier le code. Redirection dans ' + countdown + ' secondes.';
                countdown--;

                if (countdown < 0) {
                    window.location.href = 'enter_code.php';
                } else {
                    setTimeout(updateCountdown, 1000);
                }
            }

            // Démarrez le compte à rebours après le chargement de la page
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(updateCountdown, 1000);
            });
        </script>";
    } else {
        echo "Erreur lors de l'enregistrement du code et du pseudo : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Génération du Code</title>
</head>
<body>

<h1>Génération du Code</h1>

<!-- Formulaire pour choisir le pseudo -->
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="pseudo">Choisissez votre pseudo :</label>
    <input type="text" id="pseudo" name="pseudo" required>
    
    <button type="submit">Générer le Code</button>
</form>

</body>
</html>

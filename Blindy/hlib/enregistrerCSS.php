<?php
include 'db.php';

if(isset($_POST['couleurFond']) && isset($_POST['couleurTexteEquipe1']) && isset($_POST['couleurTexteEquipe2'])) {
    $couleurFond = $_POST['couleurFond'];
    $couleurTexteEquipe1 = $_POST['couleurTexteEquipe1'];
    $couleurTexteEquipe2 = $_POST['couleurTexteEquipe2'];

    // Mettre à jour les préférences de l'utilisateur dans la base de données
    $utilisateur_id = 1; // Remplacez par l'identifiant du maître du jeu

    $sql = "UPDATE preferences SET couleur_fond = ?, couleur_texte_equipe1 = ?, couleur_texte_equipe2 = ? WHERE utilisateur_id = ?";
    
    // Utilisation de requêtes préparées pour éviter les injections SQL
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Liaison des paramètres
        $stmt->bind_param("sssi", $couleurFond, $couleurTexteEquipe1, $couleurTexteEquipe2, $utilisateur_id);

        // Exécution de la requête
        $stmt->execute();

        // Fermeture de la déclaration
        $stmt->close();

        echo "Styles CSS mis à jour avec succès.";
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }
} else {
    echo "Paramètres manquants.";
}

$conn->close();
?>

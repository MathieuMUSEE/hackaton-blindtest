<?php
include 'db.php';

// Fonction pour afficher les scores
function afficherScores() {
    global $conn;

    // Récupérer les préférences de l'utilisateur
    $preferences = recupererPreferences();

    // Assurez-vous que la connexion est établie
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM equipes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div id="equipe_' . $row['id'] . '" style="' . (isset($preferences['style_css']) ? $preferences['style_css'] : '') . '">';
            echo $row['nom_equipe'] . ": <span class='score' style='color: " . (isset($preferences['couleur_texte_equipe' . $row['id']]) ? $preferences['couleur_texte_equipe' . $row['id']] : '') . ";'>" . $row['score'] . "</span> points";
            echo '<button class="ajouterPoints" data-equipe="' . $row['id'] . '">+1 point</button>';
            echo '</div>';
        }
    } else {
        echo "Aucune équipe trouvée.";
    }
}

function afficherEquipesAvecJoueurs() {
    global $conn;

    // Récupérer les équipes avec les joueurs attribués
    $sql = "SELECT e.id as equipe_id, e.nom_equipe, f.pseudo FROM equipes e LEFT JOIN file_attente f ON e.id = f.equipe_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>Équipes avec Joueurs :</h2>";
        while ($row = $result->fetch_assoc()) {
            echo "<p>Équipe " . $row['nom_equipe'] . " : " . ($row['pseudo'] ? $row['pseudo'] : "Aucun joueur attribué") . "</p>";
        }
    } else {
        echo "<p>Aucune équipe avec joueur attribué.</p>";
    }
}

function afficherFileAttente() {
    global $conn;

    // Assurez-vous que la connexion est établie
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sqlFileAttente = "SELECT * FROM file_attente";
    $resultFileAttente = $conn->query($sqlFileAttente);

    if ($resultFileAttente->num_rows > 0) {
        echo "<h2>Joueurs en Attente :</h2>";
        echo "<ul>";
        while ($row = $resultFileAttente->fetch_assoc()) {
            echo "<li>" . $row['pseudo'] . " ";

            // Afficher une liste déroulante des équipes
            echo "<select class='equipeDropdown' data-joueur='" . $row['id'] . "'>";
            echo "<option value=''>Choisir une équipe</option>";
            afficherEquipesDansDropdown(); // Fonction à créer pour afficher les équipes
            echo "</select>";

            echo "<button class='attribuerEquipe' data-joueur='" . $row['id'] . "'>Attribuer Équipe</button></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Aucun joueur en attente.</p>";
    }
}

function afficherEquipesDansDropdown() {
    global $conn;

    $sqlEquipes = "SELECT * FROM equipes";
    $resultEquipes = $conn->query($sqlEquipes);

    while ($equipe = $resultEquipes->fetch_assoc()) {
        echo "<option value='" . $equipe['id'] . "'>" . $equipe['nom_equipe'] . "</option>";
    }
}

// Fonction pour récupérer les préférences de l'utilisateur
function recupererPreferences() {
    global $conn;

    // Assurez-vous que la connexion est établie
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Utilisateur_id peut être un identifiant unique pour le maître du jeu
    $utilisateur_id = 1; // Remplacez par l'identifiant du maître du jeu

    // Récupérer les préférences de l'utilisateur
    $sql = "SELECT * FROM preferences WHERE utilisateur_id = $utilisateur_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau de Scores</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
        .equipe {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .score {
            font-size: 18px;
            font-weight: bold;
        }

        .ajouterPoints {
            margin-left: 10px;
            padding: 5px 10px;
            cursor: pointer;
            background-color: #0066cc;
            color: #fff;
            border: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <button id="toggleFullScreen">Activer le mode plein écran</button>
    <!-- Interface pour la personnalisation du CSS -->
    <div id="cssCustomization">
        <h2>Personnalisation du CSS</h2>
        <form id="cssForm">
            <label for="couleurFond">Couleur de Fond:</label>
            <input type="color" id="couleurFond" name="couleurFond">

            <label for="couleurTexteEquipe1">Couleur du Texte - Équipe 1:</label>
            <input type="color" id="couleurTexteEquipe1" name="couleurTexteEquipe1">

            <label for="couleurTexteEquipe2">Couleur du Texte - Équipe 2:</label>
            <input type="color" id="couleurTexteEquipe2" name="couleurTexteEquipe2">

            <!-- Ajoutez d'autres champs de personnalisation selon vos besoins -->

            <button type="button" id="enregistrerCSS">Enregistrer</button>
        </form>
    </div>

    <div id="ajouterEquipe">
        <h2>Ajouter une Équipe</h2>
        <form id="equipeForm">
            <label for="nomEquipe">Nom de l'Équipe :</label>
            <input type="text" id="nomEquipe" name="nomEquipe" required>
            <button type="button" id="ajouterEquipeBtn">Ajouter</button>
        </form>
    </div>

    <!-- Afficher les joueurs en attente -->
    <div id="fileAttente">
        <?php afficherFileAttente(); ?>
    </div>

    <h1>Panneau de Scores</h1>

    <!-- Afficher les scores -->
    <div id="scores">
        <?php afficherScores(); ?>
    </div>

    <!-- Script jQuery pour ajouter des points en temps réel -->
    <script>
        $(document).ready(function () {
            $('.ajouterPoints').on('click', function () {
                var equipeId = $(this).data('equipe');

                // Appel AJAX pour mettre à jour les points
                $.ajax({
                    method: "POST",
                    url: "ajouterPoints.php", // Créez un fichier ajouterPoints.php pour gérer cette requête
                    data: { equipeId: equipeId, points: 1 },
                    success: function (response) {
                        // Mettre à jour le score affiché en temps réel
                        $('#equipe_' + equipeId + ' .score').text(response);
                    },
                    error: function (error) {
                        console.error("Erreur lors de la mise à jour des points : " + error);
                    }
                });
            });
        });

        $(document).ready(function () {
            $('#ajouterEquipeBtn').on('click', function () {
                var nomEquipe = $('#nomEquipe').val();

                // Appel AJAX pour ajouter une équipe
                $.ajax({
                    method: "POST",
                    url: "ajouterEquipe.php", // Créez un fichier ajouterEquipe.php pour gérer cette requête
                    data: { nomEquipe: nomEquipe },
                    success: function (response) {
                        // Rafraîchir la page pour afficher la nouvelle équipe
                        location.reload();
                    },
                    error: function (error) {
                        console.error("Erreur lors de l'ajout de l'équipe : " + error);
                    }
                });
            });
        });

        $(document).ready(function () {
            $('.attribuerEquipe').on('click', function () {
                var joueurId = $(this).data('joueur');
                var equipeId = $(this).siblings('.equipeDropdown').val();

                // Appel AJAX pour attribuer l'équipe au joueur
                $.ajax({
                    method: "POST",
                    url: "attribuerEquipe.php",
                    data: { joueurId: joueurId, equipeId: equipeId },
                    success: function (response) {
                        // Rafraîchir la page pour refléter les changements
                        location.reload();
                        // Afficher les équipes avec les joueurs attribués
                        afficherEquipesAvecJoueurs();
                    },
                    error: function (error) {
                        console.error("Erreur lors de l'attribution de l'équipe : " + error);
                    }
                });
            });
        });

        $(document).ready(function () {
            // ... Votre code actuel ...
        });

        // Fonction pour activer/désactiver le mode plein écran
        function toggleFullScreen() {
            var doc = window.document;
            var docEl = doc.documentElement;

            var requestFullScreen = docEl.requestFullscreen || docEl.mozRequestFullScreen || docEl.webkitRequestFullScreen || docEl.msRequestFullscreen;
            var cancelFullScreen = doc.exitFullscreen || doc.mozCancelFullScreen || doc.webkitExitFullscreen || doc.msExitFullscreen;

            if (!doc.fullscreenElement && !doc.mozFullScreenElement && !doc.webkitFullscreenElement && !doc.msFullscreenElement) {
                requestFullScreen.call(docEl);
            } else {
                cancelFullScreen.call(doc);
            }
        }

        document.getElementById('toggleFullScreen').addEventListener('click', toggleFullScreen);

        $('#enregistrerCSS').on('click', function () {
            // Récupérer les valeurs des champs de personnalisation du CSS
            var couleurFond = $('#couleurFond').val();
            var couleurTexteEquipe1 = $('#couleurTexteEquipe1').val();
            var couleurTexteEquipe2 = $('#couleurTexteEquipe2').val();

            // Ajoutez d'autres variables pour les champs supplémentaires

            // Appel AJAX pour enregistrer les préférences de CSS
            $.ajax({
                method: "POST",
                url: "enregistrerCSS.php",
                data: {
                    couleurFond: couleurFond,
                    couleurTexteEquipe1: couleurTexteEquipe1,
                    couleurTexteEquipe2: couleurTexteEquipe2
                    // Ajoutez d'autres données si nécessaire
                },
                success: function (response) {
                    console.log("Styles CSS enregistrés avec succès.");

                    // Mettre à jour les styles directement sur la page
                    var equipe1 = $('#equipe_1');
                    var equipe2 = $('#equipe_2');

                    equipe1.css('background-color', couleurFond);
                    equipe1.find('.score').css('color', couleurTexteEquipe1);

                    equipe2.css('background-color', couleurFond);
                    equipe2.find('.score').css('color', couleurTexteEquipe2);
                },
                error: function (error) {
                    console.error("Erreur lors de l'enregistrement des styles CSS : " + error);
                }
            });
        });
        afficherEquipesAvecJoueurs();
    </script>

</body>

</html>

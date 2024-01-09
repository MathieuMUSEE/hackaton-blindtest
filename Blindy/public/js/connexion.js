// Code PIN 
const expectedPin = "1234";

function checkPin() {
    // Récupérer la valeur du champ d'entrée
    const enteredPin = document.getElementById("pinInput").value;

    // Vérifie si le code PIN est correct
    if (enteredPin === expectedPin) {
        document.getElementById("message").textContent = "Code PIN correct. Accès autorisé.";
        // Pour rediriger vers la page du blindtest
    } else {
        document.getElementById("message").textContent = "Code PIN incorrect. Veuillez réessayer.";
    }
}



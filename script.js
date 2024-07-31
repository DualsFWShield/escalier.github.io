// Ajoute un événement au formulaire pour valider les entrées
document.querySelector('form').addEventListener('submit', function(event) {
    // Vérifie si les champs sont remplis
    if (document.querySelector('#player1_name').value === '' || document.querySelector('#player2_name').value === '' || document.querySelector('#plis_pronostiques').value === '') {
        alert('Veuillez remplir tous les champs');
        event.preventDefault();
    }
});

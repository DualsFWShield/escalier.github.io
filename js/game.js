// game.js

document.addEventListener('DOMContentLoaded', () => {
    let players = [];
    let currentRound = 0;
    const maxRounds = 20;

    function initializeGame() {
        // Load players from storage if available
        const storedData = localStorage.getItem('gameData');
        if (storedData) {
            const gameData = JSON.parse(storedData);
            players = gameData.players || [];
            currentRound = gameData.currentRound || 0;
        }

        updateUI();
    }

    function updateUI() {
        updatePlayerInputs();
        updateScoresTable();
        updateCurrentPlayer();
        document.getElementById('round-number').textContent = currentRound + 1;
    }

    function updatePlayerInputs() {
        const roundInputs = document.getElementById('round-inputs');
        roundInputs.innerHTML = '';

        players.forEach(player => {
            const container = document.createElement('div');
            container.innerHTML = `
                <label>${player.name} - Prédiction:</label>
                <input type="number" id="prediction-${player.id}" min="0" max="${getMaxPlisForRound()}" required>
                <label>${player.name} - Réel:</label>
                <input type="number" id="result-${player.id}" min="0" max="${getMaxPlisForRound()}" required>
            `;
            roundInputs.appendChild(container);
        });
    }

    function updateScoresTable() {
        const scoresTableBody = document.querySelector('#scores-table tbody');
        scoresTableBody.innerHTML = '';

        players.forEach(player => {
            const row = document.createElement('tr');
            row.innerHTML = `<td>${player.name}</td><td>${player.score || 0}</td>`;
            scoresTableBody.appendChild(row);
        });
    }

    function updateCurrentPlayer() {
        if (players.length > 0) {
            const currentPlayer = players[currentRound % players.length];
            document.getElementById('current-player').textContent = currentPlayer.name;
        }
    }

    function getMaxPlisForRound() {
        if (currentRound < 10) return currentRound + 1;
        return 20 - currentRound;
    }

    function calculatePoints() {
        players.forEach(player => {
            const prediction = parseInt(document.getElementById(`prediction-${player.id}`).value, 10);
            const result = parseInt(document.getElementById(`result-${player.id}`).value, 10);
            let points = result * 5; // Points for the number of plis won

            if (prediction === result) {
                points += 5; // Bonus for correct prediction
            } else {
                const difference = Math.abs(prediction - result);
                points -= (difference * 5) + 5; // Penalty for incorrect prediction
            }

            // Ensure minimum of 5 points per round
            if (points < 5) points = 5;

            if (!player.score) player.score = 0;
            player.score += points;
        });

        // Save game state
        localStorage.setItem('gameData', JSON.stringify({
            players: players,
            currentRound: currentRound
        }));

        updateUI();
    }

    function handleNextRound() {
        calculatePoints();
        if (currentRound < maxRounds - 1) {
            currentRound++;
            updateUI();
        } else {
            alert('La partie est terminée.');
        }
    }

    function handlePrevRound() {
        if (currentRound > 0) {
            currentRound--;
            updateUI();
        }
    }

    function handleExport() {
        const gameData = localStorage.getItem('gameData');
        if (gameData) {
            const blob = new Blob([gameData], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'game-data.json';
            a.click();
            URL.revokeObjectURL(url);
        }
    }

    function handleImport(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const data = JSON.parse(e.target.result);
                players = data.players || [];
                currentRound = data.currentRound || 0;
                updateUI();
            };
            reader.readAsText(file);
        }
    }

    document.getElementById('players-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const playersInput = document.getElementById('players-input').value;
        players = playersInput.split(',').map((name, index) => ({
            id: index,
            name: name.trim(),
            score: 0
        }));
        currentRound = 0;
        updateUI();
    });

    document.getElementById('submit-round').addEventListener('click', handleNextRound);
    document.getElementById('prev-round').addEventListener('click', handlePrevRound);
    document.getElementById('export-game-button').addEventListener('click', handleExport);
    document.getElementById('save-game-button').addEventListener('click', handleExport);
    document.getElementById('play-again-button').addEventListener('click', () => {
        localStorage.removeItem('gameData');
        location.reload();
    });

    document.getElementById('import-game-input').addEventListener('change', handleImport);

    // Initialize game
    initializeGame();
});

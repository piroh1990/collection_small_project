<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍞 Toast Panic</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>🍞 Toast Panic! 🔥</h1>
        <p>Schnapp dir das Brot, bevor es verbrennt!</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-label">Punkte</div>
            <div class="stat-value" id="score">0</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Highscore</div>
            <div class="stat-value" id="highscore">0</div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Level</div>
            <div class="stat-value" id="level">1</div>
        </div>
    </div>

    <div class="game-container">
        <canvas id="gameCanvas" width="800" height="600"></canvas>
        <div class="timer-bar">
            <div class="timer-fill" id="timerFill"></div>
        </div>
        <div class="game-over" id="gameOver">
            <h2>🔥 Verbrannt! 🔥</h2>
            <p id="finalScore"></p>
            <div class="save-score">
                <input type="text" id="playerNameSave" placeholder="Dein Name" maxlength="20">
                <button class="start-btn" onclick="saveScore()">Score speichern</button>
            </div>
            <button class="start-btn" onclick="startGame()">Nochmal spielen</button>
        </div>
    </div>

    <div class="controls">
        <button class="start-btn" onclick="startGame()">🎮 Spiel starten</button>
        <button class="start-btn" onclick="toggleSound()">🔊 Sound: <span id="soundStatus">AN</span></button>
    </div>

    <div class="skin-selector">
        <h3>🎨 Wähle dein Brot:</h3>
        <div class="skins">
            <div class="skin-option selected" data-skin="🍞" onclick="selectSkin('🍞')">🍞</div>
            <div class="skin-option" data-skin="🥖" onclick="selectSkin('🥖')">🥖</div>
            <div class="skin-option" data-skin="🥐" onclick="selectSkin('🥐')">🥐</div>
            <div class="skin-option" data-skin="🥨" onclick="selectSkin('🥨')">🥨</div>
            <div class="skin-option" data-skin="🥯" onclick="selectSkin('🥯')">🥯</div>
        </div>
    </div>

    <div class="highscores">
        <h3>🏆 Top 10 Highscores</h3>
        <ul class="score-list" id="scoreList">
            <li class="loading">Lade Highscores...</li>
        </ul>
    </div>

    <script src="game.js"></script>
</body>
</html>
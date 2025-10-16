const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

let gameState = {
    score: 0,
    level: 1,
    isPlaying: false,
    bread: { x: 400, y: 300, size: 80, burnLevel: 0 },
    timeLeft: 3000,
    maxTime: 3000,
    selectedSkin: '🍞',
    soundEnabled: true
};

let animationFrame;
let lastTime = Date.now();

// API Endpoint
const API_URL = 'api.php';

// Sound-Funktionen
function playSound(type) {
    if (!gameState.soundEnabled) return;
    
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioCtx.createOscillator();
    const gainNode = audioCtx.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioCtx.destination);
    
    if (type === 'click') {
        oscillator.frequency.value = 800;
        gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
        oscillator.start(audioCtx.currentTime);
        oscillator.stop(audioCtx.currentTime + 0.1);
    } else if (type === 'burn') {
        oscillator.frequency.value = 200;
        gainNode.gain.setValueAtTime(0.3, audioCtx.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
        oscillator.start(audioCtx.currentTime);
        oscillator.stop(audioCtx.currentTime + 0.5);
    } else if (type === 'levelup') {
        oscillator.frequency.value = 1000;
        gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
        oscillator.start(audioCtx.currentTime);
        oscillator.frequency.exponentialRampToValueAtTime(1500, audioCtx.currentTime + 0.2);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
        oscillator.stop(audioCtx.currentTime + 0.2);
    }
}

function toggleSound() {
    gameState.soundEnabled = !gameState.soundEnabled;
    document.getElementById('soundStatus').textContent = gameState.soundEnabled ? 'AN' : 'AUS';
}

// Skin-Auswahl
function selectSkin(skin) {
    gameState.selectedSkin = skin;
    document.querySelectorAll('.skin-option').forEach(el => {
        el.classList.remove('selected');
        if (el.dataset.skin === skin) {
            el.classList.add('selected');
        }
    });
}

// Brot zeichnen
function drawBread() {
    const { x, y, size, burnLevel } = gameState.bread;
    
    // Schatten
    ctx.fillStyle = 'rgba(0,0,0,0.3)';
    ctx.beginPath();
    ctx.ellipse(x, y + size/2 + 5, size/2 + 5, size/6 + 2, 0, 0, Math.PI * 2);
    ctx.fill();
    
    ctx.save();
    ctx.translate(x, y);
    
    // Weißer Hintergrund-Kreis für besseren Kontrast
    ctx.fillStyle = 'white';
    ctx.beginPath();
    ctx.arc(0, 0, size/1.5, 0, Math.PI * 2);
    ctx.fill();
    
    // Schwarzer Rand
    ctx.strokeStyle = '#2d3436';
    ctx.lineWidth = 3;
    ctx.stroke();
    
    ctx.font = `${size}px Arial`;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    
    // Wenn komplett verbrannt: nur Flamme
    if (burnLevel >= 1) {
        ctx.font = `${size * 1.5}px Arial`;
        ctx.fillText('🔥', 0, 0);
    } else {
        // Sonst: Brot anzeigen
        ctx.fillText(gameState.selectedSkin, 0, 0);
    }
    
    ctx.restore();
}

// Neue Position für Brot
function randomizeBreadPosition() {
    const margin = 80;
    gameState.bread.x = margin + Math.random() * (canvas.width - 2 * margin);
    gameState.bread.y = margin + Math.random() * (canvas.height - 2 * margin);
    gameState.bread.burnLevel = 0;
}

// Spiel starten
function startGame() {
    gameState.score = 0;
    gameState.level = 1;
    gameState.isPlaying = true;
    gameState.timeLeft = gameState.maxTime;
    gameState.bread.burnLevel = 0;
    
    document.getElementById('score').textContent = '0';
    document.getElementById('level').textContent = '1';
    document.getElementById('gameOver').style.display = 'none';
    
    randomizeBreadPosition();
    lastTime = Date.now();
    gameLoop();
}

// Haupt-Spielschleife
function gameLoop() {
    if (!gameState.isPlaying) return;
    
    const currentTime = Date.now();
    const deltaTime = currentTime - lastTime;
    lastTime = currentTime;
    
    // Zeit reduzieren
    gameState.timeLeft -= deltaTime;
    
    // Timer-Bar aktualisieren
    const timerPercent = Math.max(0, (gameState.timeLeft / gameState.maxTime) * 100);
    document.getElementById('timerFill').style.width = timerPercent + '%';
    
    // Verbrennungslevel erhöhen
    gameState.bread.burnLevel = Math.min(1, 1 - (gameState.timeLeft / gameState.maxTime));
    
    // Game Over wenn Zeit abgelaufen
    if (gameState.timeLeft <= 0) {
        endGame();
        return;
    }
    
    // Canvas leeren und neu zeichnen
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBread();
    
    animationFrame = requestAnimationFrame(gameLoop);
}

// Spiel beenden
function endGame() {
    gameState.isPlaying = false;
    playSound('burn');
    
    document.getElementById('finalScore').textContent = 
        `Dein Score: ${gameState.score} Punkte (Level ${gameState.level})`;
    document.getElementById('gameOver').style.display = 'block';
    document.querySelector('.game-container').classList.add('shake');
    
    setTimeout(() => {
        document.querySelector('.game-container').classList.remove('shake');
    }, 300);
    
    cancelAnimationFrame(animationFrame);
}

// Score speichern
async function saveScore() {
    const playerName = document.getElementById('playerNameSave').value.trim();
    
    if (!playerName) {
        alert('Bitte gib deinen Namen ein!');
        return;
    }
    
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'saveScore',
                name: playerName,
                score: gameState.score,
                level: gameState.level
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Score gespeichert! 🎉');
            document.getElementById('playerNameSave').value = '';
            loadHighscores();
        } else {
            alert('Fehler beim Speichern: ' + result.message);
        }
    } catch (error) {
        alert('Verbindungsfehler zum Server!');
        console.error(error);
    }
}

// Canvas-Klick
canvas.addEventListener('click', (e) => {
    if (!gameState.isPlaying) return;
    
    const rect = canvas.getBoundingClientRect();
    const clickX = e.clientX - rect.left;
    const clickY = e.clientY - rect.top;
    
    const distance = Math.sqrt(
        Math.pow(clickX - gameState.bread.x, 2) + 
        Math.pow(clickY - gameState.bread.y, 2)
    );
    
    if (distance < gameState.bread.size / 2) {
        // Treffer!
        gameState.score += Math.ceil(10 * gameState.level);
        document.getElementById('score').textContent = gameState.score;
        
        playSound('click');
        
        // Level erhöhen alle 5 Treffer
        if (gameState.score % 50 === 0) {
            gameState.level++;
            gameState.maxTime = Math.max(1000, gameState.maxTime - 200);
            document.getElementById('level').textContent = gameState.level;
            playSound('levelup');
        }
        
        // Neues Brot
        randomizeBreadPosition();
        gameState.timeLeft = gameState.maxTime;
    }
});

// Highscores laden
async function loadHighscores() {
    try {
        const response = await fetch(API_URL + '?action=getHighscores');
        const result = await response.json();
        
        const list = document.getElementById('scoreList');
        list.innerHTML = '';
        
        if (result.success && result.data.length > 0) {
            result.data.forEach((entry, index) => {
                const li = document.createElement('li');
                li.className = 'score-item';
                li.innerHTML = `
                    <span class="score-rank">#${index + 1}</span>
                    <div class="score-info">
                        <div class="score-name">${entry.player_name}</div>
                        <div class="score-details">Level ${entry.level} • ${entry.created_at}</div>
                    </div>
                    <span class="score-value">${entry.score}</span>
                `;
                list.appendChild(li);
            });
            
            // Aktualisiere Highscore-Anzeige
            document.getElementById('highscore').textContent = result.data[0].score;
        } else {
            list.innerHTML = '<div class="loading">Noch keine Highscores vorhanden</div>';
        }
    } catch (error) {
        document.getElementById('scoreList').innerHTML = 
            '<div class="error">Fehler beim Laden der Highscores</div>';
        console.error(error);
    }
}

// Initialisierung
drawBread();
loadHighscores();
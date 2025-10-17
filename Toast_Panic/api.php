<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// GET Requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'getHighscores') {
        $conn = getDBConnection();
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Datenbankverbindung fehlgeschlagen']);
            exit;
        }
        
        try {
            $stmt = $conn->prepare("
                SELECT player_name, score, level, DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') as created_at 
                FROM highscores 
                ORDER BY score DESC 
                LIMIT 10
            ");
            $stmt->execute();
            $scores = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $scores]);
        } catch(PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Ungültige Aktion']);
    }
}

// POST Requests
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Ungültige JSON-Daten']);
        exit;
    }
    
    $action = $data['action'] ?? '';
    
    if ($action === 'saveScore') {
        $name = trim($data['name'] ?? '');
        $score = intval($data['score'] ?? 0);
        $level = intval($data['level'] ?? 0);
        
        // Validierung
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Name darf nicht leer sein']);
            exit;
        }
        
        if (strlen($name) > 50) {
            echo json_encode(['success' => false, 'message' => 'Name ist zu lang']);
            exit;
        }
        
        if ($score < 0 || $level < 0) {
            echo json_encode(['success' => false, 'message' => 'Ungültige Werte']);
            exit;
        }
        
        // XSS-Schutz
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        
        $conn = getDBConnection();
        if (!$conn) {
            echo json_encode(['success' => false, 'message' => 'Datenbankverbindung fehlgeschlagen']);
            exit;
        }
        
        try {
            $stmt = $conn->prepare("
                INSERT INTO brot_highscores (player_name, score, level) 
                VALUES (:name, :score, :level)
            ");
            $stmt->execute([
                ':name' => $name,
                ':score' => $score,
                ':level' => $level
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Score gespeichert', 'id' => $conn->lastInsertId()]);
        } catch(PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern des Scores']);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Ungültige Aktion']);
    }
}
else {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfragemethode']);
}
?>
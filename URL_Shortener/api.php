<?php
require_once 'conf.php';

// Database connection
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $db;
}

// Generate random short code
function generateShortCode($length = 6) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $code;
}

// Validate URL
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// API Routes
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'shorten':
        shortenUrl();
        break;
    case 'stats':
        getStats();
        break;
    case 'admin_urls':
        getAdminUrls();
        break;
    case 'delete_url':
        deleteUrl();
        break;
    default:
        redirect();
        break;
}

function shortenUrl() {
    header('Content-Type: application/json');

    $url = trim($_POST['url'] ?? '');
    $alias = trim($_POST['alias'] ?? '');
    $title = trim($_POST['title'] ?? '');

    if (empty($url) || !isValidUrl($url)) {
        echo json_encode(['success' => false, 'error' => 'Invalid URL']);
        exit;
    }

    if (strlen($url) > MAX_URL_LENGTH) {
        echo json_encode(['success' => false, 'error' => 'URL too long']);
        exit;
    }

    $db = getDB();

    // Check if custom alias is available
    if (!empty($alias)) {
        $stmt = $db->prepare("SELECT id FROM shortener_urls WHERE custom_alias = ?");
        $stmt->execute([$alias]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Custom alias already taken']);
            exit;
        }
    }

    // Generate short code if no custom alias
    $shortCode = $alias ?: generateShortCode();

    // Make sure short code is unique
    if (empty($alias)) {
        $stmt = $db->prepare("SELECT id FROM shortener_urls WHERE short_code = ?");
        $stmt->execute([$shortCode]);
        while ($stmt->fetch()) {
            $shortCode = generateShortCode();
            $stmt->execute([$shortCode]);
        }
    }

    // Insert URL
    $stmt = $db->prepare("
        INSERT INTO shortener_urls (short_code, original_url, custom_alias, title, created_by_ip)
        VALUES (?, ?, ?, ?, ?)
    ");

    try {
        $stmt->execute([
            $shortCode,
            $url,
            $alias ?: null,
            $title ?: null,
            $_SERVER['REMOTE_ADDR']
        ]);

        $urlId = $db->lastInsertId();
        $shortUrl = BASE_URL . '/' . $shortCode;

        echo json_encode([
            'success' => true,
            'short_url' => $shortUrl,
            'short_code' => $shortCode,
            'url_id' => $urlId
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Failed to create short URL']);
    }
}

function redirect() {
    $code = trim($_SERVER['REQUEST_URI'], '/');

    if (empty($code)) {
        header('Location: /');
        exit;
    }

    $db = getDB();
    $stmt = $db->prepare("
        SELECT id, original_url, is_active FROM shortener_urls
        WHERE (short_code = ? OR custom_alias = ?) AND is_active = 1
    ");
    $stmt->execute([$code, $code]);
    $url = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$url) {
        http_response_code(404);
        echo "Short URL not found";
        exit;
    }

    // Track click
    $stmt = $db->prepare("
        INSERT INTO shortener_clicks (url_id, ip_address, user_agent, referrer)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $url['id'],
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_REFERER'] ?? ''
    ]);

    // Update click count
    $stmt = $db->prepare("
        UPDATE shortener_urls SET click_count = click_count + 1, last_clicked = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$url['id']]);

    // Redirect
    header('Location: ' . $url['original_url']);
    exit;
}

function getStats() {
    header('Content-Type: application/json');

    $code = $_GET['code'] ?? '';
    if (empty($code)) {
        echo json_encode(['success' => false, 'error' => 'No code provided']);
        exit;
    }

    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.*, COUNT(c.id) as total_clicks,
               DATE_FORMAT(u.created_at, '%Y-%m-%d %H:%i') as created_date
        FROM shortener_urls u
        LEFT JOIN shortener_clicks c ON u.id = c.url_id
        WHERE u.short_code = ? OR u.custom_alias = ?
        GROUP BY u.id
    ");
    $stmt->execute([$code, $code]);
    $url = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$url) {
        echo json_encode(['success' => false, 'error' => 'URL not found']);
        exit;
    }

    // Get click data for chart
    $stmt = $db->prepare("
        SELECT DATE(clicked_at) as date, COUNT(*) as clicks
        FROM shortener_clicks
        WHERE url_id = ?
        GROUP BY DATE(clicked_at)
        ORDER BY date DESC
        LIMIT 30
    ");
    $stmt->execute([$url['id']]);
    $clickData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'url' => $url,
        'click_data' => $clickData
    ]);
}

function getAdminUrls() {
    header('Content-Type: application/json');

    $token = $_GET['token'] ?? '';
    if ($token !== ADMIN_TOKEN) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.*, COUNT(c.id) as total_clicks
        FROM shortener_urls u
        LEFT JOIN shortener_clicks c ON u.id = c.url_id
        GROUP BY u.id
        ORDER BY u.created_at DESC
        LIMIT 100
    ");
    $stmt->execute();
    $urls = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'urls' => $urls]);
}

function deleteUrl() {
    header('Content-Type: application/json');

    $token = $_POST['token'] ?? '';
    $urlId = $_POST['url_id'] ?? '';

    if ($token !== ADMIN_TOKEN) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    if (empty($urlId)) {
        echo json_encode(['success' => false, 'error' => 'No URL ID provided']);
        exit;
    }

    $db = getDB();
    $stmt = $db->prepare("DELETE FROM shortener_urls WHERE id = ?");
    $stmt->execute([$urlId]);

    echo json_encode(['success' => true]);
}
?>
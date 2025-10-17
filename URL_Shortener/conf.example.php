<?php
// URL Shortener Configuration
// Copy this file to conf.php and fill in your settings

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'url_shortener');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Application Settings
define('BASE_URL', 'https://yourdomain.com'); // Your domain for short URLs
define('SITE_NAME', 'URL Shortener');
define('ADMIN_TOKEN', 'yxtbfkisudhfkaiushdf'); // Change this for security

// Features
define('ENABLE_QR_CODES', true);
define('ENABLE_ANALYTICS', true);
define('ENABLE_CUSTOM_ALIASES', true);
define('MAX_URL_LENGTH', 2048);
define('DEFAULT_EXPIRY_DAYS', 365); // Default expiry in days

// Security
define('RATE_LIMIT_REQUESTS', 100); // Requests per hour per IP
define('RATE_LIMIT_WINDOW', 3600); // Window in seconds

// Optional: External Services
// define('IP_GEOLOCATION_API', 'https://ipapi.co/{ip}/json/'); // For location tracking
?>
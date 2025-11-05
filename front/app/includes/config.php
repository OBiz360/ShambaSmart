<?php
// config.php - Database configuration and connection
session_start();

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'shambasmart');

// Site settings
define('SITE_NAME', 'ShambaSmart');
define('SITE_URL', 'http://localhost/shambasmart');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Create database connection
try {
    $conn = new PDO(
    "mysql:host=" . DB_HOST . ";port=3308;dbname=" . DB_NAME,
    DB_USER,
    DB_PASS,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Helper function to redirect
function redirect($page) {
    header("Location: " . SITE_URL . "/" . $page);
    exit();
}

// Helper function to sanitize input
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Helper function for success messages
function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Helper function to display messages
function displayMessage() {
    if(isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'success';
        $message = $_SESSION['message'];
        echo "<div class='alert alert-{$type}'>{$message}</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}
?>
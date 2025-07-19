<?php
/**
 * Configuration file for the Graduate Management System
 * Loads environment variables and sets application constants
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

// Load environment variables
loadEnv(__DIR__ . '/../.env');

// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'graduate_system');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');

// Application configuration
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Graduate Management System');
define('APP_URL', $_ENV['APP_URL'] ?? 'http://localhost');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');

// Security configuration
define('SESSION_NAME', $_ENV['SESSION_NAME'] ?? 'graduate_session');
define('SESSION_LIFETIME', $_ENV['SESSION_LIFETIME'] ?? 3600);

// Path configuration
define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_PATH', APP_ROOT . '/public');
define('APP_PATH', APP_ROOT . '/app');
define('VIEW_PATH', APP_PATH . '/views');

// Error reporting based on environment
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
session_name(SESSION_NAME);
session_set_cookie_params(SESSION_LIFETIME);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
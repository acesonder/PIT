<?php
/**
 * Database Configuration
 * Update these settings with your MySQL credentials
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pit_count');
define('DB_CHARSET', 'utf8mb4');

/**
 * Admin Configuration
 */
define('ADMIN_PASSCODE', '079777');

/**
 * Site Configuration
 */
define('SITE_TITLE', 'Point-in-Time Count - Cobourg & Northumberland');
define('TIMEZONE', 'America/Toronto');

/**
 * File Upload Configuration
 */
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

/**
 * Session Configuration
 */
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

/**
 * Error Reporting (set to 0 in production)
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Set timezone
 */
date_default_timezone_set(TIMEZONE);

/**
 * Database Connection
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserializing
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Helper function to get database connection
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Security: Start session if not already started
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

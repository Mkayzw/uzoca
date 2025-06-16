<?php
// Enhanced database configuration for Render deployment
// Supports both MySQL and PostgreSQL

// Get database configuration from environment variables
$db_host = $_ENV['DB_HOST'] ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? 'root';
$db_pass = $_ENV['DB_PASS'] ?? '';
$db_name = $_ENV['DB_NAME'] ?? 'uzoca';
$db_port = $_ENV['DB_PORT'] ?? '3306';

// Parse DATABASE_URL if provided (common on Render)
if (isset($_ENV['DATABASE_URL']) && !empty($_ENV['DATABASE_URL'])) {
    $db_url = parse_url($_ENV['DATABASE_URL']);
    
    $db_host = $db_url['host'] ?? $db_host;
    $db_user = $db_url['user'] ?? $db_user;
    $db_pass = $db_url['pass'] ?? $db_pass;
    $db_name = ltrim($db_url['path'] ?? $db_name, '/');
    $db_port = $db_url['port'] ?? $db_port;
    
    // Determine database type from scheme
    $db_type = $db_url['scheme'] ?? 'mysql';
} else {
    // Default to MySQL
    $db_type = 'mysql';
}

// Define constants
define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);
define('DB_PORT', $db_port);
define('DB_TYPE', $db_type);

// Create database connection based on type
try {
    if ($db_type === 'postgres' || $db_type === 'postgresql') {
        // PostgreSQL connection using PDO
        $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        
        // For compatibility, also create a mysqli-like wrapper
        $conn = new class($pdo) {
            private $pdo;
            public $connect_error = null;
            
            public function __construct($pdo) {
                $this->pdo = $pdo;
            }
            
            public function query($sql) {
                try {
                    return $this->pdo->query($sql);
                } catch (PDOException $e) {
                    error_log("Database query error: " . $e->getMessage());
                    return false;
                }
            }
            
            public function prepare($sql) {
                return $this->pdo->prepare($sql);
            }
            
            public function close() {
                $this->pdo = null;
            }
            
            public function real_escape_string($string) {
                return $this->pdo->quote($string);
            }
        };
        
    } else {
        // MySQL connection using MySQLi
        $conn = new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("MySQL connection failed: " . $conn->connect_error);
        }
        
        // Set charset
        $conn->set_charset("utf8mb4");
    }
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    
    // Create a mock connection object for graceful degradation
    $conn = new class($e->getMessage()) {
        public $connect_error;
        
        public function __construct($error) {
            $this->connect_error = $error;
        }
        
        public function query($sql) { return false; }
        public function prepare($sql) { return false; }
        public function close() { }
        public function real_escape_string($string) { return $string; }
    };
}

// Helper function to execute queries with error handling
function db_query($sql, $params = []) {
    global $conn;
    
    if ($conn->connect_error) {
        return false;
    }
    
    if (empty($params)) {
        return $conn->query($sql);
    } else {
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->execute($params);
            return $stmt;
        }
        return false;
    }
}
?>

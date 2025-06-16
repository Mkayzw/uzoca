<?php
namespace app\assets;

class DB {
    private static $instance = null;
    private $conn = null;
    private $lastError = '';
    private $lastQuery = '';

    private function __construct() {
        try {
            $this->conn = new \mysqli('localhost', 'root', '', 'uzoca');
            if ($this->conn->connect_error) {
                throw new \Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Database connection error: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function select($columns, $table, $where = "", ...$params) {
        try {
            $query = "SELECT $columns FROM $table";
            if (!empty($where)) {
                $query .= " $where";
            }
            
            $this->lastQuery = $query;
            $stmt = $this->conn->prepare($query);
            
            if ($stmt === false) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }

            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                if (!$stmt->bind_param($types, ...$params)) {
                    throw new \Exception("Bind failed: " . $stmt->error);
                }
            }

            if (!$stmt->execute()) {
                throw new \Exception("Execute failed: " . $stmt->error);
            }

            return $stmt->get_result();
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Database error: " . $e->getMessage());
            throw $e;
        }
    }

    public function prepare($query, $types = "", ...$params) {
        $this->lastQuery = $query;
        
        // Prepare the statement
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            $error = $this->conn->error;
            $this->lastError = "Prepare failed: " . $error;
            error_log("Database error: " . $this->lastError);
            throw new \Exception($this->lastError);
        }

        // Bind parameters if any
        if (!empty($params)) {
            if (!$stmt->bind_param($types, ...$params)) {
                $error = $stmt->error;
                $this->lastError = "Bind failed: " . $error;
                error_log("Database error: " . $this->lastError);
                throw new \Exception($this->lastError);
            }
        }

        // For SELECT queries, execute and return the result
        if (stripos($query, 'SELECT') === 0) {
            if (!$stmt->execute()) {
                $error = $stmt->error;
                $this->lastError = "Execute failed: " . $error;
                error_log("Database error: " . $this->lastError);
                throw new \Exception($this->lastError);
            }
            return $stmt->get_result();
        }
        
        // For other queries (INSERT, UPDATE, DELETE), execute and return true if successful
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $this->lastError = "Execute failed: " . $error;
            error_log("Database error: " . $this->lastError);
            throw new \Exception($this->lastError);
        }
        return true;
    }

    public function query($query) {
        try {
            $this->lastQuery = $query;
            $result = $this->conn->query($query);
            if (!$result) {
                throw new \Exception("Query failed: " . $this->conn->error);
            }
            return $result;
        } catch (\Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Database error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getError() {
        return $this->lastError;
    }

    public function getLastQuery() {
        return $this->lastQuery;
    }

    public function lastID() {
        return $this->conn->insert_id;
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
} 
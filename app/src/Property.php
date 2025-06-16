<?php
namespace app\src;

use PDO;

class Property {
    private $conn;

    public function __construct() {
        $this->conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllProperties() {
        $query = "SELECT p.*, 
                        u.name as agent_name,
                        u.profile_pic as agent_image
                 FROM properties p
                 LEFT JOIN users u ON p.agent_id = u.id
                 WHERE p.status = 'active'
                 ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPropertyById($id) {
        $query = "SELECT p.*, 
                        u.name as agent_name,
                        u.profile_pic as agent_image,
                        u.email as agent_email,
                        u.phone as agent_phone
                 FROM properties p
                 LEFT JOIN users u ON p.agent_id = u.id
                 WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPropertiesByAgent($agentId) {
        $query = "SELECT * FROM properties WHERE agent_id = :agent_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":agent_id", $agentId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPropertiesByCategory($category) {
        $query = "SELECT p.*, 
                        u.name as agent_name,
                        u.profile_pic as agent_image
                 FROM properties p
                 LEFT JOIN users u ON p.agent_id = u.id
                 WHERE p.category = :category AND p.status = 'active'
                 ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category", $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchProperties($searchTerm) {
        $query = "SELECT p.*, 
                        u.name as agent_name,
                        u.profile_pic as agent_image
                 FROM properties p
                 LEFT JOIN users u ON p.agent_id = u.id
                 WHERE (p.title LIKE :search OR p.description LIKE :search OR p.location LIKE :search)
                 AND p.status = 'active'
                 ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$searchTerm}%";
        $stmt->bindParam(":search", $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filterProperties($category = null, $minPrice = null, $maxPrice = null) {
        $query = "SELECT p.*, 
                        u.name as agent_name,
                        u.profile_pic as agent_image
                 FROM properties p
                 LEFT JOIN users u ON p.agent_id = u.id
                 WHERE p.status = 'active'";
        
        $params = [];
        
        if ($category) {
            $query .= " AND p.category = :category";
            $params[':category'] = $category;
        }
        
        if ($minPrice !== null) {
            $query .= " AND p.price >= :min_price";
            $params[':min_price'] = $minPrice;
        }
        
        if ($maxPrice !== null) {
            $query .= " AND p.price <= :max_price";
            $params[':max_price'] = $maxPrice;
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 
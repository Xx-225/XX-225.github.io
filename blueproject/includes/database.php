<?php
class Database {
    private $conn = null;
    
    public function __construct() {
        $this->conn = new mysqli("localhost", "root", "root", "portfolio_db");
        
        if ($this->conn->connect_error) {
            $this->conn = null;
            return;
        }
        
        $this->conn->set_charset("utf8mb4");
    }
    
    public function query($sql, $params = []) {
        if ($this->conn === null) {
            return false;
        }
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    // 添加 insert 方法
    public function insert($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if (!$stmt) {
            return false;
        }
        
        $insertId = $stmt->insert_id;
        $stmt->close();
        
        return $insertId;
    }
    
    // 添加 update 方法
    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if (!$stmt) {
            return false;
        }
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        return $affectedRows;
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if (!$stmt) {
            return [];
        }
        
        $result = $stmt->get_result();
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        $stmt->close();
        return $rows;
    }
    
    public function fetchOne($sql, $params = []) {
        $rows = $this->fetchAll($sql, $params);
        return $rows[0] ?? null;
    }
}
?>
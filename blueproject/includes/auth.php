<?php
require_once 'database.php';

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function register($username, $email, $password, $full_name = '') {
        // 验证用户名是否存在
        $checkSql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $existing = $this->db->fetchOne($checkSql, [$username, $email]);
        
        if ($existing) {
            return ['success' => false, 'message' => '用户名或邮箱已存在'];
        }
        
        // 哈希密码
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // 插入用户
        $sql = "INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)";
        $userId = $this->db->insert($sql, [$username, $email, $hashedPassword, $full_name]);
        
        if ($userId) {
            $this->login($username, $password);
            return ['success' => true, 'message' => '注册成功'];
        }
        
        return ['success' => false, 'message' => '注册失败'];
    }
    
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $user = $this->db->fetchOne($sql, [$username, $username]);
        
        if (!$user || !password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => '用户名或密码错误'];
        }
        
        // 设置会话
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        
        return ['success' => true, 'message' => '登录成功'];
    }
    
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => '已退出登录'];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->fetchOne($sql, [$_SESSION['user_id']]);
    }
    
    public function updateProfile($data) {
        if (!$this->isLoggedIn()) {
            return ['success' => false, 'message' => '请先登录'];
        }
        
        $userId = $_SESSION['user_id'];
        $fields = [];
        $params = [];
        
        if (isset($data['full_name'])) {
            $fields[] = "full_name = ?";
            $params[] = $data['full_name'];
        }
        
        if (isset($data['bio'])) {
            $fields[] = "bio = ?";
            $params[] = $data['bio'];
        }
        
        if (isset($data['avatar'])) {
            $fields[] = "avatar = ?";
            $params[] = $data['avatar'];
        }
        
        if (!empty($fields)) {
            $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
            $params[] = $userId;
            
            $result = $this->db->update($sql, $params);
            
            if ($result) {
                // 更新会话中的用户信息
                if (isset($data['full_name'])) {
                    $_SESSION['full_name'] = $data['full_name'];
                }
                return ['success' => true, 'message' => '资料更新成功'];
            }
        }
        
        return ['success' => false, 'message' => '更新失败'];
    }
}
?>
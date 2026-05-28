<?php
// install.php - 数据库安装脚本
$password = 'root'; // 改为您测试出的密码

echo "<h2>蓝调作品集 - 数据库安装向导</h2>";

try {
    // 连接MySQL
    $conn = new mysqli('localhost', 'root', $password);
    
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }
    
    echo "✅ 连接到MySQL成功<br>";
    
    // 创建数据库
    $sql = "CREATE DATABASE IF NOT EXISTS portfolio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql) === TRUE) {
        echo "✅ 数据库 portfolio_db 创建成功<br>";
    } else {
        echo "⚠️ 数据库已存在或创建失败: " . $conn->error . "<br>";
    }
    
    // 选择数据库
    $conn->select_db('portfolio_db');
    
    // 创建用户表
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        avatar VARCHAR(255) DEFAULT 'default-avatar.png',
        bio TEXT,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ 用户表创建成功<br>";
    } else {
        echo "❌ 用户表创建失败: " . $conn->error . "<br>";
    }
    
    // 创建作品表
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(200) NOT NULL,
        description TEXT,
        category ENUM('design', 'development', 'photography', 'art', 'other') DEFAULT 'other',
        price DECIMAL(10, 2) DEFAULT 0.00,
        thumbnail VARCHAR(255),
        file_path VARCHAR(255) NOT NULL,
        file_size INT,
        file_type VARCHAR(50),
        downloads INT DEFAULT 0,
        status ENUM('draft', 'published', 'sold', 'archived') DEFAULT 'draft',
        is_featured BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ 作品表创建成功<br>";
    } else {
        echo "❌ 作品表创建失败: " . $conn->error . "<br>";
    }
    
    // 插入测试数据
    $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
    
    $sql = "INSERT IGNORE INTO users (username, email, password, full_name, role) VALUES 
            ('admin', 'admin@portfolio.com', '$hashed_password', '系统管理员', 'admin'),
            ('user1', 'user1@test.com', '$hashed_password', '测试用户', 'user')";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ 测试用户创建成功<br>";
        echo "🔐 管理员账号: admin / password123<br>";
        echo "👤 用户账号: user1 / password123<br>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 安装完成！</h3>";
    echo "<p>请确保 config.php 中的密码设置为: <code>'$password'</code></p>";
    echo "<a href='index.php'>前往首页</a>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ 安装失败: " . $e->getMessage();
}
?>
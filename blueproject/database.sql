-- 创建数据库
CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

-- 用户表
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    avatar VARCHAR(255) DEFAULT 'default-avatar.png',
    bio TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 作品表
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
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
);

-- 购物车表
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
);

-- 订单表
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 订单详情表
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 下载记录表
CREATE TABLE downloads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    order_id INT,
    downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- 插入测试用户
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@portfolio.com', '$2y$10$N9qo8uLOickgx2ZMRZoMy.MrqO3t3eHl3J6Vq7Q8LkY7bP6Q2J2Xa', '系统管理员', 'admin'),
('user1', 'user1@test.com', '$2y$10$N9qo8uLOickgx2ZMRZoMy.MrqO3t3eHl3J6Vq7Q8LkY7bP6Q2J2Xa', '测试用户1', 'user');

-- 插入测试作品
INSERT INTO products (user_id, title, description, category, price, thumbnail, file_path, status) VALUES
(1, '响应式网站设计', '一个现代化的响应式网站设计，采用最新的设计趋势和技术', 'design', 99.99, 'design1.jpg', 'design1.zip', 'published'),
(1, '电商平台开发', '完整的电商平台前后端开发，包含支付、订单管理等功能', 'development', 199.99, 'dev1.jpg', 'dev1.zip', 'published'),
(2, '风景摄影集', '精选的高清风景摄影作品，适合用作壁纸或设计素材', 'photography', 29.99, 'photo1.jpg', 'photo1.zip', 'published');
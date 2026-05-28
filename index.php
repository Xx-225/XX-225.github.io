<?php
require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/auth.php';

$auth = new Auth();
$db = new Database();

// 获取热门作品
$featuredProducts = $db->fetchAll(
    "SELECT p.*, u.username, u.full_name 
     FROM products p 
     JOIN users u ON p.user_id = u.id 
     WHERE p.status = 'published' 
     ORDER BY p.created_at DESC 
     LIMIT 6"
);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - 创意作品展示平台</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <i class="fas fa-palette"></i>
                <?php echo SITE_NAME; ?>
            </a>
            
            <ul class="nav-menu">
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> 首页</a></li>
                <li><a href="portfolio.php"><i class="fas fa-th-large"></i> 作品集</a></li>
                <li><a href="#categories"><i class="fas fa-tags"></i> 分类</a></li>
                
                <?php if ($auth->isLoggedIn()): ?>
                    <li><a href="dashboard.php"><i class="fas fa-user-circle"></i> 控制台</a></li>
                    <?php if ($auth->isAdmin()): ?>
                        <li><a href="admin/"><i class="fas fa-cog"></i> 管理</a></li>
                    <?php endif; ?>
                    <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> 购物车</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> 退出</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> 登录</a></li>
                    <li><a href="register.php" class="btn btn-outline"><i class="fas fa-user-plus"></i> 注册</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- 英雄区域 -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>展示您的创意，连接全球买家</h1>
                <p>一个专为设计师、开发者、艺术家打造的作品展示与交易平台</p>
                <div class="hero-buttons">
                    <?php if ($auth->isLoggedIn()): ?>
                        <a href="upload.php" class="btn btn-primary btn-large">
                            <i class="fas fa-cloud-upload-alt"></i> 上传作品
                        </a>
                        <a href="portfolio.php" class="btn btn-outline btn-large">
                            <i class="fas fa-eye"></i> 浏览作品
                        </a>
                    <?php else: ?>
                        <a href="register.php" class="btn btn-primary btn-large">
                            <i class="fas fa-user-plus"></i> 免费注册
                        </a>
                        <a href="portfolio.php" class="btn btn-outline btn-large">
                            <i class="fas fa-eye"></i> 探索作品
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- 特色作品 -->
    <section class="featured-products">
        <div class="container">
            <h2 class="section-title">精选作品</h2>
            <div class="products-grid">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo $product['thumbnail'] ? 'uploads/' . $product['thumbnail'] : 'assets/images/default-product.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php if ($product['price'] > 0): ?>
                            <span class="price-tag">¥<?php echo number_format($product['price'], 2); ?></span>
                        <?php else: ?>
                            <span class="price-tag free">免费</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="author">by <?php echo htmlspecialchars($product['full_name'] ?: $product['username']); ?></p>
                        <p class="description"><?php echo mb_substr($product['description'], 0, 100) . '...'; ?></p>
                        <div class="product-meta">
                            <span><i class="fas fa-download"></i> <?php echo $product['downloads']; ?> 下载</span>
                            <span><i class="fas fa-calendar"></i> <?php echo date('Y-m-d', strtotime($product['created_at'])); ?></span>
                        </div>
                        <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline">查看详情</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 分类展示 -->
    <section class="categories" id="categories">
        <div class="container">
            <h2 class="section-title">作品分类</h2>
            <div class="categories-grid">
                <a href="portfolio.php?category=design" class="category-card">
                    <i class="fas fa-paint-brush"></i>
                    <h3>设计作品</h3>
                    <p>UI/UX、平面、插画设计</p>
                </a>
                <a href="portfolio.php?category=development" class="category-card">
                    <i class="fas fa-code"></i>
                    <h3>开发项目</h3>
                    <p>网站、应用、脚本代码</p>
                </a>
                <a href="portfolio.php?category=photography" class="category-card">
                    <i class="fas fa-camera"></i>
                    <h3>摄影作品</h3>
                    <p>人像、风景、商业摄影</p>
                </a>
                <a href="portfolio.php?category=art" class="category-card">
                    <i class="fas fa-palette"></i>
                    <h3>艺术作品</h3>
                    <p>绘画、数字艺术、3D建模</p>
                </a>
            </div>
        </div>
    </section>

    <!-- 功能特点 -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">平台特色</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>安全可靠</h3>
                    <p>SSL加密传输，确保您的作品和交易安全</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-bolt"></i>
                    <h3>快速上传</h3>
                    <p>支持大文件上传，上传速度稳定快速</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>数据分析</h3>
                    <p>详细的下载和销售数据分析报告</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-headset"></i>
                    <h3>专业支持</h3>
                    <p>7×24小时客服支持，解决问题更高效</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 页脚 -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p>连接创意与商业，让每一份才华都得到价值体现</p>
                </div>
                <div class="footer-section">
                    <h4>快速链接</h4>
                    <ul>
                        <li><a href="index.php">首页</a></li>
                        <li><a href="portfolio.php">作品集</a></li>
                        <li><a href="dashboard.php">控制台</a></li>
                        <li><a href="contact.php">联系我们</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>联系我们</h4>
                    <p><i class="fas fa-envelope"></i> support@portfolio.com</p>
                    <p><i class="fas fa-phone"></i> 400-123-4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. 保留所有权利.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
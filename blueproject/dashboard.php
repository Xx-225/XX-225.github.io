<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/database.php';

$auth = new Auth();
$db = new Database();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$user = $auth->getCurrentUser();

// 获取用户数据统计
$stats = $db->fetchOne("
    SELECT 
        (SELECT COUNT(*) FROM products WHERE user_id = ?) as total_products,
        (SELECT COUNT(*) FROM products WHERE user_id = ? AND price > 0) as paid_products,
        (SELECT COUNT(*) FROM products WHERE user_id = ? AND price = 0) as free_products,
        (SELECT SUM(downloads) FROM products WHERE user_id = ?) as total_downloads,
        (SELECT IFNULL(SUM(p.price), 0) FROM products p 
         JOIN downloads d ON p.id = d.product_id 
         WHERE p.user_id = ? AND d.order_id IS NOT NULL) as total_income
", [$userId, $userId, $userId, $userId, $userId]);

// 获取用户最新作品
$recentProducts = $db->fetchAll("
    SELECT * FROM products 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
", [$userId]);

// 获取用户最新下载
$recentDownloads = $db->fetchAll("
    SELECT d.*, p.title, p.price 
    FROM downloads d
    JOIN products p ON d.product_id = p.id
    WHERE p.user_id = ?
    ORDER BY d.downloaded_at DESC 
    LIMIT 5
", [$userId]);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>控制台 - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- 导航栏（同首页） -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <i class="fas fa-palette"></i>
                <?php echo SITE_NAME; ?>
            </a>
            <ul class="nav-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> 首页</a></li>
                <li><a href="upload.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-cloud-upload-alt"></i> 上传作品
                </a></li>
                <li><a href="portfolio.php"><i class="fas fa-th-large"></i> 浏览</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> 购物车</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> 退出</a></li>
            </ul>
        </div>
    </nav>

    <div class="container dashboard-container">
        <!-- 侧边栏 -->
        <div class="dashboard-sidebar">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 100px; height: 100px; background: var(--primary-color); border-radius: 50%; margin: 0 auto 15px; 
                            display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem;">
                    <i class="fas fa-user"></i>
                </div>
                <h3><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h3>
                <p style="color: var(--gray-color); font-size: 0.9rem;">@<?php echo $user['username']; ?></p>
            </div>
            
            <ul class="dashboard-menu">
                <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> 控制台</a></li>
                <li><a href="profile.php"><i class="fas fa-user-edit"></i> 个人资料</a></li>
                <li><a href="my-products.php"><i class="fas fa-box"></i> 我的作品</a></li>
                <li><a href="my-downloads.php"><i class="fas fa-download"></i> 我的下载</a></li>
                <li><a href="my-purchases.php"><i class="fas fa-shopping-bag"></i> 购买记录</a></li>
                <li><a href="my-sales.php"><i class="fas fa-chart-line"></i> 销售统计</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> 账户设置</a></li>
            </ul>
        </div>

        <!-- 主要内容 -->
        <div class="dashboard-content">
            <h2 style="margin-bottom: 30px; color: var(--dark-color);">
                <i class="fas fa-tachometer-alt"></i> 控制台概览
            </h2>
            
            <!-- 数据统计卡片 -->
            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
                <div style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 20px; border-radius: var(--border-radius);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0; font-size: 2.5rem;"><?php echo $stats['total_products'] ?? 0; ?></h3>
                            <p style="margin: 5px 0 0 0; opacity: 0.9;">作品数量</p>
                        </div>
                        <i class="fas fa-box" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
                
                <div style="background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; padding: 20px; border-radius: var(--border-radius);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0; font-size: 2.5rem;"><?php echo $stats['total_downloads'] ?? 0; ?></h3>
                            <p style="margin: 5px 0 0 0; opacity: 0.9;">总下载量</p>
                        </div>
                        <i class="fas fa-download" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
                
                <div style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white; padding: 20px; border-radius: var(--border-radius);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0; font-size: 2.5rem;">¥<?php echo number_format($stats['total_income'] ?? 0, 2); ?></h3>
                            <p style="margin: 5px 0 0 0; opacity: 0.9;">总收入</p>
                        </div>
                        <i class="fas fa-yen-sign" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
            
            <!-- 最新作品 -->
            <div style="background: var(--white); padding: 20px; border-radius: var(--border-radius); margin-bottom: 30px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0;"><i class="fas fa-clock"></i> 最新作品</h3>
                    <a href="my-products.php" style="color: var(--primary-color); text-decoration: none;">
                        查看全部 <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <?php if (empty($recentProducts)): ?>
                    <p style="color: var(--gray-color); text-align: center; padding: 20px;">
                        <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>
                        还没有上传任何作品<br>
                        <a href="upload.php" style="color: var(--primary-color);">立即上传第一个作品</a>
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 12px; text-align: left;">作品标题</th>
                                    <th style="padding: 12px; text-align: left;">价格</th>
                                    <th style="padding: 12px; text-align: left;">下载量</th>
                                    <th style="padding: 12px; text-align: left;">状态</th>
                                    <th style="padding: 12px; text-align: left;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentProducts as $product): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <img src="uploads/<?php echo $product['thumbnail']; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['title']); ?>"
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                            <div>
                                                <strong><?php echo htmlspecialchars($product['title']); ?></strong><br>
                                                <small style="color: var(--gray-color);"><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php if ($product['price'] > 0): ?>
                                            <span style="color: var(--primary-color); font-weight: bold;">
                                                ¥<?php echo number_format($product['price'], 2); ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--secondary-color);">免费</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px;"><?php echo $product['downloads']; ?></td>
                                    <td style="padding: 12px;">
                                        <?php 
                                        $statusColors = [
                                            'draft' => '#f39c12',
                                            'published' => '#2ecc71',
                                            'sold' => '#9b59b6',
                                            'archived' => '#95a5a6'
                                        ];
                                        ?>
                                        <span style="background: <?php echo $statusColors[$product['status']]; ?>; 
                                              color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                                            <?php echo $product['status']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <a href="edit-product.php?id=<?php echo $product['id']; ?>" 
                                           style="color: var(--primary-color); margin-right: 10px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete-product.php?id=<?php echo $product['id']; ?>" 
                                           style="color: var(--danger-color);"
                                           onclick="return confirm('确定要删除这个作品吗？')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 快速操作 -->
            <div style="background: #f8f9fa; padding: 30px; border-radius: var(--border-radius); text-align: center;">
                <h3 style="margin-bottom: 20px;">快速开始</h3>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="upload.php" class="btn btn-primary">
                        <i class="fas fa-cloud-upload-alt"></i> 上传新作品
                    </a>
                    <a href="portfolio.php" class="btn btn-outline">
                        <i class="fas fa-eye"></i> 浏览作品
                    </a>
                    <a href="profile.php" class="btn btn-outline">
                        <i class="fas fa-user-edit"></i> 编辑资料
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>
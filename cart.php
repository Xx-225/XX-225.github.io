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
$message = '';
$error = '';

// 获取购物车内容
$cartItems = $db->fetchAll("
    SELECT c.*, p.title, p.price, p.thumbnail, p.description, u.username
    FROM cart c
    JOIN products p ON c.product_id = p.id
    JOIN users u ON p.user_id = u.id
    WHERE c.user_id = ?
", [$userId]);

// 计算总价
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// 处理移除操作
if (isset($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    $db->update("DELETE FROM cart WHERE user_id = ? AND product_id = ?", [$userId, $productId]);
    header('Location: cart.php?message=已从购物车移除');
    exit;
}

// 处理清空购物车
if (isset($_POST['clear_cart'])) {
    $db->update("DELETE FROM cart WHERE user_id = ?", [$userId]);
    $message = '购物车已清空';
    header('Location: cart.php?message=购物车已清空');
    exit;
}

// 处理更新数量
if (isset($_POST['update_quantity'])) {
    foreach ($_POST['quantities'] as $productId => $quantity) {
        $quantity = intval($quantity);
        if ($quantity > 0) {
            $db->update("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?", 
                       [$quantity, $userId, $productId]);
        } else {
            $db->update("DELETE FROM cart WHERE user_id = ? AND product_id = ?", 
                       [$userId, $productId]);
        }
    }
    $message = '购物车已更新';
    header('Location: cart.php?message=购物车已更新');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>购物车 - <?php echo SITE_NAME; ?></title>
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
                <li><a href="dashboard.php"><i class="fas fa-user-circle"></i> 控制台</a></li>
                <li><a href="portfolio.php"><i class="fas fa-th-large"></i> 作品集</a></li>
                <li><a href="cart.php" class="active"><i class="fas fa-shopping-cart"></i> 购物车 
                    <span style="background: var(--danger-color); color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.8rem;">
                        <?php echo count($cartItems); ?>
                    </span>
                </a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> 退出</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 40px 0;">
        <h2 style="margin-bottom: 30px; color: var(--dark-color);">
            <i class="fas fa-shopping-cart"></i> 我的购物车
        </h2>
        
        <?php if (isset($_GET['message'])): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cartItems)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 5rem; color: #ddd; margin-bottom: 20px;"></i>
                <h3 style="color: var(--gray-color); margin-bottom: 20px;">购物车是空的</h3>
                <p style="color: var(--gray-color); margin-bottom: 30px;">快去添加一些喜欢的作品吧！</p>
                <a href="portfolio.php" class="btn btn-primary btn-large">
                    <i class="fas fa-shopping-bag"></i> 去逛逛
                </a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div style="background: var(--white); border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow);">
                    <!-- 购物车头部 -->
                    <div style="background: #f8f9fa; padding: 20px; border-bottom: 1px solid #eee;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <h3 style="margin: 0;">购物车商品 (<?php echo count($cartItems); ?> 件)</h3>
                                <p style="margin: 5px 0 0 0; color: var(--gray-color); font-size: 0.9rem;">
                                    最后更新时间：<?php echo date('Y-m-d H:i:s'); ?>
                                </p>
                            </div>
                            <button type="submit" name="update_quantity" class="btn btn-outline">
                                <i class="fas fa-sync-alt"></i> 更新购物车
                            </button>
                        </div>
                    </div>
                    
                    <!-- 购物车商品列表 -->
                    <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <div style="display: flex; align-items: center; gap: 20px; flex: 1;">
                            <input type="checkbox" name="selected[]" value="<?php echo $item['product_id']; ?>" checked style="width: 20px; height: 20px;">
                            <img src="uploads/<?php echo $item['thumbnail']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                 class="cart-item-image">
                            <div>
                                <h4 style="margin: 0 0 10px 0;"><?php echo htmlspecialchars($item['title']); ?></h4>
                                <p style="margin: 0 0 5px 0; color: var(--gray-color); font-size: 0.9rem;">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($item['username']); ?>
                                </p>
                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                    <?php echo mb_substr($item['description'], 0, 100) . '...'; ?>
                                </p>
                            </div>
                        </div>
                        
                        <div style="text-align: center;">
                            <p style="font-size: 1.2rem; font-weight: bold; color: var(--primary-color); margin-bottom: 10px;">
                                ¥<?php echo number
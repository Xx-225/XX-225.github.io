<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = '请输入用户名和密码';
    } else {
        $result = $auth->login($username, $password);
        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

if ($auth->isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 - <?php echo SITE_NAME; ?></title>
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
                <li><a href="register.php"><i class="fas fa-user-plus"></i> 注册</a></li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 30px; color: var(--dark-color);">
            <i class="fas fa-sign-in-alt"></i> 用户登录
        </h2>
        
        <?php if ($error): ?>
            <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: var(--border-radius); margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> 用户名或邮箱</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       required placeholder="请输入用户名或邮箱">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> 密码</label>
                <input type="password" class="form-control" id="password" name="password" 
                       required placeholder="请输入密码">
            </div>
            
            <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="display: flex; align-items: center; gap: 5px;">
                    <input type="checkbox" name="remember"> 记住我
                </label>
                <a href="forgot-password.php" style="color: var(--primary-color); text-decoration: none;">
                    <i class="fas fa-key"></i> 忘记密码？
                </a>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-sign-in-alt"></i> 登录
            </button>
        </form>
        
        <div class="form-footer">
            <p>还没有账号？ <a href="register.php"><i class="fas fa-user-plus"></i> 立即注册</a></p>
        </div>
        
        <div style="text-align: center; margin-top: 20px; color: var(--gray-color);">
            <p>或使用以下方式登录：</p>
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 10px;">
                <button type="button" style="background: #db4437; color: white; border: none; padding: 10px 20px; border-radius: var(--border-radius); cursor: pointer;">
                    <i class="fab fa-google"></i> Google
                </button>
                <button type="button" style="background: #1877f2; color: white; border: none; padding: 10px 20px; border-radius: var(--border-radius); cursor: pointer;">
                    <i class="fab fa-facebook"></i> Facebook
                </button>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>
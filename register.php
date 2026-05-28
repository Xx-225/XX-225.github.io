<?php
// register.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$auth = new Auth();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    
    // 验证输入
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = '请填写所有必填字段';
    } elseif ($password !== $confirm_password) {
        $error = '两次输入的密码不一致';
    } elseif (strlen($password) < 6) {
        $error = '密码长度不能少于6位';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '邮箱格式不正确';
    } else {
        $result = $auth->register($username, $email, $password, $full_name);
        if ($result['success']) {
            $success = '注册成功！正在跳转到控制台...';
            header('Refresh: 2; url=dashboard.php');
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">
            <i class="fas fa-user-plus"></i> 用户注册
        </h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> 用户名 *</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       required placeholder="请输入用户名">
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> 邮箱 *</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                       required placeholder="请输入邮箱">
            </div>
            
            <div class="form-group">
                <label for="full_name"><i class="fas fa-id-card"></i> 姓名</label>
                <input type="text" class="form-control" id="full_name" name="full_name" 
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                       placeholder="请输入您的姓名（可选）">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> 密码 *</label>
                <input type="password" class="form-control" id="password" name="password" 
                       required placeholder="请输入密码（至少6位）">
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> 确认密码 *</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                       required placeholder="请再次输入密码">
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 5px;">
                    <input type="checkbox" name="terms" required> 
                    我已阅读并同意 <a href="terms.php" style="color: #3498db;">服务条款</a>
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-user-plus"></i> 注册账号
            </button>
        </form>
        
        <div class="form-footer">
            <p>已有账号？ <a href="login.php"><i class="fas fa-sign-in-alt"></i> 立即登录</a></p>
        </div>
    </div>

    <style>
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .btn {
            display: inline-block;
            padding: 12px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #7f8c8d;
        }
        
        .form-footer a {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</body>
</html>
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

$error = '';
$success = '';
$categories = ['design' => '设计作品', 'development' => '开发项目', 'photography' => '摄影作品', 'art' => '艺术作品', 'other' => '其他'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = $_POST['category'] ?? 'other';
    $price = floatval($_POST['price'] ?? 0);
    $userId = $_SESSION['user_id'];
    
    // 验证输入
    if (empty($title)) {
        $error = '请输入作品标题';
    } elseif (empty($description)) {
        $error = '请输入作品描述';
    } elseif ($price < 0) {
        $error = '价格不能为负数';
    } elseif (!isset($_FILES['thumbnail']) || $_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
        $error = '请上传缩略图';
    } elseif (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = '请上传作品文件';
    } else {
        // 处理文件上传
        $uploadDir = UPLOAD_DIR;
        
        // 创建上传目录
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // 上传缩略图
        $thumbnailFile = $_FILES['thumbnail'];
        $thumbnailExt = strtolower(pathinfo($thumbnailFile['name'], PATHINFO_EXTENSION));
        $thumbnailName = uniqid() . '_' . time() . '.' . $thumbnailExt;
        $thumbnailPath = $uploadDir . $thumbnailName;
        
        if (!in_array($thumbnailExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error = '缩略图格式不支持，请上传 JPG, PNG 或 GIF 格式';
        } elseif (!move_uploaded_file($thumbnailFile['tmp_name'], $thumbnailPath)) {
            $error = '缩略图上传失败';
        } else {
            // 上传作品文件
            $file = $_FILES['file'];
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = uniqid() . '_' . time() . '.' . $fileExt;
            $filePath = $uploadDir . $fileName;
            
            if (!in_array($fileExt, ALLOWED_TYPES)) {
                $error = '文件格式不支持，支持的格式：' . implode(', ', ALLOWED_TYPES);
                unlink($thumbnailPath); // 删除已上传的缩略图
            } elseif ($file['size'] > MAX_FILE_SIZE) {
                $error = '文件大小不能超过 ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB';
                unlink($thumbnailPath);
            } elseif (!move_uploaded_file($file['tmp_name'], $filePath)) {
                $error = '文件上传失败';
                unlink($thumbnailPath);
            } else {
                // 保存到数据库
                $sql = "INSERT INTO products (user_id, title, description, category, price, thumbnail, file_path, file_size, file_type, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'published')";
                
                $fileSize = filesize($filePath);
                
                $result = $db->insert($sql, [
                    $userId, $title, $description, $category, $price, 
                    $thumbnailName, $fileName, $fileSize, $fileExt
                ]);
                
                if ($result) {
                    $success = '作品上传成功！';
                    // 清空表单
                    $_POST = [];
                } else {
                    $error = '保存到数据库失败';
                    unlink($thumbnailPath);
                    unlink($filePath);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>上传作品 - <?php echo SITE_NAME; ?></title>
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
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> 退出</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding: 40px 0;">
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 30px; color: var(--dark-color);">
                <i class="fas fa-cloud-upload-alt"></i> 上传作品
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
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title"><i class="fas fa-heading"></i> 作品标题 *</label>
                    <input type="text" class="form-control" id="title" name="title" 
                           value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                           required placeholder="请输入作品标题">
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> 作品描述 *</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="5" required placeholder="详细描述您的作品，包括特点、技术栈、使用说明等"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category"><i class="fas fa-tag"></i> 作品分类 *</label>
                    <select class="form-control" id="category" name="category" required>
                        <option value="">请选择分类</option>
                        <?php foreach ($categories as $value => $label): ?>
                            <option value="<?php echo $value; ?>" 
                                <?php echo ($_POST['category'] ?? '') === $value ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price"><i class="fas fa-tag"></i> 价格（元）</label>
                    <input type="number" class="form-control" id="price" name="price" 
                           value="<?php echo htmlspecialchars($_POST['price'] ?? '0'); ?>" 
                           min="0" step="0.01" placeholder="0.00 表示免费">
                    <small style="color: var(--gray-color);">设置为0表示免费下载</small>
                </div>
                
                <div class="form-group">
                    <label for="thumbnail"><i class="fas fa-image"></i> 缩略图 *</label>
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail" 
                           accept="image/*" required>
                    <small style="color: var(--gray-color);">支持 JPG, PNG, GIF 格式，建议尺寸 800x600px</small>
                </div>
                
                <div class="form-group">
                    <label for="file"><i class="fas fa-file-upload"></i> 作品文件 *</label>
                    <input type="file" class="form-control" id="file" name="file" required>
                    <small style="color: var(--gray-color);">
                        支持格式：<?php echo strtoupper(implode(', ', ALLOWED_TYPES)); ?>，最大 <?php echo (MAX_FILE_SIZE / 1024 / 1024); ?>MB
                    </small>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 30px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-upload"></i> 上传作品
                    </button>
                    <a href="dashboard.php" class="btn btn-outline" style="flex: 1; text-align: center;">
                        <i class="fas fa-times"></i> 取消
                    </a>
                </div>
            </form>
            
            <div style="background: #e3f2fd; padding: 20px; border-radius: var(--border-radius); margin-top: 30px;">
                <h4><i class="fas fa-lightbulb"></i> 上传提示：</h4>
                <ul style="color: #1565c0; margin: 10px 0 0 20px;">
                    <li>请确保您拥有上传作品的完整版权</li>
                    <li>详细的作品描述有助于提高销量</li>
                    <li>高质量的缩略图能吸引更多用户</li>
                    <li>合理定价可以提高作品竞争力</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="assets/js/upload.js"></script>
</body>
</html>
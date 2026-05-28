<?php
// portfolio.php - 完整的作品集页面
session_start();

// 模拟数据库数据（如果数据库未连接）
$products = [
    [
        'id' => 1,
        'title' => '响应式企业官网设计',
        'description' => '采用现代化设计语言，完全响应式布局，适配各种设备。包含完整的前端界面和后台管理系统。',
        'category' => 'design',
        'price' => 199.99,
        'thumbnail' => 'design1.jpg',
        'username' => '设计师小王',
        'downloads' => 128,
        'created_at' => '2024-01-15'
    ],
    [
        'id' => 2,
        'title' => '电商数据分析平台',
        'description' => '基于Vue.js + Element UI的数据可视化平台，实时展示销售数据、用户行为分析等功能。',
        'category' => 'development',
        'price' => 299.99,
        'thumbnail' => 'dev1.jpg',
        'username' => '开发者小李',
        'downloads' => 89,
        'created_at' => '2024-02-20'
    ],
    [
        'id' => 3,
        'title' => '自然风光摄影集',
        'description' => '高清4K自然风光摄影作品，包含山川、湖泊、森林等主题，适合用作壁纸或设计素材。',
        'category' => 'photography',
        'price' => 49.99,
        'thumbnail' => 'photo1.jpg',
        'username' => '摄影师小张',
        'downloads' => 256,
        'created_at' => '2024-03-10'
    ],
    [
        'id' => 4,
        'title' => '品牌VI系统设计',
        'description' => '完整的企业品牌视觉识别系统，包含Logo、色彩规范、字体系统、应用示例等。',
        'category' => 'design',
        'price' => 399.99,
        'thumbnail' => 'design2.jpg',
        'username' => '设计师小王',
        'downloads' => 67,
        'created_at' => '2024-01-30'
    ],
    [
        'id' => 5,
        'title' => '智能家居移动应用',
        'description' => 'React Native开发的智能家居控制应用，支持设备控制、场景模式、能耗分析等功能。',
        'category' => 'development',
        'price' => 249.99,
        'thumbnail' => 'dev2.jpg',
        'username' => '开发者小陈',
        'downloads' => 145,
        'created_at' => '2024-02-28'
    ],
    [
        'id' => 6,
        'title' => '城市建筑摄影',
        'description' => '现代城市建筑摄影作品，展现城市天际线、建筑细节、光影效果等。',
        'category' => 'photography',
        'price' => 39.99,
        'thumbnail' => 'photo2.jpg',
        'username' => '摄影师小刘',
        'downloads' => 312,
        'created_at' => '2024-03-15'
    ]
];

$categories = [
    'all' => '全部作品',
    'design' => '设计作品',
    'development' => '开发项目',
    'photography' => '摄影作品',
    'art' => '艺术作品'
];

// 获取当前分类
$current_category = $_GET['category'] ?? 'all';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>作品集 - 蓝调作品集平台</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .category-filter {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .category-btn {
            padding: 8px 16px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 20px;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .category-btn:hover,
        .category-btn.active {
            background: #1E3A8A;
            color: white;
            border-color: #1E3A8A;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .product-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }
        
        .product-content {
            padding: 20px;
        }
        
        .product-price {
            color: #1E3A8A;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 15px;
        }
        
        .search-box {
            padding: 10px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            width: 300px;
            margin-left: 20px;
        }
        
        .sort-options {
            display: flex;
            gap: 10px;
            align-items: center;
        }
    </style>
</head>
<body>
    <!-- 导航栏 -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-palette"></i> 蓝调作品集
            </a>
            
            <ul class="nav-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> 首页</a></li>
                <li><a href="portfolio.php" class="active"><i class="fas fa-th-large"></i> 作品集</a></li>
                <li><a href="upload.php"><i class="fas fa-cloud-upload-alt"></i> 上传作品</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> 购物车</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="dashboard.php"><i class="fas fa-user-circle"></i> 控制台</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> 退出</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> 登录</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> 注册</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- 页头 -->
    <header class="hero" style="background: linear-gradient(135deg, #1E3A8A, #3B82F6);">
        <div class="container">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">🎨 作品集展示</h1>
            <p style="font-size: 1.2rem; opacity: 0.9;">发现创意灵感，探索优质数字作品</p>
            
            <!-- 搜索框 -->
            <div style="margin-top: 30px; display: flex; justify-content: center;">
                <div style="position: relative; width: 100%; max-width: 600px;">
                    <input type="text" placeholder="搜索作品标题、描述或作者..." 
                           style="width: 100%; padding: 15px 20px; border: none; border-radius: 25px; font-size: 1rem;">
                    <button style="position: absolute; right: 10px; top: 10px; background: #1E3A8A; color: white; 
                                  border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer;">
                        <i class="fas fa-search"></i> 搜索
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- 主内容 -->
    <main class="container" style="padding: 40px 0;">
        <!-- 分类筛选 -->
        <div class="category-filter">
            <h3 style="margin-right: 20px;">分类：</h3>
            <?php foreach($categories as $key => $name): ?>
                <a href="portfolio.php?category=<?php echo $key; ?>" 
                   class="category-btn <?php echo $current_category == $key ? 'active' : ''; ?>">
                    <?php echo $name; ?>
                </a>
            <?php endforeach; ?>
            
            <!-- 排序选项 -->
            <div class="sort-options" style="margin-left: auto;">
                <span>排序：</span>
                <select style="padding: 8px 15px; border: 2px solid #e9ecef; border-radius: 5px;">
                    <option>最新发布</option>
                    <option>最受欢迎</option>
                    <option>价格从低到高</option>
                    <option>价格从高到低</option>
                </select>
            </div>
        </div>

        <!-- 作品网格 -->
        <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; margin-top: 30px;">
            <?php foreach($products as $product): ?>
                <?php if($current_category == 'all' || $current_category == $product['category']): ?>
                <div class="product-card">
                    <div class="product-image">
                        <i class="fas fa-<?php echo $product['category'] == 'design' ? 'paint-brush' : 
                                               ($product['category'] == 'development' ? 'code' : 
                                               ($product['category'] == 'photography' ? 'camera' : 'palette')); ?>"></i>
                    </div>
                    
                    <div class="product-content">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p style="color: #6c757d; margin: 10px 0; line-height: 1.5;">
                            <?php echo mb_substr($product['description'], 0, 80) . '...'; ?>
                        </p>
                        
                        <div class="product-price">
                            <?php if($product['price'] > 0): ?>
                                ¥<?php echo number_format($product['price'], 2); ?>
                            <?php else: ?>
                                <span style="color: #2ecc71;">免费</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-meta">
                            <span><i class="fas fa-user"></i> <?php echo $product['username']; ?></span>
                            <span><i class="fas fa-download"></i> <?php echo $product['downloads']; ?></span>
                        </div>
                        
                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <a href="product.php?id=<?php echo $product['id']; ?>" 
                               class="btn" style="flex: 1; text-align: center;">
                                <i class="fas fa-eye"></i> 查看详情
                            </a>
                            <button class="btn" style="background: #2ecc71; border: none; padding: 10px 15px; border-radius: 5px;">
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- 分页 -->
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 50px;">
            <a href="#" class="btn" style="background: #f8f9fa; color: #495057;">上一页</a>
            <a href="#" class="btn active">1</a>
            <a href="#" class="btn" style="background: #f8f9fa; color: #495057;">2</a>
            <a href="#" class="btn" style="background: #f8f9fa; color: #495057;">3</a>
            <span style="padding: 10px 20px;">...</span>
            <a href="#" class="btn" style="background: #f8f9fa; color: #495057;">10</a>
            <a href="#" class="btn" style="background: #f8f9fa; color: #495057;">下一页</a>
        </div>
    </main>

    <!-- 页脚 -->
    <footer class="footer" style="background: #2c3e50; color: white; padding: 40px 0; margin-top: 50px;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
                <div>
                    <h3 style="margin-bottom: 20px;">蓝调作品集</h3>
                    <p>连接创意与商业，让每一份才华都得到价值体现</p>
                </div>
                <div>
                    <h4 style="margin-bottom: 20px;">快速链接</h4>
                    <ul style="list-style: none;">
                        <li><a href="index.php" style="color: #bdc3c7; text-decoration: none;">首页</a></li>
                        <li><a href="portfolio.php" style="color: #bdc3c7; text-decoration: none;">作品集</a></li>
                        <li><a href="upload.php" style="color: #bdc3c7; text-decoration: none;">上传作品</a></li>
                    </ul>
                </div>
                <div>
                    <h4 style="margin-bottom: 20px;">联系我们</h4>
                    <p><i class="fas fa-envelope"></i> support@blueportfolio.com</p>
                    <p><i class="fas fa-phone"></i> 400-123-4567</p>
                </div>
            </div>
            <div style="text-align: center; padding-top: 20px; border-top: 1px solid #34495e; margin-top: 30px;">
                <p>© <?php echo date('Y'); ?> 蓝调作品集平台. 保留所有权利.</p>
            </div>
        </div>
    </footer>

    <script>
        // 搜索功能
        document.querySelector('input[type="text"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    alert('搜索: ' + query);
                    // 实际项目中这里应该跳转到搜索页面
                }
            }
        });
        
        // 添加到购物车功能
        document.querySelectorAll('.btn[style*="background: #2ecc71"]').forEach(button => {
            button.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const productName = productCard.querySelector('h3').textContent;
                alert('已将 "' + productName + '" 添加到购物车');
            });
        });
    </script>
</body>
</html>
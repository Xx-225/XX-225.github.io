<?php
// check_mysql.php
echo "<h2>检测MySQL连接方式</h2>";

// 测试不同连接方式
$tests = [
    ['root', ''],           // 无密码
    ['root', 'root'],       // 密码root
    ['root', 'password'],   // 密码password
    ['', ''],               // 无用户
];

// 添加XAMPP 8.1+的可能配置
$ports = [3306, 3307, 3308];

foreach ($tests as $test) {
    $user = $test[0];
    $pass = $test[1];
    
    foreach ($ports as $port) {
        echo "测试: 用户='{$user}', 密码='{$pass}', 端口={$port}... ";
        
        try {
            $conn = new mysqli("localhost:{$port}", $user, $pass);
            
            if ($conn->connect_error) {
                echo "<span style='color:orange'>失败: {$conn->connect_error}</span><br>";
            } else {
                echo "<span style='color:green;font-weight:bold'>✅ 成功！</span><br>";
                
                // 尝试创建数据库
                $conn->query("CREATE DATABASE IF NOT EXISTS portfolio_db");
                echo "✅ 数据库检查/创建完成<br>";
                
                $conn->close();
                echo "<h3>🎉 请使用以下配置：</h3>";
                echo "<pre style='background:#f5f5f5;padding:10px;'>";
                echo "define('DB_HOST', 'localhost:{$port}');\n";
                echo "define('DB_USER', '{$user}');\n";
                echo "define('DB_PASS', '{$pass}');\n";
                echo "define('DB_NAME', 'portfolio_db');";
                echo "</pre>";
                exit;
            }
        } catch (Exception $e) {
            echo "<span style='color:red'>异常: " . $e->getMessage() . "</span><br>";
        }
    }
}

echo "<h3 style='color:red'>❌ 所有测试都失败！</h3>";
echo "<p>请尝试以下操作：</p>";
echo "<ol>
<li>确保XAMPP的MySQL服务已启动（显示Running）</li>
<li>在XAMPP控制面板点击MySQL的'Shell'按钮，输入: <code>mysql -u root</code></li>
<li>如果提示需要密码，输入'root'或直接回车</li>
</ol>";
?>
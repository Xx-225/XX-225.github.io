<?php
// includes/functions.php
require_once 'database.php';

function formatPrice($price) {
    return '¥' . number_format($price, 2);
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return '刚刚';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . '分钟前';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . '小时前';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . '天前';
    } else {
        return date('Y-m-d', $time);
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function getCategoryName($category) {
    $categories = [
        'design' => '设计作品',
        'development' => '开发项目',
        'photography' => '摄影作品',
        'art' => '艺术作品',
        'other' => '其他'
    ];
    
    return $categories[$category] ?? '其他';
}
?>
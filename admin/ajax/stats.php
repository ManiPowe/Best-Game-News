<?php
session_start();
require_once '../../assets/app/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'activity') {
    // Активность за последние 7 дней
    $result = mysqli_query($conn, "
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM news 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    
    $labels = [];
    $data = [];
    
    // Заполняем все 7 дней (даже если нет данных)
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('d.m', strtotime($date));
        $data[$date] = 0;
    }
    
    while ($row = mysqli_fetch_assoc($result)) {
        $data[$row['date']] = $row['count'];
    }
    
    echo json_encode([
        'labels' => $labels,
        'data' => array_values($data)
    ]);
    
} elseif ($action === 'categories') {
    // Распределение по категориям
    $result = mysqli_query($conn, "
        SELECT category, COUNT(*) as count 
        FROM news 
        GROUP BY category
        ORDER BY count DESC
    ");
    
    $labels = [];
    $data = [];
    $colors = ['#df1b1b', '#4CAF50', '#2196F3', '#FF9800', '#9C27B0'];
    
    $category_names = [
        'news' => 'Новости',
        'games' => 'Игры',
        'articles' => 'Статьи',
        'videos' => 'Видео',
        'walkthroughs' => 'Прохождения'
    ];
    
    $i = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $labels[] = $category_names[$row['category']] ?? $row['category'];
        $data[] = $row['count'];
        $i++;
    }
    
    echo json_encode([
        'labels' => $labels,
        'data' => $data,
        'colors' => array_slice($colors, 0, count($data))
    ]);
}
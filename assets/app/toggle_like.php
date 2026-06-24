<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Необходимо войти в систему']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$news_id = $data['news_id'] ?? 0;
$user_id = $_SESSION['user_id'];

if (!$news_id) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID новости']);
    exit;
}

// Проверяем, лайкал ли уже пользователь
$check_sql = "SELECT * FROM news_likes WHERE user_id = ? AND news_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $news_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    // Удаляем лайк
    $delete_sql = "DELETE FROM news_likes WHERE user_id = ? AND news_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "ii", $user_id, $news_id);
    mysqli_stmt_execute($delete_stmt);
    
    $liked = false;
} else {
    // Добавляем лайк
    $insert_sql = "INSERT INTO news_likes (user_id, news_id, created_at) VALUES (?, ?, NOW())";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $news_id);
    mysqli_stmt_execute($insert_stmt);
    
    $liked = true;
}

// Считаем общее количество лайков
$count_sql = "SELECT COUNT(*) as likes_count FROM news_likes WHERE news_id = ?";
$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, "i", $news_id);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_data = mysqli_fetch_assoc($count_result);

// Обновляем счётчик в таблице news
$update_sql = "UPDATE news SET likes_count = ? WHERE id = ?";
$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "ii", $count_data['likes_count'], $news_id);
mysqli_stmt_execute($update_stmt);

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'likes_count' => $count_data['likes_count']
]);
?>
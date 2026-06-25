<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Необходимо войти в систему']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$comment_id = $data['comment_id'] ?? 0;
$user_id = $_SESSION['user_id'];

if (!$comment_id) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID комментария']);
    exit;
}

// Проверяем, лайкал ли уже
$check_sql = "SELECT id FROM comment_likes WHERE user_id = ? AND comment_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $comment_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    // Удаляем лайк
    $delete_sql = "DELETE FROM comment_likes WHERE user_id = ? AND comment_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "ii", $user_id, $comment_id);
    mysqli_stmt_execute($delete_stmt);
    
    $liked = false;
} else {
    // Добавляем лайк
    $insert_sql = "INSERT INTO comment_likes (user_id, comment_id, created_at) VALUES (?, ?, NOW())";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $comment_id);
    mysqli_stmt_execute($insert_stmt);
    
    $liked = true;
}

// Считаем лайки
$count_sql = "SELECT COUNT(*) as likes_count FROM comment_likes WHERE comment_id = ?";
$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, "i", $comment_id);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_data = mysqli_fetch_assoc($count_result);

// Обновляем счётчик
$update_sql = "UPDATE comments SET likes_count = ? WHERE id = ?";
$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "ii", $count_data['likes_count'], $comment_id);
mysqli_stmt_execute($update_stmt);

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'likes_count' => $count_data['likes_count']
]);
?>
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

// Проверяем, есть ли уже в избранном
$check_sql = "SELECT id FROM favorites WHERE user_id = ? AND news_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $news_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) > 0) {
    // Удаляем из избранного
    $delete_sql = "DELETE FROM favorites WHERE user_id = ? AND news_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "ii", $user_id, $news_id);
    mysqli_stmt_execute($delete_stmt);
    
    $favorited = false;
} else {
    // Добавляем в избранное
    $insert_sql = "INSERT INTO favorites (user_id, news_id, created_at) VALUES (?, ?, NOW())";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $news_id);
    mysqli_stmt_execute($insert_stmt);
    
    $favorited = true;
}

// Считаем общее количество избранных
$count_sql = "SELECT COUNT(*) as favorites_count FROM favorites WHERE news_id = ?";
$count_stmt = mysqli_prepare($conn, $count_sql);
mysqli_stmt_bind_param($count_stmt, "i", $news_id);
mysqli_stmt_execute($count_stmt);
$count_result = mysqli_stmt_get_result($count_stmt);
$count_data = mysqli_fetch_assoc($count_result);

echo json_encode([
    'success' => true,
    'favorited' => $favorited,
    'favorites_count' => $count_data['favorites_count']
]);
?>
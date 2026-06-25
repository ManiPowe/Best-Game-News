<?php
session_start();
require_once __DIR__ . '/../../assets/app/db.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit;
}

// Получаем роль из БД (т.к. в сессии её нет)
$role_check_sql = "SELECT role FROM users WHERE id = ?";
$role_stmt = mysqli_prepare($conn, $role_check_sql);
mysqli_stmt_bind_param($role_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($role_stmt);
$role_result = mysqli_stmt_get_result($role_stmt);
$role_data = mysqli_fetch_assoc($role_result);
$user_role = $role_data['role'] ?? '';

// Проверяем права
if (!in_array($user_role, ['admin', 'moderator'])) {
    header("Location: /index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['news_id'])) {
    $news_id = (int) $_POST['news_id'];
    
    // СНАЧАЛА получаем информацию о новости (до обновления)
    $news_info_sql = "SELECT author_id, title FROM news WHERE id = ?";
    $news_info_stmt = mysqli_prepare($conn, $news_info_sql);
    mysqli_stmt_bind_param($news_info_stmt, "i", $news_id);
    mysqli_stmt_execute($news_info_stmt);
    $news_info = mysqli_fetch_assoc(mysqli_stmt_get_result($news_info_stmt));
    
    // Обновляем статус новости
    $update_sql = "UPDATE news SET status = 'published' WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "i", $news_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        // Отправляем уведомление автору (только если новость найдена)
        if ($news_info) {
            require_once __DIR__ . '/../../assets/app/notifications.php';
            sendNotification(
                $conn, 
                $news_info['author_id'], 
                'post_approved', 
                "Ваша новость «{$news_info['title']}» была опубликована!"
            );
        }
        
        $_SESSION['success'] = "Новость успешно опубликована!";
    } else {
        $_SESSION['error'] = "Ошибка при публикации новости";
    }
    
    header("Location: /admin/admin.php?tab=moderation");
    exit;
}

header("Location: /admin/admin.php?tab=moderation");
exit;
?>
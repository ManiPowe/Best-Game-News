<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../..//login");
    exit;
}

$current_user_id = $_SESSION['user_id'];
$news_id = (int) $_POST['news_id'];

// Получаем роль текущего пользователя
$role_sql = "SELECT role FROM users WHERE id = ?";
$role_stmt = mysqli_prepare($conn, $role_sql);
mysqli_stmt_bind_param($role_stmt, "i", $current_user_id);
mysqli_stmt_execute($role_stmt);
$role_data = mysqli_fetch_assoc(mysqli_stmt_get_result($role_stmt));
$current_role = $role_data['role'] ?? '';

// Получаем новость
$check_sql = "SELECT id, image, author_id, title FROM news WHERE id = ?";
$stmt_check = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt_check, "i", $news_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$news = mysqli_fetch_assoc($result_check);

if (!$news) {
    die("Новость не найдена!");
}

// Проверяем права: либо автор, либо админ/модер
$is_author = ($news['author_id'] == $current_user_id);
$is_moderator = in_array($current_role, ['admin', 'moderator']);

if (!$is_author && !$is_moderator) {
    die("У вас нет прав на удаление этой новости!");
}

// Удаляем файл картинки если есть
if ($news['image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $news['image'])) {
    unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $news['image']);
}

// Удаляем новость
$delete_sql = "DELETE FROM news WHERE id = ?";
$stmt_delete = mysqli_prepare($conn, $delete_sql);
mysqli_stmt_bind_param($stmt_delete, "i", $news_id);
mysqli_stmt_execute($stmt_delete);

// Обновляем счётчик постов у АВТОРА новости
mysqli_query($conn, "UPDATE users SET posts_count = GREATEST(posts_count - 1, 0) WHERE id = " . (int)$news['author_id']);

// Редирект в профиль автора новости
header("Location: ../..//profile/" . $news['author_id']);
exit;
?>
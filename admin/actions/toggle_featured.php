<?php
session_start();
require_once __DIR__ . '/../../assets/app/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit;
}

// Получаем роль из БД
$role_sql = "SELECT role FROM users WHERE id = ?";
$role_stmt = mysqli_prepare($conn, $role_sql);
mysqli_stmt_bind_param($role_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($role_stmt);
$user_role = mysqli_fetch_assoc(mysqli_stmt_get_result($role_stmt))['role'] ?? '';

// Только админ может управлять слайдером
if ($user_role !== 'admin') {
    header("Location: /index.php");
    exit;
}

if (isset($_GET['id'])) {
    $news_id = (int) $_GET['id'];
    
    // Получаем текущий статус
    $check_sql = "SELECT is_featured FROM news WHERE id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $news_id);
    mysqli_stmt_execute($check_stmt);
    $current_status = mysqli_fetch_assoc(mysqli_stmt_get_result($check_stmt))['is_featured'] ?? 0;
    
    // Переключаем статус
    $new_status = $current_status ? 0 : 1;
    $update_sql = "UPDATE news SET is_featured = ? WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ii", $new_status, $news_id);
    
    if (mysqli_stmt_execute($update_stmt)) {
        $_SESSION['success'] = $new_status ? "Новость добавлена в слайдер!" : "Новость убрана из слайдера";
    } else {
        $_SESSION['error'] = "Ошибка при обновлении";
    }
}

header("Location: /admin/admin.php?tab=featured");
exit;
?>
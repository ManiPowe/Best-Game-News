<?php
session_start();
require_once '../assets/app/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Получаем роль из БД
$check_sql = "SELECT role FROM users WHERE id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($check_stmt);
$user_data = mysqli_fetch_assoc(mysqli_stmt_get_result($check_stmt));

if (!in_array($user_data['role'] ?? '', ['admin', 'moderator'])) {
    header("Location: ../index.php");
    exit;
}

$admin_user = $user_data;
?>
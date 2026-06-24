<?php
session_start();
require_once '../assets/app/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$check_sql = "SELECT role, login, avatar FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin_user = mysqli_fetch_assoc($result);

if (!$admin_user || !in_array($admin_user['role'], ['admin', 'moderator'])) {
    header("Location: ../index.php");
    exit;
}

$_SESSION['admin_role'] = $admin_user['role'];
?>
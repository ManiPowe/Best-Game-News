<?php
session_start();
require_once '../../assets/app/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$check_sql = "SELECT role FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$current_user = mysqli_fetch_assoc($result);

if (!$current_user || $current_user['role'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

$target_user_id = intval($_POST['user_id'] ?? $_GET['user_id'] ?? 0);
$new_role = $_POST['role'] ?? $_GET['role'] ?? '';

$allowed_roles = ['user', 'creator', 'moderator', 'admin'];

if (!$target_user_id || !in_array($new_role, $allowed_roles)) {
    header("Location: ../admin.php?tab=users&error=invalid_data");
    exit;
}

if ($target_user_id == $_SESSION['user_id'] && $new_role !== 'admin') {
    header("Location: ../admin.php?tab=users&error=cant_demote_self");
    exit;
}

$update_sql = "UPDATE users SET role = ? WHERE id = ?";
$stmt_update = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($stmt_update, "si", $new_role, $target_user_id);

if (mysqli_stmt_execute($stmt_update)) {
    header("Location: ../admin.php?tab=users&msg=role_updated");
} else {
    header("Location: ../admin.php?tab=users&error=db_error");
}
exit;
?>
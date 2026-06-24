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

if (!$current_user || !in_array($current_user['role'], ['admin', 'moderator'])) {
    header("Location: ../../index.php");
    exit;
}

$news_id = intval($_GET['id'] ?? 0);

if (!$news_id) {
    header("Location: ../admin.php?tab=featured&error=no_id");
    exit;
}

$get_sql = "SELECT is_featured FROM news WHERE id = ?";
$stmt_get = mysqli_prepare($conn, $get_sql);
mysqli_stmt_bind_param($stmt_get, "i", $news_id);
mysqli_stmt_execute($stmt_get);
$result_get = mysqli_stmt_get_result($stmt_get);
$news_data = mysqli_fetch_assoc($result_get);

if (!$news_data) {
    header("Location: ../admin.php?tab=featured&error=news_not_found");
    exit;
}

$new_featured_status = $news_data['is_featured'] ? 0 : 1;

$update_sql = "UPDATE news SET is_featured = ? WHERE id = ?";
$stmt_update = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($stmt_update, "ii", $new_featured_status, $news_id);

if (mysqli_stmt_execute($stmt_update)) {
    $msg = $new_featured_status ? 'added_featured' : 'removed_featured';
    header("Location: ../admin.php?tab=featured&msg={$msg}");
} else {
    header("Location: ../admin.php?tab=featured&error=db_error");
}
exit;
?>
<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$news_id = (int) $_POST['news_id'];

$check_sql = "SELECT id, image FROM news WHERE id = ? AND author_id = ?";
$stmt_check = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($stmt_check, "ii", $news_id, $user_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$news = mysqli_fetch_assoc($result_check);

if (!$news) {
    die("Новость не найдена или у вас нет прав!");
}

if ($news['image'] && file_exists($news['image'])) {
    unlink($news['image']);
}

$delete_sql = "DELETE FROM news WHERE id = ?";
$stmt_delete = mysqli_prepare($conn, $delete_sql);
mysqli_stmt_bind_param($stmt_delete, "i", $news_id);
mysqli_stmt_execute($stmt_delete);

mysqli_query($conn, "UPDATE users SET posts_count = GREATEST(posts_count - 1, 0) WHERE id = $user_id");

header("Location: ../../profile.php?id=" . $user_id);
exit;
?>
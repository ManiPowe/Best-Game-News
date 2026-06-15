<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['user_id'])) {
    $user_id = 1;
} else {
    $user_id = $_SESSION['user_id'];
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    die("Ошибка загрузки файла. Код ошибки: " . $_FILES['avatar']['error'] . " <a href='../cab.php'>Назад</a>");
}

$file = $_FILES['avatar'];

$original_name = $file['name'];
$tmp_path = $file['tmp_name'];
$file_size = $file['size'];
$max_size = 10 * 1024 * 1024;
if ($file_size > $max_size) {
    die("Файл слишком большой! Максимум 10 МБ. <a href='../cab.php'>Назад</a>");
}

$extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

$allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

if (!in_array($extension, $allowed_extensions)) {
    die("Разрешены только файлы форматов: JPG, PNG, WEBP, GIF. <a href='../cab.php'>Назад</a>");
}

$image_info = getimagesize($tmp_path);
if ($image_info === false) {
    die("Файл не является изображением! <a href='../cab.php'>Назад</a>");
}

$unique_name = 'avatar_' . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

$upload_dir = '../Media/avatars/';
$destination = $upload_dir . $unique_name;

if (!move_uploaded_file($tmp_path, $destination)) {
    die("Не удалось сохранить файл на сервере. <a href='../cab.php'>Назад</a>");
}

$sql_old = "SELECT avatar FROM users WHERE id = ?";
$stmt_old = mysqli_prepare($conn, $sql_old);
mysqli_stmt_bind_param($stmt_old, "i", $user_id);
mysqli_stmt_execute($stmt_old);
$result_old = mysqli_stmt_get_result($stmt_old);
$old_data = mysqli_fetch_assoc($result_old);

if ($old_data && $old_data['avatar'] !== 'assets/Media/Photo/man.png' && file_exists($old_data['avatar'])) {
    unlink($old_data['avatar']);
}

$new_avatar_path = 'assets/Media/avatars/' . basename($destination);

$sql_update = "UPDATE users SET avatar = ? WHERE id = ?";
$stmt_update = mysqli_prepare($conn, $sql_update);
mysqli_stmt_bind_param($stmt_update, "si", $new_avatar_path, $user_id);

if (mysqli_stmt_execute($stmt_update)) {
    $_SESSION['avatar'] = $new_avatar_path;
    
    header("Location: ../cab.php");
    exit;
} else {
    die("Ошибка обновления базы данных: " . mysqli_error($conn));
}
?>
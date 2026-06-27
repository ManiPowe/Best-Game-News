<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['file'])) {
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];
$upload_dir = __DIR__ . '/../Media/uploads/';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed_image = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
$allowed_video = ['mp4', 'webm'];
$max_size = 50 * 1024 * 1024; // 50 МБ

// Проверка размера
if ($file['size'] > $max_size) {
    echo json_encode(['error' => 'File too large (max 50MB)']);
    exit;
}

// Определение типа файла
$is_video = in_array($extension, $allowed_video);
$is_image = in_array($extension, $allowed_image);

if (!$is_video && !$is_image) {
    echo json_encode(['error' => 'Invalid file type']);
    exit;
}

// Генерация уникального имени
$unique_name = ($is_video ? 'video_' : 'img_') . uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
$destination = $upload_dir . $unique_name;

if (move_uploaded_file($file['tmp_name'], $destination)) {
    $url = '/assets/Media/uploads/' . $unique_name;
    echo json_encode([
        'location' => $url,
        'url' => $url
    ]);
} else {
    echo json_encode(['error' => 'Failed to move uploaded file']);
}
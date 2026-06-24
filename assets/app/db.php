<?php
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'BGN';

    $conn = mysqli_connect($servername,$username,$password, $dbname);
    if (!$conn){
        die("Подключение не удалось:" . mysqli_connect_error());
    }
    if (isset($_SESSION['user_id'])) {
        $update_activity = "UPDATE users SET last_activity = NOW() WHERE id = ?";
        $stmt_activity = mysqli_prepare($conn, $update_activity);
        mysqli_stmt_bind_param($stmt_activity, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt_activity);
    }
?>
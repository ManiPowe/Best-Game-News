<?php
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'BGN';

    $conn = mysqli_connect($servername,$username,$password, $dbname);
    if (!$conn){
        die("Подключение не удалось:" . mysqli_connect_error());
    } else{
        echo "Идите нахуйте сте таксите";
    }
?>
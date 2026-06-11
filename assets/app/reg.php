<?php
    require_once  ('db.php');
    $login = $_POST['login'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    $sql = "INSERT INTO `users` (login, password, name, email, phone) VALUES ('$login','$password','$name','$email','$phone')";

    $conn -> query($sql);
    ?>
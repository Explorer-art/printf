<?php
require_once('db.php');

$login = $_POST['login'];
$pass = $_POST['password'];
$repeatpass = $_POST['repeatpass'];
$email = $_POST['email'];

if (empty($login) || empty($pass) || empty($repeatpass) || empty($email)) {
    echo "Заполните все поля";
} else
{
    if ($pass != $repeatpass) {

        echo "Пароли не совпадают";


    }else{
        $sql = "INSERT INTO `users`(login,pass,email)  VALUES ('$login','$pass','$email')";

        if( $connect->query($sql) === true){
            echo "Успешная регистрация";
        }
        else{
            echo "Ошибка: ".$connect->error;
        }
    }

}

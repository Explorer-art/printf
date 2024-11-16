<?php
session_start();
include('db.php');

$username = $_POST["username"];
$email = $_POST["email"];
$password = $_POST["password"];
$password_hash = password_hash($password, PASSWORD_BCRYPT); # Хеширование пароля

$query = $connection->prepare("SELECT * FROM users WHERE username = :username"); # Выбрать все записи (*) из таблицы users в которых username = $username
$query->bindParam("username", $username, PDO::PARAM_STR); # Устанавливаем параметр username в запрос выше
$query->execute(); # Выполняем запрос

if ($query->rowCount() > 0) {
    echo "<p>Это имя пользователя уже занято!</p>";
}
    
if ($query->rowCount() == 0) {
    $query = $connection->prepare("INSERT INTO users(username,email,password) VALUES (:username,:email,:password_hash)"); # Добавляем нового пользователя
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->bindParam("email", $email, PDO::PARAM_STR);
    $query->bindParam("password_hash", $password_hash, PDO::PARAM_STR);
    $result = $query->execute();
    
    if ($result) {
        echo "<p>Регистрация прошла успешно!</p>";
    } else {
        echo "<p>Неверные данные!</p>";
    }
}
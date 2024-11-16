<?php
session_start();
require_once("db.php");

$username = $_POST["username"];
$password = $_POST["password"];

$query = $connection->prepare("SELECT * FROM users WHERE username = :username LIMIT 1"); # Выбрать все записи (*) из таблицы users в которых username = $username, но количество записей в результате не должно превышать 1 (LIMIT 1)
$query->bindParam("username", $username, PDO::PARAM_STR);
$query->execute();

if ($query->rowCount() == 1) {
    $user = $query->fetch(PDO::FETCH_ASSOC); # Извлекаем данные пользователя полученные из базы данных

    # Функция password_verify() сравнивает пароль (перед этим его хеширует) с хешем пароля в базе данных
    if (password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"]; # Сохраняем сессию пользователя

        echo "Добро пожаловать, " . $user["username"] . "!";
    } else {
        echo "Имя пользователя или пароль неверный!";
    }
} else {
    echo "Имя пользователя или пароль неверный!";
}
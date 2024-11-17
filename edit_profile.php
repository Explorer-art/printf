<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
}

$user_id = $_SESSION['user_id'];
$query =$connection->prepare("SELECT * FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();
$email = $query->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];


    $query = $connection->prepare("UPDATE users SET username = ? WHERE id = ?");
    if ($query->execute([$username, $user_id])) {
        echo "Данные успешно обновлены!";
        header("Location: profile.php");
        exit();
    } else {
        echo "Ошибка обновления данных";
    }


    $query = $connection->prepare("UPDATE users SET email = ? WHERE id = ?");
    if ($query->execute([$email, $user_id])) {
        echo "Данные успешно обновлены!";
        header("Location: profile.php");
        exit();
    } else {
        echo "Ошибка обновления данных";
    }
}
    ?>;


<h1>Редактировать профиль </h1>>
<form method="post">
    <label for = "username">Имя: </label>
    <input type ="text" id = "username" name ="username" value ="<?php htmlspecialchars($user["username"]);?>" required>
    <label for = "email">Почта: </label>
    <input type ="text" id = "email" name ="email" value ="<?php htmlspecialchars($user["email"]);?>" required>
    <button type="submit">Сохранить изменения </button>
</form>
<a href = "profile.php">Назад к профилю</a>


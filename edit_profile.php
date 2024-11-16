<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
}

$user_id = $_SESSION['user_id'];
$stmt =$PDO->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt = fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];


    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    if ($stmt->execute([$username, $user_id])) {
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

    <button type="submit">Сохранить изминения </button>
</form>
<a href = "profile.php">Назад к профилю</a>
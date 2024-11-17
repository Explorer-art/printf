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
$email = trim(strip_tags(htmlspecialchars($_POST['email'])));

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
        if($query->execute([$new_email, $user_id])){
            echo "Электронная почта успешно обновлена";
        }
        else{
            echo "Ошибка";
        }
}
    ?>;


<h1>Редактировать профиль </h1>>
<form action="edit_profile.php" method="post">
    <label for = "username">Имя: </label>
    <input type ="text" id = "username" name ="username" value ="<?php htmlspecialchars($user["username"]);?>" required>
    <label for = "email">Новая электронная почта</label>
    <input type="email" id="email" name="email" required>
    <button type="submit">Сохранить изменения </button>
</form>
<a href = "profile.php">Назад к профилю</a>


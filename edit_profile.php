<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $connection->prepare("SELECT * FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = trim(strip_tags(htmlspecialchars($_POST["email"])));



    $emailQuery = $connection->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
    $emailQuery->execute([$email, $user_id]);

    if ($emailQuery->rowCount() > 0) {
        echo "Электронная почта уже используется другим пользователем.";
        http_response_code(400);
        exit();
    }

    $query = $connection->prepare("UPDATE users SET username = ? WHERE id = ?");
    if ($query->execute([$username, $user_id])) {
        echo "Имя пользователя успешно обновлено!";
    } else {
        echo "Ошибка обновления имени пользователя.";
        http_response_code(500);
        exit();
    }

    $query = $connection->prepare("UPDATE users SET email = ? WHERE id = ?");
    if ($query->execute([$email, $user_id])) {
        echo "Электронная почта успешно обновлена!";
    } else {
        echo "Ошибка обновления электронной почты.";
        http_response_code(500);
        exit();
    }

    // Перенаправление на профиль после успешного обновления
    header("Location: profile.php");
    http_response_code(200);
    exit();
}
?>

<h1>Редактировать профиль</h1>

<form action="upload_logo.php" method="post" enctype="multipart/form-data">

<label for="logo"> Выберите аватар: </label>
<input type="file" name="logo" id="logo" accept="image/*" required>
<button type="submit">Сохранить аватар: </button>
</form>>

<form action="edit_profile.php" method="post" >

    <label for="username">Имя:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user["username"]); ?>">

    <label for="email">Новая электронная почта:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>">

    <button type="submit">Сохранить изменения</button>
</form>
<a href="profile.php">Назад к профилю</a>


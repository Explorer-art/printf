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
        $data = [
            "success" => false,
            "message" => "Электронная почта уже используется другим пользователем"
        ]

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
        exit();
    }

    $query = $connection->prepare("UPDATE users SET username = ? WHERE id = ?");
    if ($query->execute([$username, $user_id])) {
        $data = [
            "success" => true,
            "message" => "Имя пользователя успешно обновлено!"
        ]

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(200);
        echo json_encode($data);
    } else {
        $data = [
            "success" => false,
            "message" => "Ошибка обновления имени пользователя"
        ]

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(500);
        echo json_encode($data);
        exit();
    }

    $query = $connection->prepare("UPDATE users SET email = ? WHERE id = ?");
    if ($query->execute([$email, $user_id])) {
        $data = [
            "success" => true,
            "message" => "Электронная почта успешно обновлена!"
        ]

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(200);
        echo json_encode($data);
    } else {
        $data = [
            "success" => false,
            "message" => "Ошибка обновления электронной почты"
        ]

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(500);
        echo json_encode($data);
    }

    // Перенаправление на профиль после успешного обновления
    header("Location: profile.php");
    exit();
}
?>

<h1>Редактировать профиль</h1>
<form action="edit_profile.php" method="post">
    <label for="username">Имя:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user["username"]); ?>">

    <label for="email">Новая электронная почта:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>">

    <button type="submit">Сохранить изменения</button>
</form>
<a href="profile.php">Назад к профилю</a>
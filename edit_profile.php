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
$user = $query->fetch(PDO::FETCH_ASSOC);


if (!$user) {
    echo "Пользователь не найден.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = trim(strip_tags(htmlspecialchars($_POST["email"])));
    $description = trim(strip_tags(htmlspecialchars($_POST["description"])));


    $emailQuery = $connection->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
    $emailQuery->execute([$email, $user_id]);

    if ($emailQuery->rowCount() > 0) {
        $data = [
            "success" => false,
            "message" => "Электронная почта уже используется другим пользователем"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
        exit();
    }

    $query = $connection->prepare("UPDATE users SET username = ? WHERE id = ?");
    if (!$query->execute([$username, $user_id])) {
        echo "Ошибка обновления имени пользователя";
        exit();
    }

    $query = $connection->prepare("UPDATE users SET email = ? WHERE id = ?");
    if (!$query->execute([$email, $user_id])) {
        echo "Ошибка обновления электронной почты";
        exit();
    }


    $query = $connection->prepare("UPDATE users SET description = ? WHERE id = ?");
    if (!$query->execute([$description, $user_id])) {
        echo "Ошибка обновления описания";
        exit();
    }


    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['logo']['name']);

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {

            $query = $connection->prepare("UPDATE users SET logo = ? WHERE id = ?");
            if ($query->execute([$uploadFile, $user_id])) {
                echo "Логотип успешно загружен и профиль обновлен.";
            } else {
                echo "Ошибка обновления логотипа.";
            }
        } else {
            echo "Ошибка загрузки файла.";
        }
    } else {
        echo "Логотип не загружен.";
    }

    header("Location: profile.php");
    exit();
}
?>

<h1>Редактировать профиль</h1>

<form action="edit_profile.php" method="post" enctype="multipart/form-data">
    <label for="logo">Выберите аватар:</label>
    <input type="file" name="logo" id="logo" accept="image/*">

    <label for="username">Имя:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

    <label for="email">Новая электронная почта:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

    <label for="description">Описание:</label>
    <textarea id="description" name="description"><?php echo htmlspecialchars($user['description']); ?></textarea>

    <button type="submit">Сохранить изменения</button>
</form>

<a href="profile.php">Назад к профилю</a>

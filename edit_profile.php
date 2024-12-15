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
        $upload_dir = 'logo/';
        $image_file_type = strtolower(pathinfo($logo_file['name'], PATHINFO_EXTENSION));
        $upload_file = $upload_dir . uniqid('avatar_', true) . $image_file_type;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_file)) {

            $query = $connection->prepare("UPDATE users SET logo = ? WHERE id = ?");
            if ($query->execute([$upload_file, $user_id])) {
                echo "Логотип успешно загружен и профиль обновлен.";
            } else {
                echo "Ошибка обновления логотипа.";
            }
        } else {
            echo "Ошибка загрузки файла.";
        }
    } else {
        echo "Аватарка не загружена.";
    }

    header("Location: profile.php");
    exit();
}
?>

<DOCTYPE HTML>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="/static/styles/header_style.css">
        <link rel="stylesheet" href="static/styles/edit_profile_style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <title>Редактировать профиль</title>
    </head>
    <body>
        <?php
        if (isset($_SESSION["user_id"])) {
            require_once("header_auth.php");
        } else {
            require_once("header_unauth.php");
        }
        ?>

        <main>
          <div class="container">
            <div class="wrapper">
              <form action="edit_profile.php" method="post" enctype="multipart/form-data">

                <h1>Редактировать профиль</h1>

                <div class="user-logo">
                  <img src="<?php echo htmlspecialchars($user["logo"]) ?>">

                  <label for="input-file">Изменить</label>
                  <input type="file" accept="image/jpg, image/png, image/jpeg" id="input-file" name="logo">

                </div>

                  <div class="input-wrapper">

                    <div class="input-group">
                      <div class="input-box-name">
                        <p>Изменить имя</p>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                      </div>

                      <div class="input-box-pass">
                        <p>Изменить пароль</p>
                        <input type="text">
                      </div>
                    </div>

                    <div class="input-box-email">
                      <p>Изменить почту</p>
                      <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="input-box-description">
                      <p>Добавить описание</p>
                      <textarea name="description" maxlength="200"><?php echo htmlspecialchars($user['description']); ?></textarea>
                    </div>

                  </div>

                <button class="btn-change-name" type="submit">Сохранить</button>

              </form>
            </div>
          </div>
        </main>
    </body>
</html>
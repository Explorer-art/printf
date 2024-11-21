<?php
session_start();
include "db.php";

if (isset($_SESSION["user_id"]) && !isset($_GET["user"])) {
    $user_id = $_SESSION["user_id"];

    $query = $connection->prepare("SELECT * FROM users WHERE id = ?");
    $query->execute([$user_id]);
    $user = $query->fetch();

    if (!$user) {
        $data = [
            "success" => false,
            "message" => "Пользователь не найден"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(200);
        echo json_encode($data);
        exit();
    }
    ?>

    <!doctype html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="static/styles/profile_style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <title>Профиль <?php echo htmlspecialchars($user['username']) ?></title>
    </head>
    <body>
    <main>
        <div class="container">
            <div class="wrapper">
                <h1>Профиль</h1>
                <div class="logo">
                    <article class="item">
                        <div class="logo">
                            <img class="personLogo" src="<?php echo htmlspecialchars($user["logo"]) ?>" alt="Логотип">
                        </div>
                    </article>
                    <h2><?= htmlspecialchars($user['username']) ?></h2>
                    <p class="mail"><?= htmlspecialchars($user['email']) ?></p>
                    <p class="description">Обо мне</p>
                </div>

                <div class="container-image-wrapper">
                    <div class="container-image">
                        <div class="user-gallery">
                            <div class="photo"> </div>
                        </div>
                    </div>
                </div>

                <div class="edit-profile">
                    <a href="edit_profile.php">Редактировать профиль</a>
                </div>
            </div>
        </div>
    </main>
    </body>
    </html>

    <?php
} elseif (isset($_GET["user"])) {
    $user_id = $_GET["user"];

    $query = $connection->prepare("SELECT * FROM users WHERE id = ?");
    $query->execute([$user_id]);
    $user = $query->fetch();

    if (!$user) {
        $data = [
            "success" => false,
            "message" => "Пользователь не найден"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(200);
        echo json_encode($data);
        exit();
    }

    // Получаем фотографии пользователя
    $photosQuery = $connection->prepare("SELECT file_path FROM images WHERE user_id = ?");
    $photosQuery->execute([$user_id]);
    $photos = $photosQuery->fetchAll();
    ?>

    <!doctype html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="static/styles/profile_style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <title>Профиль <?php echo htmlspecialchars($user['username']) ?></title>
    </head>
    <body>
    <main>
        <div class="container">
            <div class="wrapper">
                <h1>Профиль</h1>
                <div class="logo">
                    <img src="<?php echo htmlspecialchars($user['logo']) ?>" alt="Логотип">
                    <h2><?= htmlspecialchars($user['username']) ?></h2>
                    <p class="mail"><?= htmlspecialchars($user['email']) ?></p>
                    <p class="description">Обо мне</p>
                </div>

                <div class="container-image-wrapper">
                    <div class="container-image">
                        <div class="user-gallery">
                            <?php if ($photos): ?>
                                <?php foreach ($photos as $photo): ?>
                                    <div class="photo-item">
                                        <img src="<?php echo htmlspecialchars($photo['file_path']); ?>" alt="User Photo" style="width:100px;height:auto;">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Фотографии отсутствуют.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="edit-profile">
                    <a href="edit_profile.php">Редактировать профиль</a>
                </div>
            </div>
        </div>
    </main>
    </body>
    </html>
<?php
} else {
    echo "Ошибка! Вы не авторизованы, по этому вы не можете посмотретб свой профиль.";
}
?>
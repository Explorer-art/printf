<?php
session_start();
include "db.php";

if (isset($_SESSION["user_id"]) && (!isset($_GET["user"]) || $_GET["user"] == $_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
} elseif (isset($_GET["user"])) {
    $user_id = $_GET["user"];
} else {
    echo "Ошибка! Вы не авторизованы, по этому вы не можете посмотреть свой профиль.";
    exit();
}

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

<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/static/styles/header_style.css">
    <link rel="stylesheet" href="static/styles/profile_style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Профиль <?= htmlspecialchars($user["username"]) ?></title>
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
                <h1>Профиль</h1>
                <div class="user-logo">
                    <img src="<?php echo htmlspecialchars($user["logo"]) ?>" alt="<?php echo htmlspecialchars($user["logo"]) ?>">
                    <h2><?= htmlspecialchars($user["username"]) ?></h2>
                    <p class="description"><?= htmlspecialchars($user["description"]) ?></p> <!-- Вывод описания -->
                </div>

                <?php
                $query = $connection->prepare("SELECT * FROM images WHERE user_id = ? ORDER BY created_at DESC");
                $query->execute([$user_id]);
                $images = $query->fetchAll();
                ?>

                <div class="container-image-wrapper">
                    <div class="container-image">
                        <div class="user-gallery">
                            <?php if ($images) {
                                foreach ($images as $image) {
                                    echo '<img class="image" id="' . htmlspecialchars($image["id"]) . '" src="' . htmlspecialchars($image["thumbnail_path"]) . '">';
                                }
                            } else {
                                ?>
                                <p>Изображений нет</p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <?php
                if (isset($_SESSION["user_id"]) && (!isset($_GET["user"]) || $_GET["user"] == $_SESSION["user_id"])) {
                    ?>
                    <div class="edit-profile">
                        <a href="edit_profile.php">Редактировать профиль</a>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </main>
<script>

    document.addEventListener('click', function(event) {
        const clickedElement = event.target;

        if (clickedElement.classList.contains('image')) {
            window.open('https://printf.mrprixter.ru/view.php?image=' + clickedElement.id, '_self');
        }
    });

</script>
</body>
</html>
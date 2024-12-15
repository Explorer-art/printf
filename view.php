<?php
session_start();
require_once("db.php");

if (!isset($_GET["image"])) {
	$data = [
        "success" => false,
        "message" => "Изображение не найдено!"
    ];

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($data);
    exit();
}

$image_id = $_GET["image"];

$query = $connection->prepare("SELECT * FROM images WHERE id = ?");
$query->execute([$image_id]);
$image = $query->fetch();

if (!$image) {
    $data = [
        "success" => false,
        "message" => "Изображение не найдено!"
    ];

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($data);
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="/static/styles/header_style.css">
	<link rel="stylesheet" href="static/styles/view_style.css">
	<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
	<title>Изображение <?= htmlspecialchars($image["title"]) ?></title>
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
    <div class="back">
        <a class="btn-back" href="javascript:history.back()"><i class='bx bx-left-arrow-alt'></i></a>
    </div>

	<div class="container">

		<div class="left-panel">
			<img class="user-image" src="<?= htmlspecialchars($image["file_path"]) ?>" alt="">
		</div>


		<div class="right-panel">
			<div class="block"></div>
			<div class="user-profile">

			<?php
			$query = $connection->prepare("SELECT * FROM users WHERE id = ?");
			$query->execute([$image["user_id"]]);
			$user = $query->fetch();
			?>

            <div class="user-profile">
                <img class="user-logo" src="<?= htmlspecialchars($user["logo"]) ?>" alt="Аватар">
                <a class="user-name" href="profile.php?user=<?= htmlspecialchars($image["user_id"]) ?>">
                    <?= htmlspecialchars($user["username"]) ?>
                </a>
            </div>

            </div>

			<h2><?= htmlspecialchars($image["title"]) ?></h2>

			<?php
			if (isset($_SESSION["user_id"]) && $_SESSION["user_id"] == $image["user_id"]) {
			?>
			<a class="btn-download" type="submit" href="<?= htmlspecialchars($image["file_path"]) ?>" download>Скачать</a>
			<input class="btn-delete" type="submit" value="Удалить" onclick="delete_image()"></input>
			<?php
			} else {
            ?>
            <a class="btn-download-guest" type="submit" href="<?= htmlspecialchars($image["file_path"]) ?>" download>Скачать</a>
            <?php
            }
			?>
		</div>
	</div>
</main>
<script>
	function delete_image() {
		var xhr = new XMLHttpRequest();

		xhr.open("GET", "https://mrprixter.ru/delete_image.php?image=<?= htmlspecialchars($image["id"]) ?>", true);

		xhr.onload = function() {
			if (xhr.status === 200) {
				window.open("https://mrprixter.ru", "_self");
			}
		}

		xhr.send();
	}
</script>
</body>
</html>
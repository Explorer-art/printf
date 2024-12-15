<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/static/styles/header_style.css">
    <link rel="stylesheet" href="/static/styles/index_style.css">
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<title>Printf</title>
</head>
<body>
	<?php
	session_start();

	if (isset($_SESSION["user_id"])) {
		require_once("header_auth.php");
	} else {
		require_once("header_unauth.php");
	}
	?>

	<br>

	<?php
		require_once("db.php");

		$images_per_page = 30; // Максимальное количество изображений на странице

		if (isset($_GET["page"])) {
			$page = $_GET["page"];
		} else {
			$page = 1;
		}

		$query = $connection->query("SELECT COUNT(*) FROM images");

		$total_images = $query->fetchColumn();
		$total_pages = ceil($total_images / $images_per_page);
		$image_offset = ($page - 1) * $images_per_page;

		if ($total_images == 0) {
			echo "Изображения не найдены";
			exit();
		}

		if ($page > $total_pages) {
			$page = $total_pages;
		}

		if ($page == 1) {
			$query = $connection->prepare("SELECT * FROM images ORDER BY created_at DESC LIMIT :images_per_page");
			$query->bindParam("images_per_page", $images_per_page, PDO::PARAM_INT);
			$query->execute();
		} elseif ($page == 2) {
			$query = $connection->prepare("SELECT * FROM images ORDER BY created_at DESC LIMIT :images_per_page OFFSET :images_per_page");
			$query->bindParam("images_per_page", $images_per_page, PDO::PARAM_INT);
			$query->execute();
		} else {
			$query = $connection->prepare("SELECT * FROM images ORDER BY created_at DESC LIMIT :images_per_page OFFSET :image_offset");
			$query->bindParam("images_per_page", $images_per_page, PDO::PARAM_INT);
			$query->bindParam("image_offset", $image_offset, PDO::PARAM_INT);
			$query->execute();
		}

		$images = $query->fetchAll();
		?>
    <main>
        <div class="container-img">
            <?php
            foreach ($images as $image) {
                echo '<div class="box" id="box">';
                echo '<img class="image" src="' . htmlspecialchars($image["thumbnail_path"]) . '" id="' . htmlspecialchars($image["id"]) . '" alt="Uploaded Image"/>';
                echo '</div>';
            }
            ?>
        </div>
    <?php
    $previos_page = $page - 1;
    $next_page = $page + 1;
    ?>
    <div class="btn-nav">
    	<?php
    	if (isset($_GET["page"]) && $page != 1) {
    	?>
    		<a class="back" href="index.php?page=<?= htmlspecialchars($previos_page) ?>">Вернуться</a>
    	<?php
    	}
    	?>
        <a class="next" href="index.php?page=<?= htmlspecialchars($next_page) ?>">Продолжить</a>
    </div>
    </main>
<script>

	document.addEventListener('click', function(event) {
		const clickedElement = event.target;

		if (clickedElement.classList.contains('image')) {
			window.open('https://mrprixter.ru/view.php?image=' + clickedElement.id, '_self');
		}
	});

</script>
</body>
</html>
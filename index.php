<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/static/styles/header_style.css">
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
		
		$images_per_page = 10;

		if (isset($_GET["page"])) {
			$page = $_GET["page"];
		} else {
			$page = 1;
		}

		$query = $connection->query("SELECT COUNT(*) FROM images");

		$total_images = $query->fetchColumn();
		$total_pages = ceil($total_images / $images_per_page);

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
			$query = $connection->prepare("SELECT * FROM images ORDER BY created_at DESC LIMIT :images_per_page OFFSET :page * :images_per_page");
			$query->bindParam("images_per_page", $images_per_page, PDO::PARAM_INT);
			$query->bindParam("page", $page, PDO::PARAM_STR);
			$query->execute();
		}

		$images = $query->fetchAll();

		foreach ($images as $image) {
			echo '<div>';
			echo '<p>"' . htmlspecialchars($image["title"]) . '"</p>';
			echo '<img src="' . htmlspecialchars($image["file_path"]) . '" width=250>';
			echo '<div>';
		}
	?>
</body>
</html>
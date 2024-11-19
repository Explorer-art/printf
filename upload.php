<?php
session_start();
require_once("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (!isset($_SESSION["user_id"])) {
		echo "Загружать файлы можно только авторизированным пользователям.";
		exit();
	}

	if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
		$upload_dir = "uploads/";

		$file_tmp_path = $_FILES["image"]["tmp_name"];
		$file_name = $_FILES["image"]["name"];
		$file_type = $_FILES["image"]["type"];
		$file_size = $_FILES["image"]["size"];

		if ($file_type == "image/png" or $file_type == "image/jpg" or $file_type == "image/jpeg") {
			$user_id = $_SESSION["user_id"];
			$file_path = $upload_dir . $user_id . "/" . uniqid("", true) . "_" . $file_name;

			if (!is_dir($upload_dir)) {
				mkdir($upload_dir, 0777, true);
			}

			if (!is_dir($upload_dir . $user_id)) {
				mkdir($upload_dir . $user_id, 0777, true);
			}

			if (move_uploaded_file($file_tmp_path, $file_path)) {
				$query = $connection->prepare("INSERT INTO images (user_id, file_name, file_path) VALUES (:user_id, :file_name, :file_path)");
				$query->bindParam("user_id", $user_id, PDO::PARAM_STR);
				$query->bindParam("file_name", $file_name, PDO::PARAM_STR);
				$query->bindParam("file_path", $file_path, PDO::PARAM_STR);
				$query->execute();
				
				echo "Файл загружен на сервер!";
				http_response_code(200);
			} else {
				echo "Ошибка при загрузке файлов.";
				http_response_code(500);
				exit();
			}
		} else {
			echo "Ошибка! Неподдерживаемый тип файла.";
			http_response_code(400);
			exit();
		}
	}
} else {
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/static/styles/header_style.css">
	<title>Upload</title>
</head>

<body>
	<?php
	if (isset($_SESSION["user_id"])) {
		require_once("header_auth.php");
	} else {
		require_once("header_unauth.php");
	}
	?>
	
	<form enctype="multipart/form-data" action="upload.php" method="post">
		<input type="file" accept="image/jpg, image/png, image/jpeg" name="image">

		<input type="submit" value="Загрузить изображение">
	</form>
</body>
</html>
<?php
}
?>
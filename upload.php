<?php
session_start();
require_once("db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (!isset($_SESSION["user_id"])) {
		$data = [
			"success" => false,
			"message" => "Загружать файлы можно только авторизированным пользователям."
		];

		header("Content-Type: application/json; charset=utf-8");
		http_response_code(400);
		echo json_encode($data);
		exit();
	}

	if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
		$upload_dir = "uploads/";
		
		if (isset($_POST["title"])) {
			$title = $_POST["title"];
		} else {
			$title = "Unknown title";
		}

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
				$query = $connection->prepare("INSERT INTO images (user_id, file_name, file_path, title) VALUES (:user_id, :file_name, :file_path, :title)");
				$query->bindParam("user_id", $user_id, PDO::PARAM_STR);
				$query->bindParam("file_name", $file_name, PDO::PARAM_STR);
				$query->bindParam("file_path", $file_path, PDO::PARAM_STR);
				$query->bindParam("title", $title, PDO::PARAM_STR);
				$query->execute();
			} else {
				$data = [
	                "success" => false,
	                "message" => "Ошибка при загрузке файлов."
	            ];

	            header("Content-Type: application/json; charset=utf-8");
	            http_response_code(500);
	            echo json_encode($data);
				exit();
			}
		} else {
			$data = [
                "success" => false,
                "message" => "Ошибка! Неподдерживаемый тип файла."
            ];

            header("Content-Type: application/json; charset=utf-8");
            http_response_code(400);
            echo json_encode($data);
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
	<link rel="stylesheet" href="/static/styles/upload_style.css">
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
	
	<div class="load-form">
		<form enctype="multipart/form-data" action="upload.php" method="post">
			<div class="load-file">

				<h1>Загрузка изображений</h1>

				<label class="select-img" for="input-file">
					<i class='bx bx-image-add'></i> Выберите изображение
				</label>

				<input type="file" accept="image/jpg,
				image/png, image/jpeg" id="input-file" name="image">

				<textarea class="text" maxlength="64" placeholder="Название" ></textarea>

				<label class="load" for="input-load">
					<i class='bx bxs-cloud-upload'></i>
					Загрузить
				</label>

				<input type="submit" id="input-load">

			</div>
		</form>
	</div>
</body>
</html>
<?php
}
?>
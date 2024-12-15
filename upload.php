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
			$title = "Unknown name";
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
				$file_ext = strtolower($file_type);

				switch ($file_ext) {
					case "image/png":
						$original_image = imagecreatefrompng($file_path);
						break;
					case "image/jpg":
					case "image/jpeg":
						$original_image = imagecreatefromjpeg($file_path);
						break;
					default:
						$data = [
							"success" => false,
							"message" => "Ошибка! Неподдерживаемый тип файла."
						];

						header("Content-Type: application/json; charset=utf-8");
						http_response_code(400);
						echo json_encode($data);
						exit();
				}

				$original_width = imagesx($original_image);
				$original_height = imagesy($original_image);

				if ($original_width > 1080 || $original_height > 1080) {
					$thumb_width = $original_width / 4;
					$thumb_height = $original_height / 4;
				} else if ($original_width > 500 || $original_height > 500) {
					$thumb_width = $original_width / 2;
					$thumb_height = $original_height / 2;
				} else {
					$thumb_width = $original_width;
					$thumb_height = $original_height;
				}

				$thumb_image = imagecreatetruecolor($thumb_width, $thumb_height);

				imagecopyresampled($thumb_image, $original_image, 0, 0, 0, 0, $thumb_width, $thumb_height, $original_width, $original_height);

				$thumb_path = $upload_dir . $user_id . "/" . uniqid("", true) . "_thumbnail_" . $file_name;

				switch ($file_ext) {
					case "image/png":
						imagepng($thumb_image, $thumb_path);
						break;
					case "image/jpg":
					case "image/jpeg":
						imagejpeg($thumb_image, $thumb_path);
						break;
					default:
						$data = [
							"success" => false,
							"message" => "Ошибка! Неподдерживаемый тип файла."
						];

						header("Content-Type: application/json; charset=utf-8");
						http_response_code(400);
						echo json_encode($data);
						exit();
				}

				imagedestroy($original_image);
				imagedestroy($thumb_image);

				$query = $connection->prepare("INSERT INTO images (user_id, title, file_name, file_path, thumbnail_path) VALUES (:user_id, :title, :file_name, :file_path, :thumbnail_path)");
				$query->bindParam("user_id", $user_id, PDO::PARAM_STR);
				$query->bindParam("title", $title, PDO::PARAM_STR);
				$query->bindParam("file_name", $file_name, PDO::PARAM_STR);
				$query->bindParam("file_path", $file_path, PDO::PARAM_STR);
				$query->bindParam("thumbnail_path", $thumb_path, PDO::PARAM_STR);
				$query->execute();

				$data = [
					"success" => true,
					"message" => "Файл успешно загружен!"
				];

				header("Content-Type: application/json; charset=utf-8");
				echo json_encode($data);
			} else {
				$data = [
					"success" => false,
					"message" => "Ошибка при загрузке файлов."
				];

				header("Content-Type: application/json; charset=utf-8");
				http_response_code(500);
				echo json_encode($data);
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
	<link rel="stylesheet" href="/static/styles/upload_style.css">
	<link rel="stylesheet" href="/static/styles/bar.css">
	<link rel="stylesheet" href="/static/styles/header_style.css">
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

				<div id="response-message" class="bar"></div>

				<label class="select-img" for="input-file">
					<i class='bx bx-image-add'></i> Выберите изображение
				</label>

				<input type="file" accept="image/jpg,
				image/png, image/jpeg" id="input-file" name="image">

				<textarea class="text" maxlength="64" placeholder="Название" name="title"></textarea>

				<label class="load" for="input-load">
					<i class='bx bxs-cloud-upload'></i>
					Загрузить
				</label>

				<input type="submit" id="input-load">

			</div>
		</form>
	</div>
<script>
    document.querySelector("form").addEventListener("submit", async (event) => {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        const responseMessage = document.getElementById("response-message");
        responseMessage.style.display = "none"; // Скрываем старое сообщение

        try {
            const response = await fetch(form.action, {
                method: form.method,
                body: formData,
            });

            const data = await response.json();

            responseMessage.style.display = "block";

            if (data.success) {
                responseMessage.textContent = data.message;
                responseMessage.classList.add("success");
                form.reset();
            } else {
                responseMessage.textContent = data.message;
                responseMessage.classList.add("error");
            }
        } catch (error) {

        }
    });

</script>
</body>
</html>
<?php
}
?>
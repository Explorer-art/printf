<?php
if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
	$upload_dir = "images/";

	$file_tmp_path = $_FILES["image"]["tmp_name"];
	$file_name = $_FILES["image"]["name"];
	$file_size = $_FILES["image"]["size"];
	$file_type = $_FILES["image"]["type"];
	$file_path = $upload_dir . uniqid() . "_" . $file_name;

	if (!is_dir($upload_dir)) {
		mkdir($upload_dir, 0777, true);
	}

	if (move_uploaded_file($file_tmp_path, $file_path)) {
		echo "Файл загружен на сервер!";
	} else {
		echo "Ошибка при загрузке файлов.";
	}
}
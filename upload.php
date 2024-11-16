<?php
if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
	$upload_dir = "images/";

	$file_tmp_path = $_FILES["file"]["tmp_name"];
	$file_name = $_FILES["file"]["name"];
	$file_size = $_FILES["file"]["size"];
	$file_type = $_FILES["file"]["type"];
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
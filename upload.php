<?php
#$used_id = $_SESSION["used_id"];
$upload_file = "images/" . basename($_FILES["images"]["tmp_name"]);

if (move_uploaded_file($_FILES["images"]["tmp_name"], $upload_file)) {
	echo "Файл загружен на сервер!";
} else {
	echo "Ошибка! Возможная атака на сервер через загрузку файла.";
}
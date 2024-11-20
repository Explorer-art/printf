<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $users_id = $_SESSION['users_id'];
    $logo_file = $_FILES['logo'];

    if (isset($logo_file) and $logo_file['error'] === UPLOAD_ERR_OK) {
        $target_dir = "/home/webadmin/printf/logo";
        $target_file = $target_dir . basename($logo_file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($logo_file["tmp_name"]);
        if ($check === false) {
            die("Файл не является изображением.");
        }

        if (file_exists($target_file)) {
            die("Изображение уже существует.");
        }

        if ($logo_file["size"] > 500000) {
            die("Файл слишком большой.");
        }

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            die("Разрешены только файлы JPG, JPEG, PNG и GIF.");
        }

        if (move_uploaded_file($logo_file["tmp_name"], $target_file))
        {
            $query = $connection->prepare("UPDATE profiles SET logo = ? WHERE id = ?");
            if ($query->execute([$target_file, $users_id])) {
                echo "Аватар успешно обновлен!";
            } else {
                echo "Ошибка при обновлении аватара.";
            }
        } else {
            die("Ошибка при загрузке файла.");
        }
    } else {
        die("Файл не был загружен.");
    }
}
?>
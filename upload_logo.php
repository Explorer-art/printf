<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $logo_file = $_FILES['logo'];

    if (isset($logo_file) and $logo_file['error'] === UPLOAD_ERR_OK) {
        $logo_dir = "/home/webadmin/printf/logo/";

        $image_file_type = strtolower(pathinfo($logo_file["name"], PATHINFO_EXTENSION));
        $unique_name = uniqid('avatar_', true) . '.' . $image_file_type; // добавляем префикс и расширение
        $logo_path = $logo_dir . $unique_name;

        $check = getimagesize($logo_file["tmp_name"]);
        if ($check === false) {
            die("Файл не является изображением.");
        }

        if ($logo_file["size"] > 1000000) {
            die("Файл слишком большой.");
        }

        if (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            die("Разрешены только файлы JPG, JPEG, PNG и GIF.");
        }

        if (move_uploaded_file($logo_file["tmp_name"], $logo_path)) {
            $query = $connection->prepare("UPDATE users SET logo = ? WHERE id = ?");
            if ($query->execute(["logo/" . $unique_name, $user_id])) {
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

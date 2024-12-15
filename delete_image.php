<?php
session_start();
require_once("db.php");

if (!isset($_SESSION["user_id"])) {
	$data = [
        "success" => false,
        "message" => "Вы не авторизованы!"
    ];

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(400);
    echo json_encode($data);
    exit();
}

if (!isset($_GET["image"])) {
	$data = [
        "success" => false,
        "message" => "Изображение не найдено!"
    ];

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(400);
    echo json_encode($data);
    exit();
}

$image_id = $_GET["image"];

$query = $connection->prepare("SELECT * FROM images WHERE id = ?");
$query->execute([$image_id]);
$image = $query->fetch();

if (!$image) {
    $data = [
        "success" => false,
        "message" => "Изображение не найдено!"
    ];

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($data);
    exit();
}

if ($_SESSION["user_id"] == $image["user_id"]) {
    $query = $connection->prepare("DELETE FROM images WHERE id = ?");

    if ($query->execute([$image_id])) {
        $data = [
            "success" => true,
            "message" => "Изображение удалено!"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(200);
        echo json_encode($data);
    } else {
        $data = [
            "success" => false,
            "message" => "Ошибка при удалении изображения!"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
    }
} else {
    $data = [
        "success" => false,
        "message" => "Вы не создатель изображения!"
    ];

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(400);
    echo json_encode($data);
    exit();
}
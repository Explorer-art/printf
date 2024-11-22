<?php
session_start();

if (isset($_SESSION["user_id"])) {
	session_destroy();

	header("Location: index.php");
} else {
	$data = [
		"success" => false,
		"message" => "Имя пользователя должно быть не менее 3 символов"
	];

	header("Content-Type: application/json; charset=utf-8");
	http_response_code(400);
}
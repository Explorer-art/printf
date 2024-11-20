<?php
session_start();

if (isset($_SESSION["user_id"])) {
	session_destroy();
	
	echo "Вы вышли из аккаунта!";
	http_response_code(200);
} else {
	echo "Вы не авторизованы";
	http_response_code(400);
}
<?php
session_start();

if (isset($_SESSION["user_id"])) {
	session_destroy();

	echo "Вы вышли из аккаунта!";
} else {
	echo "Вы не авторизованы";
}
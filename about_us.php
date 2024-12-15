<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="/static/styles/about_style.css">
	<link rel="stylesheet" href="/static/styles/header_style.css">
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<title>О нас</title>
</head>
<body>
<?php
	session_start();

	if (isset($_SESSION["user_id"])) {
		require_once("header_auth.php");
	} else {
		require_once("header_unauth.php");
	}
?>
<img src="/static/images/About.png" alt="">
<div class="container">
		<div class="wrapper">
			<h1>О нас</h1>
			<p class="about">
				Printf — это сервис на котором вы сможете поделиться изображениями с другими пользователями.
			</p>
			<br>
			<p>
				Разработкой сайта занимается команда из 3 человек. 1 frontend и 2 backend разработчика.
				Этот сайт выступает в качестве учебного проекта.
			</p>
			<div class="card-wrapper">
				<div class="card-front">
					<p class="front-head">Илья</p>

					<p class="front">Frontend разработчик</p>
					<i class='bx bxs-user-rectangle'></i>
				</div>

				<div class="card-back1">
					<p class="back1-head">Денис</p>

					<p class="back1">Backend разработчик</p>
					<i class='bx bxs-user-rectangle'></i>
				</div>

				<div class="card-back2">
					<p class="back2-head">Евгений</p>

					<p class="back2">Backend разработчик</p>
					<i class='bx bxs-user-rectangle'></i>
				</div>
			</div>

		</div>
</div>
</body>
</html>
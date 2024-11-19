<header>
	<div class="logo">PRINTF</div>
	<nav>
		<ul class="nav_links">
			<li><a href="/">Главная</a> </li>
			<li><a href="upload.php">Загрузить</a> </li>
			<li><a href="about_us.php">О нас</a> </li>
		</ul>
	</nav>

	<div class="main">
		<a href="profile.php" class="reg">
			<?php
			require_once("db.php");

			$user_id = $_SESSION["user_id"];

			$query = $connection->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
			$query->bindParam("id", $user_id, PDO::PARAM_STR);
			$query->execute();

			$user = $query->fetch();

			echo $user["username"];
			?>
			</a>
		<a href="logout.php" class="user">Выход</a>
	</div>
</header>
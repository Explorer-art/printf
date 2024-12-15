<header>
	<div class="logo"><a href="index.php">PRINTF</a></div>
	<nav>
		<ul class="nav_links">
			<li><a href="index.php">Главная</a> </li>
			<li><a href="upload.php">Загрузить</a> </li>
			<li><a href="about_us.php">О нас</a> </li>
		</ul>
	</nav>

	<div class="main">
		<a href="profile.php" class="reg">
			<?php
			require_once("db.php");

			$_user_id = $_SESSION["user_id"];

			$query = $connection->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
			$query->bindParam("id", $_user_id, PDO::PARAM_STR);
			$query->execute();

			$_user = $query->fetch();

			echo $_user["username"];
			?>
			</a>
		<a href="logout.php" class="user">Выход</a>
	</div>
</header>
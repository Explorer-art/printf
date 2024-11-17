<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'] )){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = $connection->prepare("SELECT * FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();

if(!$user){
    echo "Пользователь не найден.";
    exit();
}
?>

<h1>Профиль пользователя</h1>
<p>Имя: <?= htmlspecialchars($user['username']) ?></p>
<p>Почта: <?= htmlspecialchars($user['email']) ?></p>
<a href = "edit_profile.php">Редактировать профиль</a>
<br>
<a href = "logout.php">Выйти</a>
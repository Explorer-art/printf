<?php
session_start();
include 'db.php';

if(!isset($_GET['id'])){
    echo "ID пользователь не указан. ";
    exit();
}

$user_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();


if(!$user){

    echo "Пользователь не найден";
    exit();
}
?>;

<h1>Профиль пользователя</h1>
<p>Имя: <?= htmlspecialchars($user['username'])?> </p>

<a href = "index.php">Назад</a>
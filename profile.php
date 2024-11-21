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
    $data = [
        "success" => false,
        "message" => "Пользователь не найден"
    ];

    header("Content-Type: application/json; charset=utf-8");
    http_response_code(200);
    echo json_encode($data);
    exit();
}
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="static/styles/profile_style.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Document</title>
</head>
<body>
<main>
  <div class="container">
    <div class="wrapper">
      <form action="" method="">
        <h1>Профиль</h1>
        <div class="logo">
          <img src="<?php echo htmlspecialchars($user['logo']) ?>">
          <h2><?= htmlspecialchars($user['username']) ?></h2>
          <p class="mail"><?= htmlspecialchars($user['email']) ?></p>
          <p class="description">Обо мне</p>
        </div>

        <div class="container-image-wrapper">
          <div class="container-image">
            <div class="user-gallery">
              <div class="img1"></div>
              <div class="img2"></div>
              <div class="img3"></div>
              <div class="img4"></div>
              <div class="img5"></div>
              <div class="img6"></div>
            </div>
          </div>
        </div>


        <div class="edit-profile">
          <a href="edit_profile.php">Редактировать профиль</a>
        </div>
      </form>
    </div>
  </div>
</main>
</body>
</html>
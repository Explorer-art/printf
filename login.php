<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();
    require_once("db.php");

    $username = $_POST["username"];
    $password = $_POST["password"];

    $query = $connection->prepare("SELECT * FROM users WHERE username = :username LIMIT 1"); # Выбрать все записи (*) из таблицы users в которых username = $username, но количество записей в результате не должно превышать 1 (LIMIT 1)
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() == 1) {
        $user = $query->fetch(); # Извлекаем данные пользователя полученные из базы данных

        # Функция password_verify() сравнивает пароль (перед этим его хеширует) с хешем пароля в базе данных
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"]; # Сохраняем сессию пользователя

            echo "Добро пожаловать, " . $user["username"] . "!";
        } else {
            echo "Имя пользователя или пароль неверный!";
        }
    } else {
        echo "Имя пользователя или пароль неверный!";
    }
} else {
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="/static/styles/login_style.css">
        <link rel="stylesheet" href="/static/styles/header_style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
        <?php include("header.php");?>
        
        <div class="wrapper">
            <form action="login.php" method="post">
                <h1>Вход</h1>
                <div class="input-box">
                    <input type="text" placeholder="Имя пользоватля" name="username" required>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="Пароль" name="password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div class="remember-forgot">
                    <label><input type="checkbox"> Запомнить меня</label>
                    <a href="#">Забыли пароль?</a>
                </div>

                <button type="submit" class="btn">Вход</button>

                <div class="register-link">
                    <p>Нет аккаунта? <a href="register.html">Регистрация</a></p>
                </div>
            </form>
        </div>
    </body>
</html>
<?php
}
?>
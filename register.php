<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();
    require_once("db.php");

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_hash = password_hash($password, PASSWORD_BCRYPT); # Хеширование пароля

    if (strlen($username) < 3) {
        echo "Имя пользователя должно быть не менее 3 символов";
        header("HTTP/1.1 400 OK"); # Возвращаем статус-код ответа
        exit();
    }

    if (strlen($username) > 25) {
        echo "Имя пользователя не должно быть более 25 символов";
        header("HTTP/1.1 400 OK");
        exit();
    }

    $query = $connection->prepare("SELECT * FROM users WHERE username = :username"); # Выбрать все записи (*) из таблицы users в которых username = $username
    $query->bindParam("username", $username, PDO::PARAM_STR); # Устанавливаем параметр username в запрос выше
    $query->execute(); # Выполняем запрос

    if ($query->rowCount() > 0) {
        echo "Это имя пользователя уже занято!";
        header("HTTP/1.1 400 OK");
        exit();
    }

    $query = $connection->prepare("SELECT * FROM users WHERE email = :email AND username != :username");
    $query->bindParam("email", $email, PDO::PARAM_STR);
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        echo "Этот адрес электронной почты уже зарегистрирован";
        header("HTTP/1.1 400 OK");
        exit();
    }

    $query = $connection->prepare("INSERT INTO users(username,email,password) VALUES (:username,:email,:password_hash)"); # Добавляем нового пользователя
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->bindParam("email", $email, PDO::PARAM_STR);
    $query->bindParam("password_hash", $password_hash, PDO::PARAM_STR);
    $result = $query->execute();
        
    if ($result) {
        header("HTTP/1.1 200 OK");
        header("Location: profile.php");
    } else {
        echo "Неверные данные!";
        header("HTTP/1.1 400 OK");
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
        <link rel="stylesheet" href="/static/styles/reg_style.css">
        <link rel="stylesheet" href="/static/styles/header_style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
        
        <div class="wrapper">
            <form action="register.php" method="post">
                <h1>Регистрация</h1>
                <div class="input-box">
                    <input type="text" placeholder="Имя пользователя" name="username" required>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <input type="email" placeholder="Электронная почта" name="email" required>
                    <i class='bx bxs-envelope'></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="Пароль" name="password" required>
                    <i class='bx bxs-lock-alt' ></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="Подтвердите пароль" name="repeatpass" required>
                    <i class='bx bxs-lock-alt' ></i>
                </div>

                <button type="submit" class="btn">Регистрация</button>

                <div class="register-link">
                    <p>Уже есть аккаунт? <a href="login.html">Вход</a></p>
                </div>
            </form>
        </div>
    </body>
</html>
<?php
}
?>
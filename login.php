<?php
session_start();

if(isset($_SESSION["user_id"] )){
    header("Location: profile.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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

            $data = [
                "success" => true,
                "message" => "Успешная авторизация!"
            ];

            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($data);
        } else {
            $data = [
                "success" => false,
                "message" => "Имя пользователя или пароль неверный!"
            ];

            header("Content-Type: application/json; charset=utf-8");
            http_response_code(400);
            echo json_encode($data);
        }
    } else {
        $data = [
            "success" => false,
            "message" => "Имя пользователя или пароль неверный!"
            ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
    }
} else {
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="/static/styles/login_style.css">
    <link rel="stylesheet" href="/static/styles/bar.css">
    <link rel="stylesheet" href="/static/styles/header_style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <?php
    if (isset($_SESSION["user_id"])) {
        require_once("header_auth.php");
    } else {
        require_once("header_unauth.php");
    }
    ?>

    <main>
        <div class="wrapper">
            <form action="login.php" method="post" id="login-form">
                <h1>Вход</h1>
                <div id="error-message" class="error-message"></div>
                <div class="input-box">
                    <input type="text" placeholder="Имя пользователя" name="username" required>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <input type="password" placeholder="Пароль" name="password" required id="password">
                    <i class='bx bxs-lock-alt' id="toggle-password"></i>
                </div>

                <button type="submit" class="btn">Войти</button>

                <div class="register-link">
                    <p>Нет аккаунта? <a href="register.php">Регистрация</a></p>
                </div>
            </form>
        </div>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');

        togglePassword.addEventListener('click', () => {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            if (isPassword) {
                togglePassword.classList.remove('bxs-lock-alt');
                togglePassword.classList.add('bxs-lock-open-alt');
            } else {
                togglePassword.classList.remove('bxs-lock-open-alt');
                togglePassword.classList.add('bxs-lock-alt');
            }
        });

        // Слушатель на форму для обработки ошибок
        document.getElementById("login-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Предотвращаем отправку формы

            const errorMessageElement = document.getElementById("error-message");
            errorMessageElement.style.display = "none"; // Скрываем старые ошибки

            const username = document.querySelector('input[name="username"]').value;
            const password = document.querySelector('input[name="password"]').value;

            if (!username || !password) {
                errorMessageElement.textContent = "Заполните все поля!";
                errorMessageElement.style.display = "block";
                return;
            }

            // Делаем асинхронный запрос к серверу
            fetch("login.php", {
                method: "POST",
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    errorMessageElement.textContent = data.message;
                    errorMessageElement.style.display = "block"; // Показываем сообщение об ошибке
                } else {
                    window.location.href = "index.php"; // Переход на страницу профиля
                }
            })
        });
    </script>
</body>
</html>

<?php
}
?>
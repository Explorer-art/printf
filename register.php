<?php
session_start();

if(isset($_SESSION['user_id'] )){
    header("Location: profile.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once("db.php");

    $secret = "ES_e2bf607731ec4377b6533d8bddf54aa8";

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $password_hash = password_hash($password, PASSWORD_BCRYPT); # Хеширование пароля
    $default_logo = "logo/user-img.svg";

    if (strlen($username) < 3) {
        $data = [
            "success" => false,
            "message" => "Имя пользователя должно быть не менее 3 символов"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
        exit();
    }

    if (strlen($username) > 25) {
        $data = [
            "success" => false,
            "message" => "Имя пользователя не должно быть более 25 символов"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
        exit();
    }

    $query = $connection->prepare("SELECT * FROM users WHERE username = :username"); # Выбрать все записи (*) из таблицы users в которых username = $username
    $query->bindParam("username", $username, PDO::PARAM_STR); # Устанавливаем параметр username в запрос выше
    $query->execute(); # Выполняем запрос

    if ($query->rowCount() > 0) {
        $data = [
            "success" => false,
            "message" => "Это имя пользователя уже занято!"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
        exit();
    }

    $query = $connection->prepare("SELECT * FROM users WHERE email = :email AND username != :username");
    $query->bindParam("email", $email, PDO::PARAM_STR);
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        $data = [
            "success" => false,
            "message" => "Этот адрес электронной почты уже зарегистрирован"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
        exit();
    }

    $verify_data = array(
        "secret" => $secret,
        "response" => $_POST["h-captcha-response"]
    );

    $verify = curl_init();

    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($verify_data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($verify, CURLOPT_VERBOSE, false);

    $response = curl_exec($verify);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        $data = [
            "success" => false,
            "message" => "Ошибка при прохождении капчи!"
        ];

        header("Content-Type: application/json; charset=utf-8");
        http_response_code(400);
        echo json_encode($data);
        exit();
    }

    $query = $connection->prepare("INSERT INTO users(username, email, password, logo) VALUES (:username, :email, :password_hash, :logo)"); # Добавляем нового пользователя
    $query->bindParam("username", $username, PDO::PARAM_STR);
    $query->bindParam("email", $email, PDO::PARAM_STR);
    $query->bindParam("password_hash", $password_hash, PDO::PARAM_STR);
    $query->bindParam("logo", $default_logo, PDO::PARAM_STR);
    $result = $query->execute();
        
    if ($result) {
        $query = $connection->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $query->bindParam("username", $username, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() == 1) {
            $user = $query->fetch();

            $_SESSION["user_id"] = $user["id"];

            $data = [
                "success" => true,
                "message" => "Успешная регистрация!"
            ];

            header("Content-Type: application/json; charset=utf-8");
            echo json_encode($data);
        } else {
            $data = [
                "success" => false,
                "message" => "Ошибка при регистрации нового пользователя!"
            ];

            header("Content-Type: application/json; charset=utf-8");
            http_response_code(400);
            echo json_encode($data);
        }
    } else {
        $data = [
            "success" => false,
            "message" => "Неверные данные!"
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
        <title>Регистрация</title>
        <link rel="stylesheet" href="/static/styles/reg_style.css">
        <link rel="stylesheet" href="/static/styles/bar.css">
        <link rel="stylesheet" href="/static/styles/header_style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <script src='https://www.hCaptcha.com/1/api.js' async defer></script>
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
            <form id="register-form" action="register.php" method="post">
                <h1>Регистрация</h1>

                <div id="error-message" class="bar error"></div>

                <div class="input-box">
                    <input type="text" placeholder="Имя пользователя" name="username" required>
                    <i class='bx bxs-user'></i>
                </div>

                <div class="input-box">
                    <input type="email" placeholder="Электронная почта" name="email" required>
                    <i class='bx bxs-envelope'></i>
                </div>

                <div class="input-box">
                    <input type="password" class="password" placeholder="Пароль" name="password" required>
                    <i class='bx bxs-lock-alt toggle-password' ></i>
                </div>

                <div class="input-box">
                    <input type="password" class="password" placeholder="Подтвердите пароль" name="repeatpass" required>
                    <i class='bx bxs-lock-alt toggle-password' ></i>
                </div>

                <div class="h-captcha" data-sitekey="e4328c08-3ca0-471e-8e30-695673060730"></div>

                <button type="submit" class="btn">Регистрация</button>

                <div class="register-link">
                    <p>Уже есть аккаунт? <a href="login.php">Вход</a></p>
                </div>
            </form>
        </div>
    </main>
    </body>

    <script>
        document.getElementById("register-form").addEventListener("submit", async function (event) {
            event.preventDefault(); // Останавливаем стандартное поведение формы

            const errorMessageElement = document.getElementById("error-message");
            const formData = new FormData(this); // Получаем данные из формы

            // Получаем значения паролей
            const password = formData.get("password");
            const repeatPassword = formData.get("repeatpass");

            errorMessageElement.style.display = "none"; // Прячем предыдущие ошибки
            errorMessageElement.textContent = "";

            // Проверяем совпадение паролей
            if (password !== repeatPassword) {
                errorMessageElement.textContent = "Пароли не совпадают!";
                errorMessageElement.style.display = "block";
                return; // Останавливаем дальнейшее выполнение, если пароли не совпадают
            }

            try {
                // Отправляем запрос на сервер
                const response = await fetch("register.php", {
                    method: "POST",
                    body: formData,
                });

                const data = await response.json(); // Получаем ответ в формате JSON

                if (response.ok) {
                    // Если регистрация успешна
                    window.location.href = "index.php"; // Перенаправляем на страницу профиля
                } else {
                    // Если сервер вернул ошибку
                    errorMessageElement.textContent = data["message"];
                    errorMessageElement.style.display = "block";
                }
            } catch (error) {
                // Убираем обработку ошибки сети
            }
        });


        // Получаем все элементы, связанные с паролем и иконками
        const passwordInputs = document.querySelectorAll('input.password'); // Все поля пароля
        const toggleIcons = document.querySelectorAll('.toggle-password'); // Иконки для переключения

        // Перебираем все иконки
        toggleIcons.forEach((icon, index) => {
            icon.addEventListener('click', () => {
                const isPassword = passwordInputs[index].getAttribute('type') === 'password'; // Проверяем, скрыт ли сейчас пароль
                passwordInputs[index].setAttribute('type', isPassword ? 'text' : 'password'); // Переключаем тип поля

                // Переключаем иконку
                if (isPassword) {
                    icon.classList.remove('bxs-lock-alt'); // Скрытый пароль
                    icon.classList.add('bxs-lock-open-alt'); // Открытый пароль
                } else {
                    icon.classList.remove('bxs-lock-open-alt'); // Открытый пароль
                    icon.classList.add('bxs-lock-alt'); // Скрытый пароль
                }
            });
        });

    </script>
</html>
<?php
}
?>
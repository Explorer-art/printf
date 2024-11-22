<?php
session_start();

if(isset($_SESSION['user_id'] )){
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
            
            // $data = [
            //     "success" => true,
            //     "message" => "Успешная авторизация!"
            // ];

            // header("Content-Type: application/json; charset=utf-8");
            header("Location: profile.php");
            // http_response_code(200);
            // echo json_encode($data);
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
        <link rel="stylesheet" href="/static/styles/header_style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
              <form action="login.php" method="post">
                  <h1>Вход</h1>
                  <div class="input-box">
                      <input type="text" placeholder="Имя пользоватля" name="username" required>
                      <i class='bx bxs-user'></i>
                  </div>
  
                  <div class="input-box">
                      <input id="password" type="password" placeholder="Пароль" name="password" required>
                        <i class='bx bxs-lock-alt' id="toggle-password"></i>

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
        </script>
    </body>
</html>
<?php
}
?>
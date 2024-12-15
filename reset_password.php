<?php

require_once 'api.php';


require_once('db.php');


$from_email = "mrprixter.ru";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['email'])) {

        $email = $_POST['email'];


        $query = $connection->prepare("SELECT * FROM users WHERE email = :email");
        $query->bindParam(':email', $email);
        $query->execute();

        if ($query->rowCount() > 0) {

            $token = bin2hex(random_bytes(50));
            $query = $connection->prepare("INSERT INTO password_resets (email, token) VALUES (:email, :token)");
            $query->bindParam(':email', $email);
            $query->bindParam(':token', $token);
            $query->execute();


            $subject = "Сброс пароля";
            $message = "Перейдите по следующей ссылке, чтобы сбросить пароль: ";
            $message .= "http://yourwebsite.com/password-reset.php?token=" . $token;


            $headers = "From: " . $from_email . "\r\n";
            $headers .= "Reply-To: " . $from_email . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            mail($email, $subject, $message, $headers);

            echo "Письмо для сброса пароля отправлено!";
        } else {
            echo "Пользователь с такой электронной почтой не найден.";
        }
    } elseif (isset($_POST['new_password']) && isset($_POST['token'])) {

        $token = $_POST['token'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);


        $query = $connection->prepare("SELECT email FROM password_resets WHERE token = :token");
        $query->bindParam(':token', $token);
        $query->execute();

        if ($query->rowCount() > 0) {
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $email = $row['email'];


            $query = $connection->prepare("UPDATE users SET password = :password WHERE email = :email");
            $query->bindParam(':password', $new_password);
            $query->bindParam(':email', $email);
            if ($query->execute()) {
                echo "Пароль успешно обновлен.";
                // Удаление токена после использования
                $query = $connection->prepare("DELETE FROM password_resets WHERE token = :token");
                $query->bindParam(':token', $token);
                $query->execute();
            }
        } else {
            echo "Неверный или устаревший токен.";
        }
    }
}
?>

<form method="post" action="">
    <h2>Сброс пароля</h2>
    <label for="email">Введите вашу электронную почту:</label>
    <input type="email" name="email" required>
    <button type="submit">Отправить ссылку для сброса пароля</button>
</form>


<?php if (isset($_GET['token'])): ?>
    <form method="post" action="">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <label for="new_password">Новый пароль:</label>
        <input type="password" name="new_password" required>
        <button type="submit">Обновить пароль</button>
    </form>
<?php endif; ?>

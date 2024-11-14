<!DOCTYPE html>
 <html lang = "en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="x-ua-compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>


<body>

<form action="register.php" method="post">
    <input type = "text" placeholder="login" name="login">
    <input type = "text" placeholder="password" name="pass">
    <input type = "text" placeholder="repeat password" name="repeatpass">
    <input type = "text" placeholder="email" name="email">
    <button type="submit">Зарегистрироваться</button>
</form>


<form action="login.php" method="post">
    <input type = "text" placeholder="login" name="login">
    <input type = "text" placeholder="password" name="pass">
    <button type="submit">Войти</button>

</form>








    <?php
    ?>
</body>
</html>
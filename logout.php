<?php
session_start();

if (isset($_SESSION['sotrudnik_id'])) {
    // Уничтожаем сессию
    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выход</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Вы вышли из своего аккаунта</h1>
    <p>Спасибо за использование нашего сервиса!</p>
    <button onclick="window.location.href='index.php'">На главную страницу</button>
    <button onclick="window.location.href='login.php'">Войти заново</button>
</body>
</html>
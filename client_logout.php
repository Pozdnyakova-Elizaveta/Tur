<?php
session_start();

// Уничтожаем сессию, если она существует
if (isset($_SESSION['client_id'])) {
    session_unset();
    session_destroy();
}
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Выход</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(45deg, #49a09d, #5f2c82);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .container {
            width: 100%;
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        h1 {
            color: #333;
        }

        button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 16px;
            background-color: #49a09d;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            border-radius: 5px;
        }

        button:hover {
            background-color: #5f2c82;
        }

        .message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Вы вышли из своего аккаунта</h1>
        <p>Спасибо за использование нашего сервиса!</p>
        <button onclick="window.location.href='tur.php'">На главную страницу</button>
        <button onclick="window.location.href='client_auth.php'">Войти заново</button>
    </div>
</body>
</html>
<?php
require 'config.php'; // подключение к базе данных

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Авторизация
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);

        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM \"Sotrudnik\" WHERE \"Email\" = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Здесь можно сохранить информацию о пользователе в сессии
                    session_start();
                    $_SESSION['user_id'] = $user['PK_Sotrudnik'];
                    // Перенаправление на защищенную страницу
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $message = 'Пользователь не найден.';
                }
            } catch (PDOException $e) {
                $message = 'Ошибка авторизации: ' . $e->getMessage();
            }
        } else {
            $message = 'Пожалуйста, введите корректный адрес электронной почты.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация Сотрудника</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(45deg, #49a09d, #5f2c82); 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            box-sizing: border-box; 
        }

        .container {
            width: 100%;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.9); 
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3); 
            text-align: center;
            box-sizing: border-box;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 22px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="email"] {
            width: calc(100% - 20px); 
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-sizing: border-box;
            outline: none; 
        }

        input[type="email"]:focus {
            border-color: #49a09d; 
            box-shadow: 0 0 8px rgba(73, 160, 157, 0.5);
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #49a09d;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, box-shadow 0.3s;
        }

        button:hover {
            background-color: #5f2c82;
            box-shadow: 0 4px 10px rgba(95, 44, 130, 0.5);
        }

        .message {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Авторизация Сотрудника</h1>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="email" name="email" placeholder="Электронная почта" required>
            <button type="submit" name="login">Войти</button>
        </form>
    </div>
</body>
</html>
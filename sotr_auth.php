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
</head>
<body>
    <h1>Авторизация Сотрудника</h1>

    <?php if ($message): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="email" name="email" placeholder="Электронная почта" required>
        <button type="submit" name="login">Войти</button>
    </form>

    </body>
</html>
<?php
require 'config.php'; // Подключение к базе данных

$message = '';

// Получение данных о поле
$genders = [];
try {
    $stmt = $pdo->query("SELECT \"PK_Pol\", \"Nazv_Pol\" FROM \"Pol\"");
    $genders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения пола: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registration or login
    if (isset($_POST['register'])) {
        // Регистрация
        $familia = trim($_POST['familia']);
        $imya = trim($_POST['imya']);
        $otchestvo = trim($_POST['otchestvo']);
        $data_rozhd = trim($_POST['data_rozhd']);
        $nomer_tel = trim($_POST['nomer_tel']);
        $pochta = trim($_POST['pochta']);
        $pk_pol = intval($_POST['pk_pol']);
        
        if ($familia && $imya && $data_rozhd && filter_var($pochta, FILTER_VALIDATE_EMAIL) && $pk_pol) {
            try {
                $stmt = $pdo->prepare("INSERT INTO \"Klient\" (\"Famil_Klient\", \"Imya_Klient\", \"Otchestvo_Klient\", \"Data_Rozhd\", \"Nomer_Tel_Klient\", \"Pochta_Klient\", \"PK_Pol\") VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$familia, $imya, $otchestvo, $data_rozhd, $nomer_tel, $pochta, $pk_pol]);
                $message = 'Регистрация успешна!';
            } catch (PDOException $e) {
                $message = 'Ошибка регистрации: ' . $e->getMessage();
            }
        } else {
            $message = 'Пожалуйста, заполните все поля корректно.';
        }
    } elseif (isset($_POST['login'])) {
        // Авторизация
        // Для упрощения мы будем авторизовывать по почте
        $pochta = trim($_POST['pochta']);

        if ($pochta) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM \"Klient\" WHERE \"Pochta_Klient\" = ?");
                $stmt->execute([$pochta]);
                $client = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($client) {
                    // Здесь можно сохранить информацию о клиенте в сессии
                    session_start();
                    $_SESSION['client_id'] = $client['PK_Klient'];
                    header("Location: client_dashboard.php"); // перенаправление на страницу после входа
                    exit;
                } else {
                    $message = 'Клиент не найден.';
                }
            } catch (PDOException $e) {
                $message = 'Ошибка авторизации: ' . $e->getMessage();
            }
        } else {
            $message = 'Пожалуйста, введите адрес электронной почты.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация и Авторизация Клиента</title>
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
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input, select, button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input:focus, select:focus {
            border-color: #49a09d;
            box-shadow: 0 0 8px rgba(73, 160, 157, 0.5);
            outline: none;
        }

        button {
            background-color: #49a09d;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
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
        <h1>Регистрация и Авторизация</h1>
        <?php if ($message): ?>
            <p class="message"> <?= htmlspecialchars($message) ?> </p>
        <?php endif; ?>

        <form method="post">
            <h2>Регистрация</h2>
            <input type="text" name="familia" placeholder="Фамилия" required>
            <input type="text" name="imya" placeholder="Имя" required>
            <input type="text" name="otchestvo" placeholder="Отчество" required>
            <input type="date" name="data_rozhd" required>
            <input type="text" name="nomer_tel" placeholder="Номер телефона" required>
            <input type="email" name="pochta" placeholder="Электронная почта" required>
            <select name="pk_pol" required>
                <option value="">--Выберите пол--</option>
                <?php foreach ($genders as $gender): ?>
                    <option value="<?= $gender['PK_Pol'] ?>">
                        <?= htmlspecialchars($gender['Nazv_Pol']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="register">Зарегистрироваться</button>
        </form>

        <form method="post">
            <h2>Авторизация</h2>
            <input type="email" name="pochta" placeholder="Электронная почта" required>
            <button type="submit" name="login">Войти</button>
        </form>
    </div>
</body>
</html>
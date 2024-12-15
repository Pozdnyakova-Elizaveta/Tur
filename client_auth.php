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
</head>
<body>
    <h1>Регистрация Клиента</h1>

    <?php if ($message): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <h2>Регистрация</h2>
        <input type="text" name="familia" placeholder="Фамилия" required>
        <input type="text" name="imya" placeholder="Имя" required>
        <input type="text" name="otchestvo" placeholder="Отчество" required>
        <input type="date" name="data_rozhd" placeholder="Дата рождения" required>
        <input type="text" name="nomer_tel" placeholder="Номер телефона" required>
        <input type="email" name="pochta" placeholder="Электронная почта" required>

        <label for="pk_pol">Выберите пол:</label>
        <select name="pk_pol" required>
            <option value="">--Выберите пол--</option>
            <?php foreach ($genders as $gender): ?>
                <option value="<?= $gender['PK_Pol'] ?>"><?= htmlspecialchars($gender['Nazv_Pol']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="register">Зарегистрироваться</button>
    </form>

    <form method="post">
        <h2>Авторизация</h2>
        <input type="email" name="pochta" placeholder="Электронная почта" required>
        <button type="submit" name="login">Войти</button>
    </form>
</body>
</html>
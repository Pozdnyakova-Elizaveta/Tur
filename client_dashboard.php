<?php
session_start();
require 'config.php'; // Подключение к базе данных

if (!isset($_SESSION['client_id'])) {
    header("Location: client_auth.php"); // Перенаправление на страницу входа, если пользователь не авторизован
    exit;
}

// Получение информации о клиенте
$client_id = $_SESSION['client_id'];
$client = [];
try {
    $stmt = $pdo->prepare("
        SELECT K.*, P.\"Nazv_Pol\" 
        FROM \"Klient\" K 
        LEFT JOIN \"Pol\" P ON K.\"PK_Pol\" = P.\"PK_Pol\" 
        WHERE K.\"PK_Klient\" = ?
    ");
    $stmt->execute([$client_id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Ошибка получения данных о клиенте: ' . $e->getMessage();
}

// Получение истории бронирований
$purchaseHistory = [];
$errorMessage = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM purchase_history(:client_id)");
    $stmt->execute(['client_id' => $client_id]);
    $purchaseHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Ошибка при выполнении запроса: " . $e->getMessage();
}

// Обработка редактирования информации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $famil_klient = trim($_POST['famil_klient']);
    $imya_klient = trim($_POST['imya_klient']);
    $otchestvo_klient = trim($_POST['otchestvo_klient']);
    $data_rozhd = trim($_POST['data_rozhd']);
    $nomer_tel_klient = trim($_POST['nomer_tel_klient']);
    $pochta_klient = trim($_POST['pochta_klient']);
    $pk_pol = intval($_POST['pk_pol']);

    try {
        $stmt = $pdo->prepare("
            UPDATE \"Klient\" 
            SET \"Famil_Klient\" = ?, \"Imya_Klient\" = ?, \"Otchestvo_Klient\" = ?, \"Data_Rozhd\" = ?, \"Nomer_Tel_Klient\" = ?, \"Pochta_Klient\" = ?, \"PK_Pol\" = ? 
            WHERE \"PK_Klient\" = ?
        ");
        $stmt->execute([$famil_klient, $imya_klient, $otchestvo_klient, $data_rozhd, $nomer_tel_klient, $pochta_klient, $pk_pol, $client_id]);
        $client['Famil_Klient'] = $famil_klient; // Обновляем информацию для представления
        $client['Imya_Klient'] = $imya_klient; 
        $client['Otchestvo_Klient'] = $otchestvo_klient; 
        $client['Data_Rozhd'] = $data_rozhd; 
        $client['Nomer_Tel_Klient'] = $nomer_tel_klient; 
        $client['Pochta_Klient'] = $pochta_klient; 
        $client['PK_Pol'] = $pk_pol; 
        $successMessage = 'Информация успешно обновлена!';
    } catch (PDOException $e) {
        $errorMessage = 'Ошибка обновления данных: ' . $e->getMessage();
    }
}

// Получение данных для выпадающих списков (пол)
$genders = [];
try {
    $stmt = $pdo->query("SELECT \"PK_Pol\", \"Nazv_Pol\" FROM \"Pol\"");
    $genders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = 'Ошибка получения данных для выпадающих списков: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный Кабинет Клиента</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
            background: linear-gradient(45deg, #49a09d, #5f2c82);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            overflow-x: auto;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            table-layout: fixed; 
        }

        th, td {
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            white-space: nowrap; 
            text-overflow: ellipsis; 
            overflow: hidden; 
        }

        th {
            text-align: left;
            background-color: #55608f;
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        td {
            word-wrap: break-word; 
            max-width: 200px; 
        }

        h1, h2 {
            color: #fff;
            font-size: 32px;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        h2 {
            font-size: 28px;
        }

        input[type="text"], input[type="tel"], input[type="email"], input[type="date"], select {
            padding: 10px;
            margin: 10px;
            width: 200px;
            border: 2px solid #ddd;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        button {
            padding: 10px 20px;
            margin: 5px;
            font-size: 16px;
            background: linear-gradient(45deg, #49a09d, #5f2c82);
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: linear-gradient(45deg, #5f2c82, #49a09d);
        }

        .history-toggle {
            background: linear-gradient(45deg, #5f2c82, #49a09d);
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .history-toggle:hover {
            background: linear-gradient(45deg, #49a09d, #5f2c82);
        }

        .hidden-history {
            display: none;
        }

        /* кнопо4ка */
        a {
            text-decoration: none;
            color: white;
            background: linear-gradient(45deg, #5f2c82, #49a09d);
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
            font-size: 16px;
            transition: background 0.3s;
        }

        a:hover {
            background: linear-gradient(45deg, #49a09d, #5f2c82);
        }
    </style>
</head>
<body>
    <h1>Личный Кабинет Клиента</h1>

    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?= htmlspecialchars($errorMessage) ?></p>
    <?php elseif (!empty($successMessage)): ?>
        <p style="color: green;"><?= htmlspecialchars($successMessage) ?></p>
    <?php endif; ?>
    <h2>Информация о клиенте</h2>
    <form method="post">
        <input type="text" name="famil_klient" placeholder="Фамилия" value="<?= htmlspecialchars($client['Famil_Klient']) ?>" required>
        <input type="text" name="imya_klient" placeholder="Имя" value="<?= htmlspecialchars($client['Imya_Klient']) ?>" required>
        <input type="text" name="otchestvo_klient" placeholder="Отчество" value="<?= htmlspecialchars($client['Otchestvo_Klient']) ?>" required>
        <input type="date" name="data_rozhd" placeholder="Дата рождения" value="<?= htmlspecialchars($client['Data_Rozhd']) ?>" required>
        <input type="tel" name="nomer_tel_klient" placeholder="Номер телефона" value="<?= htmlspecialchars($client['Nomer_Tel_Klient']) ?>" required>
        <input type="email" name="pochta_klient" placeholder="Email" value="<?= htmlspecialchars($client['Pochta_Klient']) ?>" required>

        <label for="pk_pol">Выберите пол:</label>
        <select name="pk_pol" required>
            <?php foreach ($genders as $gender): ?>
                <option value="<?= $gender['PK_Pol'] ?>" <?= $gender['PK_Pol'] == $client['PK_Pol'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($gender['Nazv_Pol']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" name="edit">Сохранить изменения</button>
    </form>

    <h2>История бронирований</h2>

    <button class="history-toggle" onclick="toggleHistory()">Показать/Скрыть историю</button>

    <div id="history" class="hidden-history">
        <?php if (!empty($purchaseHistory)): ?>
            <table>
                <tr>
                    <th>Название тура</th>
                    <th>Статус</th>
                    <th>ID Путевки</th>
                    <th>Действия</th>
                </tr>
                <?php foreach ($purchaseHistory as $purchase): ?>
                    <tr>
                        <td><?= htmlspecialchars($purchase['nazv_tura_klienta']) ?></td>
                        <td><?= htmlspecialchars($purchase['status']) ?></td>
                        <td><?= htmlspecialchars($purchase['pk_putevka']) ?></td>
                        <td>
                            <a href="booking_details.php?putevka_id=<?= htmlspecialchars($purchase['pk_putevka']) ?>">Подробная информация</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Нет истории бронирований.</p>
        <?php endif; ?>
    </div>

    <p><a href="client_logout.php">Выйти</a></p>
    <p><a href="booking.php">Забронировать тур</a></p>

    <script>
        function toggleHistory() {
            var history = document.getElementById('history');
            if (history.classList.contains('hidden-history')) {
                history.classList.remove('hidden-history');
            } else {
                history.classList.add('hidden-history');
            }
        }
    </script>
</body>
</html>
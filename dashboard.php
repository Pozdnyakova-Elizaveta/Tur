<?php
session_start();
require 'config.php'; // Подключение к базе данных

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Перенаправление на страницу входа, если пользователь не авторизован
    exit;
}

// Получение информации о сотруднике
$sotrudnik_id = $_SESSION['user_id'];
$sotrudnik = [];
try {
    $stmt = $pdo->prepare("
        SELECT S.*, K.\"Nazv_Kompaniya\", D.\"Nazv_Dolzhnost\" 
        FROM \"Sotrudnik\" S 
        JOIN \"Kompaniya\" K ON S.\"PK_Kompaniya\" = K.\"PK_Kompaniya\"
        JOIN \"Dolzhnost\" D ON S.\"PK_Dolzhnost\" = D.\"PK_Dolzhnost\"
        WHERE S.\"PK_Sotrudnik\" = ?
    ");
    $stmt->execute([$sotrudnik_id]);
    $sotrudnik = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения данных сотрудника: ' . $e->getMessage();
}

// Получение данных для выпадающих списков
$companies = [];
$positions = [];
try {
    $stmt = $pdo->query("SELECT \"PK_Kompaniya\", \"Nazv_Kompaniya\" FROM \"Kompaniya\"");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT \"PK_Dolzhnost\", \"Nazv_Dolzhnost\" FROM \"Dolzhnost\"");
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения данных: ' . $e->getMessage();
}

// Обработка редактирования информации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $familia = trim($_POST['familia']);
    $imya = trim($_POST['imya']);
    $otchestvo = trim($_POST['otchestvo']);
    $email = trim($_POST['email']);
    $pk_kompaniya = intval($_POST['pk_kompaniya']);
    $pk_dolzhnost = intval($_POST['pk_dolzhnost']);

    try {
        $stmt = $pdo->prepare("
            UPDATE \"Sotrudnik\" 
            SET \"Familia\" = ?, \"Imya\" = ?, \"Otchestvo\" = ?, \"Email\" = ?, \"PK_Kompaniya\" = ?, \"PK_Dolzhnost\" = ? 
            WHERE \"PK_Sotrudnik\" = ?
        ");
        $stmt->execute([$familia, $imya, $otchestvo, $email, $pk_kompaniya, $pk_dolzhnost, $sotrudnik_id]);
        $message = 'Информация успешно обновлена!';
    } catch (PDOException $e) {
        $message = 'Ошибка обновления данных: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный Кабинет Сотрудника</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(45deg, #49a09d, #5f2c82);
            font-family: sans-serif;
            font-weight: 100;
        }

        .container {
            width: 80%;
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }

        h1, h2 {
            color: #fff;
            text-align: center;
        }

        form {
            margin-bottom: 30px;
        }

        label {
            color: #fff;
            display: block;
            margin-top: 10px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
        }

        input[type="date"], select {
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        button {
            background-color: #55608f;
            color: #fff;
            cursor: pointer;
            border: none;
            margin-top: 20px;
        }

        button:hover {
            background-color: #444e72;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            text-align: left;
        }

        th {
            background-color: #55608f;
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        tbody td {
            position: relative;
        }

        tbody td:hover::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            top: -9999px;
            bottom: -9999px;
            background-color: rgba(255, 255, 255, 0.2);
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Личный Кабинет</h1>

        <?php if (!empty($message)): ?>
            <p style="color: red;"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <h2>Информация о сотруднике</h2>
        
        <form method="post">
            <input type="text" name="familia" placeholder="Фамилия" value="<?= htmlspecialchars($sotrudnik['Familia']) ?>" required>
            <input type="text" name="imya" placeholder="Имя" value="<?= htmlspecialchars($sotrudnik['Imya']) ?>" required>
            <input type="text" name="otchestvo" placeholder="Отчество" value="<?= htmlspecialchars($sotrudnik['Otchestvo']) ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($sotrudnik['Email']) ?>" required>

            <label for="pk_kompaniya">Выберите компанию:</label>
            <select name="pk_kompaniya" required>
                <?php foreach ($companies as $company): ?>
                    <option value="<?= $company['PK_Kompaniya'] ?>" <?= $company['PK_Kompaniya'] == $sotrudnik['PK_Kompaniya'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($company['Nazv_Kompaniya']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="pk_dolzhnost">Выберите должность:</label>
            <select name="pk_dolzhnost" required>
                <?php foreach ($positions as $position): ?>
                    <option value="<?= $position['PK_Dolzhnost'] ?>" <?= $position['PK_Dolzhnost'] == $sotrudnik['PK_Dolzhnost'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($position['Nazv_Dolzhnost']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="edit">Сохранить изменения</button>
        </form>

        <h2>Доступные действия</h2>
        <ul>
            <li><a href="populardestinations.php" style="color: #fff;">Анализ популярных направлений</a></li>
            <li><a href="tour_statistics.php" style="color: #fff;">Проданные туры</a></li>
            <li><a href="sotr_register.php" style="color: #fff;">Регистрация нового сотрудника</a></li>
        </ul>
        
        <p><a href="logout.php" style="color: #fff;">Выйти</a></p>
    </div>
</body>
</html>
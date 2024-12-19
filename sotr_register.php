<?php
require 'config.php';

$message = '';

// Получение данных компаний
$companies = [];
try {
    $stmt = $pdo->query("SELECT \"PK_Kompaniya\", \"Nazv_Kompaniya\" FROM \"Kompaniya\"");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения компаний: ' . $e->getMessage();
}

// Получение данных должностей
$positions = [];
try {
    $stmt = $pdo->query("SELECT \"PK_Dolzhnost\", \"Nazv_Dolzhnost\" FROM \"Dolzhnost\"");
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения должностей: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Регистрация
    if (isset($_POST['register'])) {
        $familia = trim($_POST['familia']);
        $imya = trim($_POST['imya']);
        $otchestvo = trim($_POST['otchestvo']);
        $email = trim($_POST['email']);
        $pk_kompaniya = intval($_POST['pk_kompaniya']);
        $pk_dolzhnost = intval($_POST['pk_dolzhnost']);
        
        if ($familia && $imya && $otchestvo && filter_var($email, FILTER_VALIDATE_EMAIL) && $pk_kompaniya && $pk_dolzhnost) {
            try {
                $stmt = $pdo->prepare("INSERT INTO \"Sotrudnik\" (\"Familia\", \"Imya\", \"Otchestvo\", \"Email\", \"PK_Kompaniya\", \"PK_Dolzhnost\") VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$familia, $imya, $otchestvo, $email, $pk_kompaniya, $pk_dolzhnost]);
                $message = 'Регистрация успешна!';
            } catch (PDOException $e) {
                $message = 'Ошибка регистрации: ' . $e->getMessage();
            }
        } else {
            $message = 'Пожалуйста, заполните все поля корректно.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация Сотрудника</title>
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
            max-width: 500px;
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
            gap: 15px;
        }

        input, select {
            width: calc(100% - 20px); 
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            outline: none;
        }

        input:focus, select:focus {
            border-color: #49a09d; 
            box-shadow: 0 0 8px rgba(73, 160, 157, 0.5);
        }

        label {
            font-weight: bold;
            color: #333;
            text-align: left;
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
    <a href="javascript:history.back()" class="back-button">Назад</a>
        <h1>Регистрация Сотрудника</h1>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="familia" placeholder="Фамилия" required>
            <input type="text" name="imya" placeholder="Имя" required>
            <input type="text" name="otchestvo" placeholder="Отчество" required>
            <input type="email" name="email" placeholder="Электронная почта" required>

            <label for="pk_kompaniya">Компания:</label>
            <select name="pk_kompaniya" id="pk_kompaniya" required>
                <option value="">-- Выберите компанию --</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?= htmlspecialchars($company['PK_Kompaniya']) ?>">
                        <?= htmlspecialchars($company['Nazv_Kompaniya']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="pk_dolzhnost">Должность:</label>
            <select name="pk_dolzhnost" id="pk_dolzhnost" required>
                <option value="">-- Выберите должность --</option>
                <?php foreach ($positions as $position): ?>
                    <option value="<?= htmlspecialchars($position['PK_Dolzhnost']) ?>">
                        <?= htmlspecialchars($position['Nazv_Dolzhnost']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="register">Зарегистрироваться</button>
        </form>
    </div>
</body>
</html>
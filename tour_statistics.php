<?php
require 'config.php';

// Переменные для хранения статистики тура
$tourStatistics = [];
$errorMessage = '';
$stmt = $pdo->prepare("SELECT \"PK_Tur\", \"Nazv_tur\" from \"Tur\"");
$stmt->execute();
$turs = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tour_id = intval($_POST['tour_id']); // Получение идентификатора тура

    try {
        // Получение информации по статистике тура
        $stmt = $pdo->prepare("SELECT * FROM tour_statistics(:tour_id)");
        $stmt->execute(['tour_id' => $tour_id]);
        $tourStatistics = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Проверка результата
        if (empty($tourStatistics)) {
            $errorMessage = 'Нет данных для указанного тура.';
        }

    } catch (PDOException $e) {
        $errorMessage = "Ошибка при выполнении запроса: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика тура</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(45deg, #49a09d, #5f2c82); 
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            color: #333;
        }

        input[type="number"], button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #49a09d;
            color: white;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #5f2c82;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        td {
            word-wrap: break-word;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 20px;
        }

        .no-data-message {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
    <a href="javascript:history.back()" class="back-button">Назад</a>
        <h1>Статистика тура</h1>

        <form method="post">
            <label for="tour_id">Тур:</label>
            <select name="tour_id" id="tour_id" required>
                <option value="">-- Выберите тур --</option>
                <?php foreach ($turs as $tour): ?>
                    <option value="<?= $tour['PK_Tur']; ?>">
                        <?= htmlspecialchars($tour['Nazv_tur']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Получить статистику тура</button>
        </form>

        <?php if (!empty($errorMessage)): ?>
            <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php elseif (!empty($tourStatistics)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Название тура</th>
                        <th>Проданные билеты</th>
                        <th>Средняя цена путевки</th>
                        <th>Прибыль от тура</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tourStatistics as $stat): ?>
                        <tr>
                            <td><?php echo isset($stat['nazv_tur']) ? htmlspecialchars($stat['nazv_tur']) : 'Нет данных'; ?></td>
                            <td><?php echo isset($stat['prodannye_bilety']) ? htmlspecialchars($stat['prodannye_bilety']) : 'Нет данных'; ?></td>
                            <td><?php echo isset($stat['srednyaya_cena_putevki']) ? htmlspecialchars(substr($stat['srednyaya_cena_putevki'], 0, -2)) : 'Нет данных'; ?> руб.</td>
                            <td><?php echo isset($stat['pribyl_tura']) ? htmlspecialchars(substr($stat['pribyl_tura'], 0, -2)) : 'Нет данных'; ?> руб.</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data-message">Нет данных для отображения.</p>
        <?php endif; ?>
    </div>

</body>
</html>
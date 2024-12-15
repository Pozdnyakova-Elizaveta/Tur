<?php
require 'config.php';

// Переменные для хранения статистики тура
$tourStatistics = [];
$errorMessage = '';

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
        // Обработка ошибок при выполнении запроса
        $errorMessage = "Ошибка при выполнении запроса: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика тура</title>
</head>
<body>
    <h1>Статистика тура</h1>

    <!-- Форма для ввода идентификатора тура -->
    <form method="post">
        <label for="tour_id">Идентификатор тура:</label>
        <input type="number" id="tour_id" name="tour_id" required>
        <button type="submit">Получить статистику тура</button>
    </form>

    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php elseif (!empty($tourStatistics)): ?>
        <table border="1">
            <tr>
                <th>Название тура</th>
                <th>Проданные билеты</th>
                <th>Средняя цена путевки</th>
                <th>Прибыль от тура</th>
            </tr>
            <?php foreach ($tourStatistics as $stat): ?>
                <tr>
                    <td><?php echo isset($stat['nazv_tur']) ? htmlspecialchars($stat['nazv_tur']) : 'Нет данных'; ?></td>
                    <td><?php echo isset($stat['prodannye_bilety']) ? htmlspecialchars($stat['prodannye_bilety']) : 'Нет данных'; ?></td>
                    <td><?php echo isset($stat['srednyaya_cena_putevki']) ? htmlspecialchars(substr($stat['srednyaya_cena_putevki'], 0, -2)) : 'Нет данных'; ?> руб.</td>
                    <td><?php echo isset($stat['pribyl_tura']) ? htmlspecialchars(substr($stat['pribyl_tura'], 0, -2)) : 'Нет данных'; ?> руб.</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Нет данных для отображения.</p>
    <?php endif; ?>
</body>
</html>
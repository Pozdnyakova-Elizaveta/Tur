<?php
require 'config.php'; 

// получаем данные 
$popularDestinations = [];
$stmt = $pdo->prepare("SELECT * FROM analyze_popular_destinations()");
$stmt->execute();
$popularDestinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Популярные направления</title>
</head>
<body>
    <h1>Популярные направления</h1>

    <h2>Статистика по популярным направлениям</h2>
    <?php if (!empty($popularDestinations)): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Направление</th>
                    <th>Продано билетов</th>
                    <th>Общая выручка</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($popularDestinations as $destination): ?>
                    <tr>
                        <td><?php echo htmlentities($destination['destination']); ?></td>
                        <td><?php echo htmlentities($destination['tickets_sold']); ?></td>
                        <td><?php echo htmlentities($destination['total_revenue']); ?> руб.</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Нет данных о популярных направлениях.</p>
    <?php endif; ?>
</body>
</html>
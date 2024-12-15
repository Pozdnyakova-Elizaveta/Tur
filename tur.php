<?php
require 'config.php';
function getTours($pdo, $filters) {
    if (!empty($filters)) {
        // Вызов функции фильтрации
        $stmt = $pdo->prepare("SELECT * FROM filtr_tur(:min_category_otel, :min_stoim, :max_stoim, :gorod, :start_data, :okonch_data, :colvo_turistov)");
        $stmt->bindParam(':min_category_otel', $filters['min_category_otel'], PDO::PARAM_INT);
        $stmt->bindParam(':min_stoim', $filters['min_stoim'], PDO::PARAM_STR);
        $stmt->bindParam(':max_stoim', $filters['max_stoim'], PDO::PARAM_STR);
        $stmt->bindParam(':gorod', $filters['gorod'], PDO::PARAM_STR);
        $stmt->bindParam(':start_data', $filters['start_data']);
        $stmt->bindParam(':okonch_data', $filters['okonch_data']);
        $stmt->bindParam(':colvo_turistov', $filters['colvo_turistov'], PDO::PARAM_INT);
    } else {
        // Получаем 10 туров, не позднее текущей даты
        $stmt = $pdo->prepare("SELECT * FROM \"Tur\" WHERE \"Date_nach\" >= CURRENT_DATE LIMIT 10");
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получаем фильтры из POST-запроса
$filters = [
    'min_category_otel' => isset($_POST['min_category_otel']) ? intval($_POST['min_category_otel']) : null,
    'min_stoim' => isset($_POST['min_stoim']) ? $_POST['min_stoim'] : null,
    'max_stoim' => isset($_POST['max_stoim']) ? $_POST['max_stoim'] : null,
    'gorod' => isset($_POST['gorod']) ? $_POST['gorod'] : null,
    'start_data' => isset($_POST['start_data']) && trim($_POST['start_data']) !== '' ? $_POST['start_data'] : null,
    'okonch_data' => isset($_POST['okonch_data'])&& trim($_POST['okonch_data']) !== '' ? $_POST['okonch_data'] : null,
    'colvo_turistov' => isset($_POST['colvo_turistov']) ? intval($_POST['colvo_turistov']) : null,
];

// Получаем туры
$tours = getTours($pdo, $filters);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Туры</title>
    <style>
        body { font-family: Arial, sans-serif; }
        form { margin-bottom: 20px; }
        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="number"], input[type="date"] { width: 100%; }
        input[type="submit"] { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Туры</h1>

    <form method="POST" action="">
        <label for="min_category_otel">Минимальная категория отеля:</label>
        <input type="number" name="min_category_otel" id="min_category_otel">

        <label for="min_stoim">Минимальная стоимость:</label>
        <input type="text" name="min_stoim" id="min_stoim">

        <label for="max_stoim">Максимальная стоимость:</label>
        <input type="text" name="max_stoim" id="max_stoim">

        <label for="gorod">Город:</label>
        <input type="text" name="gorod" id="gorod">

        <label for="start_data">Дата начала:</label>
        <input type="date" name="start_data" id="start_data">

        <label for="okonch_data">Дата окончания:</label>
        <input type="date" name="okonch_data" id="okonch_data">

        <label for="colvo_turistov">Количество туристов:</label>
        <input type="number" name="colvo_turistov" id="colvo_turistov">

        <input type="submit" value="Фильтровать">
    </form>

    <h2>Список туров</h2>
    <ul>
        <?php if ($tours): ?>
            <?php foreach ($tours as $tour): ?>
                <li>
                    <strong><?php echo htmlspecialchars($tour['tur_nazv']); ?></strong><br>
                    Дата начала: <?php echo htmlspecialchars($tour['data_start']); ?><br>
                    Дата окончания: <?php echo htmlspecialchars($tour['data_okonch']); ?><br>
                    Описание: <?php echo htmlspecialchars($tour['opisanie']); ?><br>
                    Города: <?php echo htmlspecialchars(substr($tour['goroda'], 1, -1),); ?><br>
                    Минимальная стоимость: <?php echo htmlspecialchars(substr($tour['min_stoim_tur'], 0, -2)); ?> руб.<br>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Туры не найдены.</li>
        <?php endif; ?>
    </ul>
</body>
</html>
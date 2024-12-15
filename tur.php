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
    'start_data' => isset($_POST['start_data']) ? $_POST['start_data'] : null,
    'okonch_data' => isset($_POST['okonch_data']) ? $_POST['okonch_data'] : null,
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

        h1, h2 {
            text-align: center;
            color: #333;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            margin-top: 10px;
            background-color: #49a09d;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #5f2c82;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            table-layout: fixed;
            overflow-x: auto;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            word-wrap: break-word;
            font-size: 14px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        td:nth-child(1) {
            width: 40%;
        }

        td:nth-child(2), td:nth-child(3) {
            width: 30%;
        }

        td:nth-child(2), td:nth-child(3) {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
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
        <table>
            <thead>
                <tr>
                    <th>Название тура</th>
                    <th>Дата начала</th>
                    <th>Дата окончания</th>
                    <th>Описание</th>
                    <th>Города</th>
                    <th>Минимальная стоимость</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($tours): ?>
                    <?php foreach ($tours as $tour): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tour['tur_nazv']); ?></td>
                            <td><?php echo htmlspecialchars($tour['data_start']); ?></td>
                            <td><?php echo htmlspecialchars($tour['data_okonch']); ?></td>
                            <td><?php echo htmlspecialchars($tour['opisanie']); ?></td>
                            <td><?php echo htmlspecialchars(substr($tour['goroda'], 1, -1)); ?></td>
                            <td><?php echo htmlspecialchars(substr($tour['min_stoim_tur'], 0, -2)); ?> руб.</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Туры не найдены.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
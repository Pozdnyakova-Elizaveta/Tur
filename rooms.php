<?php
require 'config.php'; 

$availableRooms = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otelId = $_POST['OtelID'];
    $checkIn = $_POST['CheckIn'];
    $checkOut = $_POST['CheckOut'];

    $stmt = $pdo->prepare("SELECT * FROM get_available_rooms(:otelId, :checkIn, :checkOut)");
    $stmt->bindParam(':otelId', $otelId, PDO::PARAM_INT);
    $stmt->bindParam(':checkIn', $checkIn, PDO::PARAM_STR);
    $stmt->bindParam(':checkOut', $checkOut, PDO::PARAM_STR);
    $stmt->execute();
    $availableRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query("SELECT \"PK_Otel\", \"Nazv_Otel\" FROM \"Otel\"");
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Доступные номера</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: sans-serif;
        }

        body {
            background: linear-gradient(45deg, #49a09d, #5f2c82);
            font-weight: 100;
        }

        .container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        table {
            width: 800px;
            border-collapse: collapse;
            margin-top: 20px;
            overflow: hidden;
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

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        label, select, input {
            margin-bottom: 15px;
            font-size: 16px;
            color: #fff;
        }

        select, input[type="date"] {
            width: 250px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select:focus, input[type="date"]:focus {
            background-color: rgba(255, 255, 255, 0.5);
        }

        option {
            background-color: #fff;
            color: #333;
        }

        option:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }

        button {
            padding: 10px 20px;
            background-color: #5f2c82;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #4c1a5b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Доступные номера</h1>

        <h2>Выберите отель и даты</h2>
        <form method="POST">
            <label for="OtelID">Отель:</label>
            <select name="OtelID" id="OtelID" required>
                <option value="">Выберите отель</option>
                <?php foreach ($hotels as $hotel): ?>
                    <option value="<?php echo htmlspecialchars($hotel['PK_Otel']); ?>">
                        <?php echo htmlspecialchars($hotel['Nazv_Otel']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="CheckIn">Дата заезда:</label>
            <input type="date" name="CheckIn" id="CheckIn" required>

            <label for="CheckOut">Дата выезда:</label>
            <input type="date" name="CheckOut" id="CheckOut" required>

            <button type="submit">Показать доступные номера</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <h2>Результаты поиска</h2>
            <?php if (!empty($availableRooms)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Номер комнаты</th>
                            <th>Цена</th>
                            <th>Описание</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($availableRooms as $room): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($room['Nomer_Komnat']); ?></td>
                                <td><?php echo htmlspecialchars(substr($room['Tsena'], 0, -2)); ?> руб.</td>
                                <td><?php echo htmlspecialchars($room['Opisanie_Tip_Nomer']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Нет доступных номеров на выбранные даты.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
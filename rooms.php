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

// для выпадающего списка извлечение
$stmt = $pdo->query("SELECT \"PK_Otel\", \"Nazv_Otel\" FROM \"Otel\"");
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Доступные номера</title>
</head>
<body>
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
            <table border="1">
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
        <td><?php 
            $cleanPrice = preg_replace('/[^\d]/', '', $room['Tsena']);
            echo number_format($cleanPrice, 0, '.', ' ') . ' Р';
            ?>
        </td>
        <td><?php echo htmlspecialchars($room['Opisanie_Tip_Nomer']); ?></td>
    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Нет доступных номеров на выбранные даты.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
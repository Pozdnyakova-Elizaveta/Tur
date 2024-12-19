<?php
require 'config.php'; 
$pk_tur = $_GET['pk_tur'];
$pk_putevka = $_GET['pk_putevka'];
$availableRooms = [];
$pointsOfTour = [];
$checkIn = null;
$checkOut = null;
$stmt = $pdo->prepare("SELECT * FROM \"Tochka_Tur\" WHERE \"PK_Tur\" = :pk_tur");
$stmt->bindParam(':pk_tur', $pk_tur);
$stmt->execute();
$pointsOfTour = $stmt->fetchAll(PDO::FETCH_ASSOC);
$message = "";

foreach ($pointsOfTour as $point) {
    $stmt = $pdo->prepare("SELECT r.\"Data_Vrem_Pribit\" FROM \"Reis\" r JOIN \"Tochka_Tur\" tt
    ON r.\"PK_Reis\"= tt.\"PK_Reis\" WHERE tt.\"PK_Tochka_Tur\" = :pk_tochka");
    $stmt->bindParam(':pk_tochka', $point['PK_Tochka_Tur']);
    $stmt->execute();
    $checkInData = $stmt->fetch(PDO::FETCH_ASSOC);
    $checkIn = $checkInData['Data_Vrem_Pribit'];
    $stmt = $pdo->prepare("SELECT min(r.\"Data_Vrem_Otpr\") as min_otpr FROM \"Reis\" r 
    JOIN \"Tochka_Tur\" tt ON r.\"PK_Reis\"= tt.\"PK_Reis\" JOIN \"Transport_usel\" tu ON
                    	r.\"PK_Transport_Usel_Otpr\" = tu.\"PK_Transport_Usel\"
                    	WHERE tt.\"PK_Tur\" = :tur_id AND r.\"Data_Vrem_Otpr\" > :data_zaezd AND tu.\"PK_Gorod\" =
                    	(SELECT tt.\"PK_Gorod\" FROM \"Tochka_Tur\" tt WHERE tt.\"PK_Tochka_Tur\" = :pk_tochka)");
                        
    $stmt->bindParam(':tur_id', $pk_tur);
    $stmt->bindParam(':data_zaezd', $checkIn);
    $stmt->bindParam(':pk_tochka', $point['PK_Tochka_Tur']);
    $stmt->execute();
    $checkOutData = $stmt->fetch(PDO::FETCH_ASSOC);
    $checkOut = $checkOutData['min_otpr'];
    if ($checkIn && $checkOut) {
        $stmt = $pdo->prepare("SELECT * FROM get_available_rooms(:otelId, :checkIn, :checkOut)");
        $stmt->bindParam(':otelId', $point['PK_Otel'],PDO::PARAM_INT);
        $stmt->bindParam(':checkIn', $checkIn, PDO::PARAM_STR);
        $stmt->bindParam(':checkOut', $checkOut, PDO::PARAM_STR);
        $stmt->execute();
        $availableRooms[$point['PK_Tochka_Tur']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try{
    $nomerSelections = $_POST['rooms']; 
    foreach ($pointsOfTour as $point) {
        foreach ($nomerSelections as $nomer) {
            $data = json_decode($nomer, true);

            // Получение данных о времени прибытия
            $stmt = $pdo->prepare("SELECT r.\"Data_Vrem_Pribit\" FROM \"Reis\" r JOIN \"Tochka_Tur\" tt
                ON r.\"PK_Reis\"= tt.\"PK_Reis\" WHERE tt.\"PK_Tochka_Tur\" = :pk_tochka");
            $stmt->bindParam(':pk_tochka', $point['PK_Tochka_Tur']);
            $stmt->execute();
            $checkInData = $stmt->fetch(PDO::FETCH_ASSOC);
            $checkIn = $checkInData['Data_Vrem_Pribit'];

            // Получение данных о времени отправления
            $stmt = $pdo->prepare("SELECT min(r.\"Data_Vrem_Otpr\") as min_otpr FROM \"Reis\" r 
                JOIN \"Tochka_Tur\" tt ON r.\"PK_Reis\"= tt.\"PK_Reis\" JOIN \"Transport_usel\" tu ON
                r.\"PK_Transport_Usel_Otpr\" = tu.\"PK_Transport_Usel\"
                WHERE tt.\"PK_Tur\" = :tur_id AND r.\"Data_Vrem_Otpr\" > :data_zaezd AND tu.\"PK_Gorod\" =
                (SELECT tt.\"PK_Gorod\" FROM \"Tochka_Tur\" tt WHERE tt.\"PK_Tochka_Tur\" = :pk_tochka)");
            $stmt->bindParam(':tur_id', $pk_tur);
            $stmt->bindParam(':data_zaezd', $checkIn);
            $stmt->bindParam(':pk_tochka', $point['PK_Tochka_Tur']);
            $stmt->execute();
            $checkOutData = $stmt->fetch(PDO::FETCH_ASSOC);
            $checkOut = $checkOutData['min_otpr'];

            // Получение данных об отеле
            $stmt = $pdo->prepare("SELECT o.\"PK_Otel\" FROM \"Otel\" o JOIN \"Nomer\" n
                ON o.\"PK_Otel\"= n.\"PK_Otel\" WHERE n.\"Nomer_Komnat\" = :nomer");
            $stmt->bindParam(':nomer', $data['nomer_komnat']);
            $stmt->execute();
            $otel = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$otel) {
                // Обработка случая, когда отель не найден
                echo "Ошибка: Отель не найден для номера: " . $data['nomer_komnat'];
                continue;
            }
            $stmt = $pdo->prepare("SELECT \"PK_Nomer\" FROM \"Nomer\" WHERE \"Nomer_Komnat\" = :nomer");
            $stmt->bindParam(':nomer', $data['nomer_komnat']);
            $stmt->execute();
            $nomer_k = $stmt->fetch(PDO::FETCH_ASSOC);
            // Вставка данных о бронировании
            if ($checkOut){ $stmt = $pdo->prepare("INSERT INTO \"Bron_Nomer\" (\"PK_Nomer\", \"PK_Otel\", \"Date_Zaezd\", \"Date_Viezda\")
                VALUES (:nomer, :otel, :zaezd, :viezd) RETURNING \"PK_Bron\"");
            $stmt->bindParam(':nomer', $nomer_k['PK_Nomer']);
            $stmt->bindParam(':otel', $otel['PK_Otel']);
            $stmt->bindParam(':zaezd', $checkIn);
            $stmt->bindParam(':viezd', $checkOut);
            $stmt->execute();
            $key = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$key) {
                // Обработка случая, когда бронирование не создано
                echo "Ошибка: Бронирование не создано для номера: " . $data['nomer_komnat'];
                continue;
            }

            // Вставка данных о путевке
            $stmt = $pdo->prepare("INSERT INTO \"Putevka_Bron_Nomer\" (\"PK_Putevka\", \"PK_Bron\", \"PK_Nomer\", \"PK_Otel\") 
                VALUES (:putevka, :bron, :nomer, :otel)");
            $stmt->bindParam(':nomer', $nomer_k['PK_Nomer']);
            $stmt->bindParam(':otel', $otel['PK_Otel']);
            $stmt->bindParam(':bron', $key['PK_Bron']);
            $stmt->bindParam(':putevka', $pk_putevka);
            $stmt->execute();
        }
        }
    }

    // Вставка данных о резервировании
    $stmt = $pdo->prepare("INSERT INTO \"Rezerv\" (\"Status_Rezerv\", \"PK_Meropriyat\", \"PK_Putevka\")
        SELECT 'Забронировано', \"PK_Meropriyat\", :pk_putevka
        FROM \"Meropriyat\"
        WHERE \"PK_Tur\" = :pk_tur");
    $stmt->bindParam(':pk_tur', $pk_tur);
    $stmt->bindParam(':pk_putevka', $pk_putevka);
    $stmt->execute();

    header("Location: client_dashboard.php");
    exit();
} catch (PDOException $e) {
    $message = 'Ошибка бронирования номера';
}
}
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование Номеров Отелей</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: sans-serif;
        }
        .message {
            color: darkred;
            text-align: center;
            margin-bottom: 15px;
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

        option {
            background-color: #fff;
            color: #333;
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
    <a href="javascript:history.back()" class="back-button">Назад</a>
        <h1>Бронирование Номеров Отелей</h1>
        <?php if ($message): ?>
            <p class="message"> <?= htmlspecialchars($message) ?> </p>
    <?php endif; ?>
        <form action="selection_hotel.php?pk_putevka=<?= $pk_putevka ?>&pk_tur=<?= $pk_tur ?>" method="post">
        <?php foreach ($pointsOfTour as $point): ?>
        <?php if (isset($availableRooms[$point['PK_Tochka_Tur']])): ?>
            <div class="form-group">
            <?php if (!empty($availableRooms[$point['PK_Tochka_Tur']])):?>
                <label for="room_<?= $point['PK_Tochka_Tur'] ?>">Номер в <?= htmlspecialchars($point['Nazvanie_Tochka_Tur']) ?>:</label>
                <select name="rooms[<?= $point['PK_Tochka_Tur'] ?>]" id="room_<?= $point['PK_Tochka_Tur'] ?>" required>
                    <option value="">-- Выберите номер --</option>
                    <?php foreach ($availableRooms[$point['PK_Tochka_Tur']] as $room): ?>
                        <option value='{"nomer_komnat": "<?= htmlspecialchars($room['Nomer_Komnat']) ?>", "tsena": "<?= htmlspecialchars($room['Tsena']) ?>", "opisanie": "<?= htmlspecialchars($room['Opisanie_Tip_Nomer']) ?>" }'>
                            Номер: <?= htmlspecialchars($room['Nomer_Komnat']) ?>, Стоимость: <?= htmlspecialchars(substr($room['Tsena'], 0, -2)) ?> руб., Описание: <?= htmlspecialchars($room['Opisanie_Tip_Nomer']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

            <button type="submit">Забронировать номер</button>
        </form>
        </div>
</body>
</html>
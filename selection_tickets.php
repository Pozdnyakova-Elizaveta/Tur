<?php
require 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Обработка формы бронирования билетов
    $ticketSelections = $_POST['tickets']; // Получаем выбранные билеты

    $pk_putevka = $_GET['pk_putevka'];
    
    $stmt = $pdo->prepare("INSERT INTO \"Bron_Bilet\" (\"Date_Bron\", \"PK_Status_Bilet\", \"PK_Putevka\") VALUES (CURRENT_DATE, 1, :pk_putevka)");
    $stmt->bindParam(':pk_putevka', $pk_putevka);
    $stmt->execute();

    foreach ($ticketSelections as $ticket) {
        $data = json_decode($ticket, true);
        $stmt = $pdo->prepare("UPDATE \"Bilet\" SET \"PK_Bron_Bilet\" = (SELECT \"PK_Bron_Bilet\" FROM \"Bron_Bilet\" 
        WHERE \"PK_Putevka\" = :pk_putevka LIMIT 1) WHERE \"PK_Bilet\" = :pk_bilet");
        // Предполагаем, что вы получаете необходимые данные из $ticket
        $stmt->bindParam(':pk_bilet', $data['pk_bilet']);
        $stmt->bindParam(':pk_putevka', $pk_putevka);
        $stmt->execute();
    }
    $pk_tur = $_GET['pk_tur'];

    header("Location: selection_hotel.php?pk_tur=$pk_tur&pk_putevka=$pk_putevka");
    exit();
}

// Получение id путевки и тура
$pk_putevka = $_GET['pk_putevka'];
$pk_tur = $_GET['pk_tur'];

// Получение точек тура
$stmt = $pdo->prepare("SELECT * FROM \"Tochka_Tur\" WHERE \"PK_Tur\" = :pk_tur");
$stmt->bindParam(':pk_tur', $pk_tur);
$stmt->execute();
$points = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение рейсов для каждой точки
$flights = [];
foreach ($points as $point) {
    $stmt = $pdo->prepare("SELECT * FROM get_available_tickets(:pk_reis)");
    $stmt->bindParam(':pk_reis', $point['PK_Reis']);
    $stmt->execute();
    $flights[$point['PK_Tochka_Tur']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование билетов</title>
    <style>
        /* Стили остаются прежними */
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(45deg, #49a09d, #5f2c82);
            font-family: sans-serif;
            font-weight: 100;
        }

        h1 {
            color: #fff;
            text-align: center;
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

        form {
            margin-bottom: 20px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            color: #000;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        option {
            background-color: #f5f5f5;
            color: #000;
        }

        option:hover {
            background-color: #ddd;
        }

        button {
            background-color: #55608f;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        button:hover {
            background-color: #444e72;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
        }

        .links {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }

        .form-group select {
            display: inline-block;
            width: 80%;
        }

        .form-group button {
            display: inline-block;
            width: 18%;
            margin-left: 2%;
        }

        .actions {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Бронирование билетов для тура</h1>
        <form action="selection_tickets.php?pk_putevka=<?= $pk_putevka ?>&pk_tur=<?= $pk_tur ?>&client_id=<?= $client_id ?>" method="post">
            <?php foreach ($points as $point): ?>
                <?php if (isset($flights[$point['PK_Tochka_Tur']])): ?>
                    <div class="form-group">
                        <label for="flight_<?= $point['PK_Tochka_Tur'] ?>">Рейс в <?= htmlspecialchars($point['Nazvanie_Tochka_Tur']) ?>:</label>
                        <select name="tickets[<?= $point['PK_Tochka_Tur'] ?>]" id="flight_<?= $point['PK_Tochka_Tur'] ?>">
                            <option value="">-- Выберите рейс --</option>
                            <?php foreach ($flights[$point['PK_Tochka_Tur']] as $flight): ?>
                                <option value='{"pk_bilet": <?= $flight['PK_Bilet'] ?>, "nomer_mest": "<?= htmlspecialchars($flight['Nomer_Mest']) ?>", "stoim_bilet": "<?= htmlspecialchars($flight['Stoim_Bilet']) ?>", "kategoria": "<?= htmlspecialchars($flight['Kategoria']) ?>" }'>
                                    Номер места: <?= htmlspecialchars($flight['Nomer_Mest']) ?>, Стоимость: <?= htmlspecialchars($flight['Stoim_Bilet']) ?> руб., Категория: <?= htmlspecialchars($flight['Kategoria']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <button type="submit">Забронировать билеты</button>
        </form>
    </div>
</body>
</html>
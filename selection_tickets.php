<?php
require 'config.php'; 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Обработка формы бронирования билетов
    $ticketSelections = $_POST['tickets']; // Получаем выбранные билеты
    $stmt = $pdo->prepare("INSERT INTO \"Bron_Bilet\" (\"Date_Bron\", \"PK_Status_Bilet\", \"PK_Putevka\") VALUES (CURRENT_DATE, :1, :pk_putevka)");
    $stmt->bindParam(':pk_putevka', $pk_putevka);
    foreach ($ticketSelections as $ticket) {
        $stmt = $pdo->prepare("UPDATE \"Bilet\" SET \"PK_Bron_Bilet\" = (SELECT \"PK_Bron_Bilet\" from \"Bron_Bilet\" 
        WHERE \"PK_Putevka\"=:pk_putevka LIMIT 1) WHERE \"PK_Bilet\" = :pk_bilet");
                // Предполагаем, что вы получаете необходимые данные из $ticket
        $stmt->bindParam(':pk_bilet', $ticket['pk_bilet']);
        $stmt->bindParam(':pk_putevka', $pk_putevka);

        $stmt->execute();
    }

    header('Location: selection_hotel.php?pk_tur=$pk_tur&pk_putevka=$pk_putevka');
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
</head>
<body>
    <h1>Бронирование билетов для тура</h1>
    <form action="booking.php?pk_putevka=<?= $pk_putevka ?>&pk_tur=<?= $pk_tur ?>" method="post">
    <?php foreach ($points as $point): ?>
    <?php if (isset($flights[$point['PK_Tochka_Tur']])): ?>
        <label for="flight_<?= $point['PK_Tochka_Tur'] ?>">Рейс в <?= htmlspecialchars($point['Nazvanie_Tochka_Tur']) ?>:</label>
        <select name="tickets[<?= $point['PK_Tochka_Tur'] ?>]">
            <option value="">-- Выберите рейс --</option>
            <?php foreach ($flights[$point['PK_Tochka_Tur']] as $flight): ?>
                <option value='{"pk_bilet": <?= $flight['PK_Bilet'] ?>, "nomer_mest": "<?= htmlspecialchars($flight['Nomer_Mest']) ?>", "stoim_bilet": "<?= htmlspecialchars($flight['Stoim_Bilet']) ?>", "kategoria": "<?= htmlspecialchars($flight['Kategoria']) ?>" }'>
                    Номер места: <?= htmlspecialchars($flight['Nomer_Mest']) ?>, Стоимость: <?= htmlspecialchars($flight['Stoim_Bilet']) ?> руб., Категория: <?= htmlspecialchars($flight['Kategoria']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
<?php endforeach; ?>
    <br>
    <button type="submit">Забронировать билеты</button>
</form>
</body>
</html>
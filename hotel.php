<?php
require 'config.php';

$addresses = []; // Массив для хранения адресов
if (isset($_GET['PK_Gorod'])) {
    $PK_Gorod = intval($_GET['PK_Gorod']);

    // Получение названия города
    $stmt_city = $pdo->prepare("SELECT \"Nazv_Gorod\" FROM \"Gorod\" WHERE \"PK_Gorod\" = :pk_gorod");
    $stmt_city->execute(['pk_gorod' => $PK_Gorod]);
    $city = $stmt_city->fetch(PDO::FETCH_ASSOC);
    
    if ($city) {
        $city_name = $city['Nazv_Gorod'];
    }

    $stmt_addresses = $pdo->prepare("
        SELECT \"Adres\".\"PK_Adres\", \"Ulica\".\"Nazv_Ulica\", \"Adres\".\"Nomer_Dom\", \"Adres\".\"Korpus\"
        FROM \"Adres\"
        JOIN \"Ulica\" ON \"Adres\".\"PK_Ulica\" = \"Ulica\".\"PK_Ulica\" 
        WHERE \"Adres\".\"PK_Gorod\" = :pk_gorod
    ");
    $stmt_addresses->execute(['pk_gorod' => $PK_Gorod]);
    $addresses = $stmt_addresses->fetchAll(PDO::FETCH_ASSOC);

    // Получение списка отелей для данного города
    $stmt = $pdo->prepare("SELECT \"Otel\".\"PK_Otel\", \"Otel\".\"Nazv_Otel\", \"Otel\".\"Klass\", \"Otel\".\"Nomer_Tel_Otel\", \"Otel\".\"Sait_Otel\", \"Otel\".\"Opisanie_Otel\",
    CONCAT(\"Ulica\".\"Nazv_Ulica\", ', ', \"Adres\".\"Nomer_Dom\", 
           CASE 
               WHEN \"Adres\".\"Korpus\" IS NOT NULL THEN CONCAT(' ', \"Adres\".\"Korpus\") 
               ELSE '' 
           END) AS \"Full_Address\"
           FROM  \"Otel\" JOIN \"Adres\" ON \"Otel\".\"PK_Adres\" = \"Adres\".\"PK_Adres\" JOIN \"Ulica\" ON \"Adres\".\"PK_Ulica\" = \"Ulica\".\"PK_Ulica\" WHERE 
           \"Adres\".\"PK_Gorod\" = :pk_gorod;");
    $stmt->execute(['pk_gorod' => $PK_Gorod]);
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_hotel'])) {
    $nazv_otela = $_POST['Nazv_Otel'];
    $klass = intval($_POST['Klass']);
    $nomer_tel_otela = $_POST['Nomer_Tel_Otel'];
    $sait_otela = $_POST['Sait_Otel'];
    $opisanie_otela = $_POST['Opisanie_Otel'];
    $pk_adres = $_POST['PK_Adres'];

    $stmt = $pdo->prepare("INSERT INTO \"Otel\" (\"Nazv_Otel\", \"Klass\", \"Nomer_Tel_Otel\", \"Sait_Otel\", \"Opisanie_Otel\", \"PK_Adres\") VALUES (:nazv_otela, :klass, :nomer_tel_otela, :sait_otela, :opisanie_otela, :pk_adres)");
    $stmt->execute([
        'nazv_otela' => $nazv_otela,
        'klass' => $klass,
        'nomer_tel_otela' => $nomer_tel_otela,
        'sait_otela' => $sait_otela,
        'opisanie_otela' => $opisanie_otela,
        'pk_adres' => $pk_adres
    ]);

    header("Location: hotels.php?PK_Gorod=$PK_Gorod");
    exit();
}

// Обработка обновления отеля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hotel'])) {
    $pk_otela = intval($_POST['PK_Otel']);
    $nazv_otela = $_POST['Nazv_Otel'];
    $klass = intval($_POST['Klass']);
    $nomer_tel_otela = $_POST['Nomer_Tel_Otel'];
    $sait_otela = $_POST['Sait_Otel'];
    $opisanie_otela = $_POST['Opisanie_Otel'];
    $pk_adres = intval($_POST['PK_Adres']);

    $stmt = $pdo->prepare("UPDATE \"Otel\" SET \"Nazv_Otel\" = :nazv_otela, \"Klass\" = :klass, \"Nomer_Tel_Otel\" = :nomer_tel_otela, \"Sait_Otel\" = :sait_otela, \"Opisanie_Otel\" = :opisanie_otela, \"PK_Adres\" =:pk_adres WHERE \"PK_Otel\" = :pk_otela");
    $stmt->execute([
        'nazv_otela' => $nazv_otela,
        'klass' => $klass,
        'nomer_tel_otela' => $nomer_tel_otela,
        'sait_otela' => $sait_otela,
        'opisanie_otela' => $opisanie_otela,
        'pk_otela' => $pk_otela,
        'pk_adres' => $pk_adres
    ]);

    header("Location: hotels.php?PK_Gorod=$PK_Gorod");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_hotel'])) {
    $pk_otela = intval($_POST['PK_Otel']);

    $stmt = $pdo->prepare("DELETE FROM \"Otel\" WHERE \"PK_Otel\" = :pk_otela");
    $stmt->execute(['pk_otela' => $pk_otela]);

    header("Location: hotels.php?PK_Gorod=$PK_Gorod");
    exit();
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отели для города</title>
</head>
<body>
<h1>Отели для города: <?php echo htmlspecialchars($city_name); ?></h1>
    <form method="POST">
        <input type="text" name="Nazv_Otel" required placeholder="Название отеля">
        <input type="number" name="Klass" min="0" max="5" required placeholder="Класс отеля">
        <input type="text" name="Nomer_Tel_Otel" required placeholder="Номер телефона">
        <input type="text" name="Sait_Otel" required placeholder="Сайт отеля">
        <textarea name="Opisanie_Otel" placeholder="Описание отеля"></textarea>
        <label for="PK_Adres">Выберите адрес:</label>
        <select name="PK_Adres" required>
            <option value="">Выберите адрес</option>
            <?php foreach ($addresses as $address): ?>
                <option value="<?php echo htmlspecialchars($address['PK_Adres']); ?>">
                    <?php echo htmlspecialchars($address['Nazv_Ulica'] . ', ' . $address['Nomer_Dom'] . ($address['Korpus'] ? ' ' . htmlspecialchars($address['Korpus']) : '')); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="add_hotel">Добавить отель</button>
    </form>
    <h2>Список отелей</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Название отеля</th>
            <th>Класс</th>
            <th>Номер телефона</th>
            <th>Сайт</th>
            <th>Описание</th>
            <th>Адрес</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($hotels as $hotel): ?>
            <tr>
                <td><?php echo htmlspecialchars($hotel['PK_Otel']); ?></td>
                <td><?php echo htmlspecialchars($hotel['Nazv_Otel']); ?></td>
                <td><?php echo htmlspecialchars($hotel['Klass']); ?></td>
                <td><?php echo htmlspecialchars($hotel['Nomer_Tel_Otel']); ?></td>
                <td><?php echo htmlspecialchars($hotel['Sait_Otel']); ?></td>
                <td><?php echo htmlspecialchars($hotel['Opisanie_Otel']); ?></td>
                <td><?php echo htmlspecialchars($hotel['Full_Address']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="PK_Otel" value="<?php echo htmlspecialchars($hotel['PK_Otel']); ?>">
                        <input type="text" name="Nazv_Otel" required value="<?php echo htmlspecialchars($hotel['Nazv_Otel']); ?>">
                        <input type="number" name="Klass" min="0" max="5" required value="<?php echo htmlspecialchars($hotel['Klass']); ?>">
                        <input type="text" name="Nomer_Tel_Otel" required value="<?php echo htmlspecialchars($hotel['Nomer_Tel_Otel']); ?>">
                        <input type="text" name="Sait_Otel" required value="<?php echo htmlspecialchars($hotel['Sait_Otel']); ?>">
                        <textarea name="Opisanie_Otel"><?php echo htmlspecialchars($hotel['Opisanie_Otel']); ?></textarea>
                        <select name="PK_Adres" required>
                            <option value="">Выберите адрес</option>
                            <?php foreach ($addresses as $address): ?>
                                <option value="<?php echo htmlspecialchars($address['PK_Adres']); ?>">
                                <?php echo htmlspecialchars($address['Nazv_Ulica'] . ', ' . $address['Nomer_Dom'] . ($address['Korpus'] ? ' ' . htmlspecialchars($address['Korpus']) : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="update_hotel">Обновить</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="PK_Otel" value="<?php echo htmlspecialchars($hotel['PK_Otel']); ?>">
                        <button type="submit" name="delete_hotel" onclick="return confirm('Вы уверены, что хотите удалить этот отель?');">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
<?php
require 'config.php';  


$bookingStatus = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $familia = $_POST['Familia'];
    $imya = $_POST['Imya'];
    $otchestvo = $_POST['Otchestvo'];
    $nomer_pasport = $_POST['NomerPasport'];
    $seria_pasport = $_POST['SeriaPasport'];
    $data_rozhd = $_POST['DataRozhd'];
    $pol_id = $_POST['PolID'];
    $tur_id = $_POST['TurID'];
    $status_id = $_POST['StatusID'];
    $klient_id = $_POST['KlientID'];

    $stmt = $pdo->prepare("SELECT book_tur(:familia, :imya, :otchestvo, :nomer_pasport, :seria_pasport, :data_rozhd, :pol_id, :tur_id, :status_id, :klient_id)");

    $stmt->bindParam(':familia', $familia, PDO::PARAM_STR);
    $stmt->bindParam(':imya', $imya, PDO::PARAM_STR);
    $stmt->bindParam(':otchestvo', $otchestvo, PDO::PARAM_STR);
    $stmt->bindParam(':nomer_pasport', $nomer_pasport, PDO::PARAM_STR);
    $stmt->bindParam(':seria_pasport', $seria_pasport, PDO::PARAM_STR);
    $stmt->bindParam(':data_rozhd', $data_rozhd, PDO::PARAM_STR);
    $stmt->bindParam(':pol_id', $pol_id, PDO::PARAM_INT);
    $stmt->bindParam(':tur_id', $tur_id, PDO::PARAM_INT);
    $stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
    $stmt->bindParam(':klient_id', $klient_id, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $bookingStatus = 'Заявка успешно оформлена!';
    } catch (PDOException $e) {
        $bookingStatus = 'Ошибка: ' . $e->getMessage();
    }
}

$turStmt = $pdo->query("SELECT \"PK_Tur\", \"Nazv_tur\" FROM \"Tur\"");
$turList = $turStmt->fetchAll(PDO::FETCH_ASSOC);

$statusStmt = $pdo->query("SELECT \"PK_Status\", \"Nazv_Status\" FROM \"Status_Zayavka\"");
$statusList = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

$polStmt = $pdo->query("SELECT \"PK_Pol\", \"Nazv_Pol\" FROM \"Pol\"");
$polList = $polStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заявки</title>
</head>
<body>
    <h1>Оформление заявки на тур</h1>

    <form method="POST">
        <label for="Familia">Фамилия:</label>
        <input type="text" name="Familia" id="Familia" required>

        <label for="Imya">Имя:</label>
        <input type="text" name="Imya" id="Imya" required>

        <label for="Otchestvo">Отчество:</label>
        <input type="text" name="Otchestvo" id="Otchestvo" required>

        <label for="NomerPasport">Номер паспорта:</label>
        <input type="text" name="NomerPasport" id="NomerPasport" maxlength="4" required>

        <label for="SeriaPasport">Серия паспорта:</label>
        <input type="text" name="SeriaPasport" id="SeriaPasport" maxlength="6" required>

        <label for="DataRozhd">Дата рождения:</label>
        <input type="date" name="DataRozhd" id="DataRozhd" required>

        <label for="PolID">Пол:</label>
        <select name="PolID" id="PolID" required>
            <option value="">Выберите пол</option>
            <?php foreach ($polList as $pol): ?>
                <option value="<?php echo htmlspecialchars($pol['PK_Pol']); ?>">
                    <?php echo htmlspecialchars($pol['Nazv_Pol']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="TurID">Тур:</label>
        <select name="TurID" id="TurID" required>
            <option value="">Выберите тур</option>
            <?php foreach ($turList as $tur): ?>
                <option value="<?php echo htmlspecialchars($tur['PK_Tur']); ?>">
                    <?php echo htmlspecialchars($tur['Nazv_tur']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="StatusID">Статус заявки:</label>
        <select name="StatusID" id="StatusID" required>
            <option value="">Выберите статус</option>
            <?php foreach ($statusList as $status): ?>
                <option value="<?php echo htmlspecialchars($status['PK_Status']); ?>">
                    <?php echo htmlspecialchars($status['Nazv_Status']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="KlientID">ID клиента:</label>
        <input type="number" name="KlientID" id="KlientID" required>

        <button type="submit">Оформить заявку</button>
    </form>

    <?php if ($bookingStatus): ?>
        <p><?php echo $bookingStatus; ?></p>
    <?php endif; ?>
</body>
</html>
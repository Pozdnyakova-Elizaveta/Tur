<?php
require 'config.php';  

$bookingStatus = '';
$client_id = null;

if (isset($_GET['client_id'])) {
    $client_id = intval($_GET['client_id']); // Получаем ID клиента из GET параметра
}

// Получаем статус по умолчанию "В обработке"
$defaultStatusStmt = $pdo->prepare("SELECT \"PK_Status\" FROM \"Status_Zayavka\" WHERE \"Nazv_Status\" = :status");
$defaultStatus = "В обработке";
$defaultStatusStmt->bindParam(':status', $defaultStatus, PDO::PARAM_STR);
$defaultStatusStmt->execute();
$defaultStatusId = $defaultStatusStmt->fetchColumn(); // получение первого столбца из результата

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $familia = $_POST['Familia'];
    $imya = $_POST['Imya'];
    $otchestvo = $_POST['Otchestvo'];
    $nomer_pasport = $_POST['NomerPasport'];
    $seria_pasport = $_POST['SeriaPasport'];
    $data_rozhd = $_POST['DataRozhd'];
    $pol_id = $_POST['PolID'];
    $tur_id = $_POST['TurID'];
    
    // Используем $defaultStatusId для статуса заявки
    $status_id = $defaultStatusId;

    $stmt = $pdo->prepare("SELECT book_tur(:familia, :imya, :otchestvo, :nomer_pasport, :seria_pasport, :data_rozhd, :pol_id, :tur_id, :status_id, :client_id)");

    $stmt->bindParam(':familia', $familia, PDO::PARAM_STR);
    $stmt->bindParam(':imya', $imya, PDO::PARAM_STR);
    $stmt->bindParam(':otchestvo', $otchestvo, PDO::PARAM_STR);
    $stmt->bindParam(':nomer_pasport', $nomer_pasport, PDO::PARAM_STR);
    $stmt->bindParam(':seria_pasport', $seria_pasport, PDO::PARAM_STR);
    $stmt->bindParam(':data_rozhd', $data_rozhd, PDO::PARAM_STR);
    $stmt->bindParam(':pol_id', $pol_id, PDO::PARAM_INT);
    $stmt->bindParam(':tur_id', $tur_id, PDO::PARAM_INT);
    $stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
    $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT); // Передача идентификатора клиента

    try {
        $stmt->execute();

        // Получение ID созданной путевки
        $tripId = $pdo->lastInsertId(); // Получение последнего вставленного ID

        // Перенаправление на новую страницу с передачей идентификаторов
        header("Location: selection_tickets.php?pk_tur=$tur_id&pk_putevka=$tripId");
        exit; // Завершаем выполнение текущего скрипта
    } catch (PDOException $e) {
        $bookingStatus = 'Ошибка: ' . $e->getMessage();
    }
}

$turStmt = $pdo->query("SELECT \"PK_Tur\", \"Nazv_tur\" FROM \"Tur\"");
$turList = $turStmt->fetchAll(PDO::FETCH_ASSOC);

$polStmt = $pdo->query("SELECT \"PK_Pol\", \"Nazv_Pol\" FROM \"Pol\"");
$polList = $polStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление путевки</title>
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

        h1, h2 {
            color: white;
        }

        form {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        label {
            display: block;
            margin-top: 15px;
            color: white;
            font-size: 16px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .container form {
            margin-top: 20px;
            width: 100%;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        p {
            color: white;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Оформление путевки</h1>

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

            <!-- Хранение ID клиента -->
            <input type="hidden" name="client_id" value="<?php echo htmlspecialchars($client_id); ?>">

            <button type="submit">Оформить путевку</button>
        </form>

        <?php if ($bookingStatus): ?>
            <p><?php echo $bookingStatus; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
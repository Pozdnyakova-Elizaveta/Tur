<?php

require 'config.php';
$message = "";
$PK_Gorod = null;
$gorod = null;
if (isset($_GET['PK_Gorod'])) {
    $PK_Gorod = intval($_GET['PK_Gorod']);
    $stmt = $pdo->prepare("SELECT \"Nazv_Gorod\" FROM \"Gorod\" WHERE \"PK_Gorod\" = :pk_g");
    $stmt->execute(['pk_g' => $PK_Gorod]);
    $gorod = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Обработка формы добавления рейса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_flight'])) {
    try{
    $data_otpr = $_POST['data_otpr'];
    $data_pribit = $_POST['data_pribit'];
    $pk_transport_sredstvo = $_POST['pk_transport_sredstvo'];
    $pk_transport_usel_otpr = $_POST['pk_transport_usel_otpr'];
    $pk_transport_usel_pribit = $_POST['pk_transport_usel_pribit'];

    $stmt = $pdo->prepare("INSERT INTO \"Reis\" (\"Data_Vrem_Otpr\", \"Data_Vrem_Pribit\", \"PK_Transport_Sredstvo\", \"PK_Transport_Usel_Otpr\", \"PK_Transport_Usel_Pribit\") 
                            VALUES (:data_otpr, :data_pribit, :pk_transport_sredstvo, :pk_transport_usel_otpr, :pk_transport_usel_pribit)");
    $stmt->execute([
        ':data_otpr' => $data_otpr,
        ':data_pribit' => $data_pribit,
        ':pk_transport_sredstvo' => $pk_transport_sredstvo,
        ':pk_transport_usel_otpr' => $pk_transport_usel_otpr,
        ':pk_transport_usel_pribit' => $pk_transport_usel_pribit
    ]);
} catch (PDOException $e) {
    $message = 'Ошибка добавления рейса' . $e->getMessage();
}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_flight'])) {
    try{
    $pk_r = intval($_POST['PK_Reis']);

    $stmt = $pdo->prepare("DELETE FROM \"Reis\" WHERE \"PK_Reis\" = :pk_r");
    $stmt->execute(['pk_r' => $pk_r]);

    header("Location: flight.php?PK_Gorod=$PK_Gorod");
    exit();
} catch (PDOException $e) {
    $message = 'Ошибка - нельзя удалить запись о рейсе';
}
}

// Получение рейсов для данного города
$flights = [];
$usel = [];
if ($PK_Gorod !== null) {
    $stmt = $pdo->prepare("SELECT 
    r.\"PK_Reis\",
    r.\"Data_Vrem_Otpr\",
    r.\"Data_Vrem_Pribit\",
    tt.\"Nazv_Tip_Transport\",
    ts.\"Nomer_Transport_Sredstvo\",
    tu_o.\"Nazv_transport_usel\" AS \"Transport_Usel_Otpr\",
    g_o.\"Nazv_Gorod\" AS \"Gorod_Otpr\",
    tu_p.\"Nazv_transport_usel\" AS \"Transport_Usel_Pribit\",
    g_p.\"Nazv_Gorod\" AS \"Gorod_Pribit\"
FROM 
    \"Reis\" r
JOIN 
    \"Transport_Sredstvo\" ts ON r.\"PK_Transport_Sredstvo\" = ts.\"PK_Transport_Sredstvo\"
JOIN 
    \"Tip_Transport\" tt ON ts.\"PK_Tip_Transport\" = tt.\"PK_Tip_Transport\"
JOIN 
    \"Transport_usel\" tu_o ON r.\"PK_Transport_Usel_Otpr\" = tu_o.\"PK_Transport_Usel\"
JOIN 
    \"Gorod\" g_o ON tu_o.\"PK_Gorod\" = g_o.\"PK_Gorod\"
JOIN 
    \"Transport_usel\" tu_p ON r.\"PK_Transport_Usel_Pribit\" = tu_p.\"PK_Transport_Usel\"
JOIN 
    \"Gorod\" g_p ON tu_p.\"PK_Gorod\" = g_p.\"PK_Gorod\"
WHERE 
    g_o.\"PK_Gorod\" = :pk_gorod OR g_p.\"PK_Gorod\" = :pk_gorod");
    $stmt->execute([':pk_gorod' => $PK_Gorod]);
    $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare("SELECT \"PK_Transport_Usel\", \"Nazv_transport_usel\" from \"Transport_usel\"");
    $stmt->execute();
    $usel = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Рейсы для города</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(45deg, #49a09d, #5f2c82);
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            color: #fff;
        }

        .hotel-form, .update-form, .delete-form {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .hotel-form input, .update-form input, .hotel-form select, .update-form select, .hotel-form textarea, .update-form textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
        }

        .hotel-form button, .update-form button, .delete-form button {
            background-color: #55608f;
            color: #fff;
            cursor: pointer;
            border: none;
            margin-top: 20px;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
        }

        .hotel-form button:hover, .update-form button:hover, .delete-form button:hover {
            background-color: #444e72;
        }

        .hotel-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .hotel-table th, .hotel-table td {
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            text-align: left;
        }

        .hotel-table th {
            background-color: #55608f;
        }

        .hotel-table tr:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .update-form input, .update-form select, .update-form textarea {
            margin-bottom: 10px;
        }

        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 5px;
        }
        .message {
            color: darkred;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<a href="javascript:history.back()" class="back-button">Назад</a>
    <h1>Рейсы для города <?php echo htmlspecialchars($gorod['Nazv_Gorod']); ?></h1>
    <?php if ($message): ?>
            <p class="message"> <?= htmlspecialchars($message) ?> </p>
    <?php endif; ?>
    <!-- Форма для добавления рейса -->
    <form method="POST" class="hotel-form">
        <label for="data_otpr">Дата и время отправления:</label>
        <input type="datetime-local" name="data_otpr" required>
        
        <label for="data_pribit">Дата и время прибытия:</label>
        <input type="datetime-local" name="data_pribit" required>
        
        <label for="pk_transport_sredstvo">ID транспортного средства:</label>
        <input type="number" name="pk_transport_sredstvo" required>
        
        <label for="pk_transport_usel_otpr">Транспортный узел (отправление):</label>
        <select name="pk_transport_usel_otpr" required>
            <option value="">Выберите узел</option>
            <?php foreach ($usel as $u): ?>
                <option value="<?php echo htmlspecialchars($u['PK_Transport_Usel']); ?>">
                    <?php echo htmlspecialchars($u['Nazv_transport_usel']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label for="pk_transport_usel_pribit">Транспортный узел (прибытие):</label>
        <select name="pk_transport_usel_pribit" required>
            <option value="">Выберите узел</option>
            <?php foreach ($usel as $u): ?>
                <option value="<?php echo htmlspecialchars($u['PK_Transport_Usel']); ?>">
                    <?php echo htmlspecialchars($u['Nazv_transport_usel']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" name="add_flight">Добавить рейс</button>
    </form>

    <h2>Список рейсов</h2>
    <table class="hotel-table">
        <tr>
            <th>ID</th>
            <th>Дата и время отправления</th>
            <th>Дата и время прибытия</th>
            <th>Транспорт</th>
            <th>Номер транспортного средства</th>
            <th>Транспортный узел отправления</th>
            <th>Город отправления</th>
            <th>Транспортный узел прибытия</th>
            <th>Город прибытия</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($flights as $flight): ?>
        <tr>
            <td><?php echo htmlspecialchars($flight['PK_Reis']); ?></td>
            <td><?php echo htmlspecialchars($flight['Data_Vrem_Otpr']); ?></td>
            <td><?php echo htmlspecialchars($flight['Data_Vrem_Pribit']); ?></td>
            <td><?php echo htmlspecialchars($flight['Nazv_Tip_Transport']); ?></td>
            <td><?php echo htmlspecialchars($flight['Nomer_Transport_Sredstvo']); ?></td>
            <td><?php echo htmlspecialchars($flight['Transport_Usel_Otpr']); ?></td>
            <td><?php echo htmlspecialchars($flight['Gorod_Otpr']); ?></td>
            <td><?php echo htmlspecialchars($flight['Transport_Usel_Pribit']); ?></td>
            <td><?php echo htmlspecialchars($flight['Gorod_Pribit']); ?></td>
            <td>
                <form method="POST" class="delete-form" onsubmit="return confirm('Вы уверены, что хотите удалить этот рейс?');">
                    <input type="hidden" name="PK_Reis" value="<?php echo htmlspecialchars($flight['PK_Reis']); ?>">
                    <button type="submit" name="delete_flight">Удалить</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
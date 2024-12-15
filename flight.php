<?php

require 'config.php';

$PK_Gorod = null;
if (isset($_GET['PK_Gorod'])) {
    $PK_Gorod = intval($_GET['PK_Gorod']);
}

// Обработка формы добавления рейса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_flight'])) {
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
}

// Получение рейсов для данного города
$flights = [];
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
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Рейсы для города</title>
</head>
<body>
    <h1>Рейсы для города с ID <?php echo htmlspecialchars($PK_Gorod); ?></h1>
    
    <!-- Форма для добавления рейса -->
    <form method="POST">
        <label for="data_otpr">Дата и время отправления:</label>
        <input type="datetime-local" name="data_otpr" required>
        
        <label for="data_pribit">Дата и время прибытия:</label>
        <input type="datetime-local" name="data_pribit" required>
        
        <label for="pk_transport_sredstvo">ID транспортного средства:</label>
        <input type="number" name="pk_transport_sredstvo" required>
        
        <label for="pk_transport_usel_otpr">ID транспортного узла (отправление):</label>
        <input type="number" name="pk_transport_usel_otpr" required>
        
        <label for="pk_transport_usel_pribit">ID транспортного узла (прибытие):</label>
        <input type="number" name="pk_transport_usel_pribit" required>
        
        <button type="submit" name="add_flight">Добавить рейс</button>
    </form>

    <h2>Список рейсов</h2>
    <table border="1">
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
                <form method="POST" action="delete_flight.php">
                    <input type="hidden" name="PK_Reis" value="<?php echo htmlspecialchars($flight['PK_Reis']); ?>">
                    <button type="submit" name="delete_flight">Удалить</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
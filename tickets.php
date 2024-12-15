<?php
require 'config.php';  

$availableTickets = [];  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reisId = $_POST['ReisID'];

    $stmt = $pdo->prepare("SELECT * FROM get_available_tickets(:reisId)");
    $stmt->bindParam(':reisId', $reisId, PDO::PARAM_INT);
    $stmt->execute();

    $availableTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query("SELECT \"PK_Reis\" FROM \"Reis\"");  
$flights = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Доступные билеты</title>
</head>
<body>
    <h1>Доступные билеты</h1>

    <h2>Выберите рейс</h2>
    <form method="POST">
        <label for="ReisID">Рейс:</label>
        <select name="ReisID" id="ReisID" required>
            <option value="">Выберите рейс</option>
            <?php foreach ($flights as $flight): ?>
                <option value="<?php echo htmlspecialchars($flight['PK_Reis']); ?>">
                    <?php echo htmlspecialchars($flight['PK_Reis']); ?>  
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Показать доступные билеты</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h2>Результаты поиска</h2>
        <?php if (!empty($availableTickets)): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Номер билета</th>
                        <th>Место</th>
                        <th>Стоимость</th>
                        <th>Категория</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($availableTickets as $ticket): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ticket['PK_Bilet']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['Nomer_Mest']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['Stoim_Bilet']); ?> руб.</td>
                            <td><?php echo htmlspecialchars($ticket['Kategoria']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Нет доступных билетов для выбранного рейса.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
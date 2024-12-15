<?php
require 'config.php';

// Переменные для хранения истории покупок
$purchaseHistory = [];
$errorMessage = '';

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = intval($_POST['client_id']); // Получение идентификатора клиента

    try {
        // Получение информации по истории покупок
        $stmt = $pdo->prepare("SELECT * FROM purchase_history(:client_id)");
        $stmt->execute(['client_id' => $client_id]);
        $purchaseHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Проверка результата
        if (empty($purchaseHistory)) {
            $errorMessage = 'Нет данных для указанного клиента.';
        }

    } catch (PDOException $e) {
        // Обработка ошибок при выполнении запроса
        $errorMessage = "Ошибка при выполнении запроса: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>История покупок</title>
</head>
<body>
    <h1>История покупок</h1>

    <!-- Форма для ввода идентификатора клиента -->
    <form method="post">
        <label for="client_id">Идентификатор клиента:</label>
        <input type="number" id="client_id" name="client_id" required>
        <button type="submit">Получить историю покупок</button>
    </form>

    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php elseif (!empty($purchaseHistory)): ?>
        <table border="1">
            <tr>
                <th>Клиент</th>
                <th>Название тура</th>
                <th>Статус</th>
                <th>ID Путевки</th>
            </tr>
            <?php foreach ($purchaseHistory as $purchase): ?>
                <tr>
                    <td><?php echo isset($purchase['klient']) ? htmlspecialchars($purchase['klient']) : 'Нет данных'; ?></td>
                    <td><?php echo isset($purchase['nazv_tura_klienta']) ? htmlspecialchars($purchase['nazv_tura_klienta']) : 'Нет данных'; ?></td>
                    <td><?php echo isset($purchase['status']) ? htmlspecialchars($purchase['status']) : 'Нет данных'; ?></td>
                    <td><?php echo isset($purchase['pk_putevka']) ? htmlspecialchars($purchase['pk_putevka']) : 'Нет данных'; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Нет данных для отображения.</p>
    <?php endif; ?>
</body>
</html>
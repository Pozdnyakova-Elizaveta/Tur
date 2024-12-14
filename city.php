<?php
require 'config.php';

// Обработка операций CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $nazv_gorod = $_POST['Nazv_Gorod'];
        $nazv_strana = $_POST['Nazv_Strana'];

        // Получаем PK_Strana по Nazv_Strana
        $stmt = $pdo->prepare("SELECT \"PK_Strana\" FROM \"Strana\" WHERE \"Nazv_Strana\" = :nazv_strana");
        $stmt->execute(['nazv_strana' => $nazv_strana]);
        $pk_strana = $stmt->fetchColumn();

        if ($pk_strana) {
            $stmt = $pdo->prepare("INSERT INTO \"Gorod\" (\"Nazv_Gorod\", \"PK_Strana\") VALUES (:nazv_gorod, :pk_strana)");
            $stmt->execute(['nazv_gorod' => $nazv_gorod, 'pk_strana' => $pk_strana]);
        }
    }

    if (isset($_POST['update'])) {
        $pk_gorod = $_POST['PK_Gorod'];
        $nazv_gorod = $_POST['Nazv_Gorod'];
        $nazv_strana = $_POST['Nazv_Strana'];

        // Получаем PK_Strana по Nazv_Strana
        $stmt = $pdo->prepare("SELECT \"PK_Strana\" FROM \"Strana\" WHERE \"Nazv_Strana\" = :nazv_strana");
        $stmt->execute(['nazv_strana' => $nazv_strana]);
        $pk_strana = $stmt->fetchColumn();

        if ($pk_strana) {
            $stmt = $pdo->prepare("UPDATE \"Gorod\" SET \"Nazv_Gorod\" = :nazv_gorod, \"PK_Strana\" = :pk_strana WHERE \"PK_Gorod\" = :pk_gorod");
            $stmt->execute(['nazv_gorod' => $nazv_gorod, 'pk_strana' => $pk_strana, 'pk_gorod' => $pk_gorod]);
        }
    }

    if (isset($_POST['delete'])) {
        $pk_gorod = $_POST['PK_Gorod'];
        $stmt = $pdo->prepare("DELETE FROM \"Gorod\" WHERE \"PK_Gorod\" = :pk_gorod");
        $stmt->execute(['pk_gorod' => $pk_gorod]);
    }
}

// Извлечение данных из Gorod
$stmt = $pdo->query("SELECT g.*, s.\"Nazv_Strana\" FROM \"Gorod\" g JOIN \"Strana\" s ON g.\"PK_Strana\" = s.\"PK_Strana\"");
$goroda = $stmt->fetchAll();

// Извлечение данных из Strana для выпадающего списка
$stmt = $pdo->query("SELECT * FROM \"Strana\"");
$strany = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD операции с таблицей Gorod</title>
</head>
<body>
    <h1>CRUD операции с таблицей Gorod</h1>

    <h2>Добавить город</h2>
    <form method="POST">
        <input type="text" name="Nazv_Gorod" required placeholder="Название города">
        <select name="Nazv_Strana" required>
            <option value="">Выберите страну</option>
            <?php foreach ($strany as $strana): ?>
                <option value="<?php echo htmlspecialchars($strana['Nazv_Strana']); ?>"><?php echo htmlspecialchars($strana['Nazv_Strana']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="add">Добавить</button>
    </form>

    <h2>Список городов</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Название города</th>
            <th>Страна</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($goroda as $gorod): ?>
            <tr>
                <td><?php echo htmlspecialchars($gorod['PK_Gorod']); ?></td>
                <td><?php echo htmlspecialchars($gorod['Nazv_Gorod']); ?></td>
                <td><?php echo htmlspecialchars($gorod['Nazv_Strana']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="PK_Gorod" value="<?php echo htmlspecialchars($gorod['PK_Gorod']); ?>">
                        <input type="text" name="Nazv_Gorod" required placeholder="Новое название" value="<?php echo htmlspecialchars($gorod['Nazv_Gorod']); ?>">
                        <select name="Nazv_Strana" required>
                            <?php foreach ($strany as $strana): ?>
                                <option value="<?php echo htmlspecialchars($strana['Nazv_Strana']); ?>" <?php echo ($strana['Nazv_Strana'] === $gorod['Nazv_Strana']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($strana['Nazv_Strana']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="update">Обновить</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="PK_Gorod" value="<?php echo htmlspecialchars($gorod['PK_Gorod']); ?>">
                        <button type="submit" name="delete" onclick="return confirm('Вы уверены, что хотите удалить этот город?');">Удалить</button>
                    </form>
                    <a href="flight.php?PK_Gorod=<?php echo htmlspecialchars($gorod['PK_Gorod']); ?>">Посмотреть рейсы</a>
                    <a href="hotel.php?PK_Gorod=<?php echo htmlspecialchars($gorod['PK_Gorod']); ?>">Посмотреть отели</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
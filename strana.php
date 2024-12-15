<?php
require 'config.php';

// Обработка операций CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $nazv_strana = $_POST['Nazv_Strana'];
        $stmt = $pdo->prepare("INSERT INTO \"Strana\" (\"Nazv_Strana\") VALUES (:nazv_strana)");
        $stmt->execute(['nazv_strana' => $nazv_strana]);
    }

    if (isset($_POST['update'])) {
        $pk_strana = $_POST['PK_Strana'];
        $nazv_strana = $_POST['Nazv_Strana'];
        $stmt = $pdo->prepare("UPDATE \"Strana\" SET \"Nazv_Strana\" = :nazv_strana WHERE \"PK_Strana\" = :pk_strana");
        $stmt->execute(['nazv_strana' => $nazv_strana, 'pk_strana' => $pk_strana]);
    }

    if (isset($_POST['delete'])) {
        $pk_strana = $_POST['PK_Strana'];
        $stmt = $pdo->prepare("DELETE FROM \"Strana\" WHERE \"PK_Strana\" = :pk_strana");
        $stmt->execute(['pk_strana' => $pk_strana]);
    }
}

// Извлечение данных
$stmt = $pdo->query("SELECT * FROM \"Strana\"");
$strany = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>CRUD операции с таблицей Strana</title>
</head>
<body>
    <h1>CRUD операции с таблицей Strana</h1>

    <h2>Добавить страну</h2>
    <form method="POST">
        <input type="text" name="Nazv_Strana" required placeholder="Название страны">
        <button type="submit" name="add">Добавить</button>
    </form>

    <h2>Список стран</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Название страны</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($strany as $strana): ?>
            <tr>
                <td><?php echo htmlspecialchars($strana['PK_Strana']); ?></td>
                <td><?php echo htmlspecialchars($strana['Nazv_Strana']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="PK_Strana" value="<?php echo htmlspecialchars($strana['PK_Strana']); ?>">
                        <input type="text" name="Nazv_Strana" required placeholder="Новое название" value="<?php echo htmlspecialchars($strana['Nazv_Strana']); ?>">
                        <button type="submit" name="update">Обновить</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="PK_Strana" value="<?php echo htmlspecialchars($strana['PK_Strana']); ?>">
                        <button type="submit" name="delete" onclick="return confirm('Вы уверены, что хотите удалить эту страну?');">Удалить</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <li><a href="city.php">Города</a></li>
</body>
</html>
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
    <title>Города</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(45deg, #49a09d, #5f2c82);
            font-family: sans-serif;
            font-weight: 100;
        }

        h1, h2 {
            color: #fff;
            text-align: center;
        }

        .container {
            width: 80%;
            max-width: 900px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            margin-top: 50px;
        }

        form {
            margin-bottom: 20px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
        }

        input[type="date"], select {
            background-color: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        button {
            background-color: #55608f;
            color: #fff;
            cursor: pointer;
            border: none;
            margin-top: 20px;
        }

        button:hover {
            background-color: #444e72;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }

        th {
            background-color: #55608f;
            color: white;
        }

        td {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        td form {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }

        td form button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        td form button[type="submit"]:hover {
            background-color: #45a049;
        }

        td a {
            display: inline-block;
            margin-top: 5px;
            padding: 5px 10px;
            background-color: #2196F3;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }

        td a:hover {
            background-color: #0b7dda;
        }

        .links {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }

        .actions {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Города</h1>

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
        <table>
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
                    <td class="actions">
                        <form method="POST">
                            <input type="hidden" name="PK_Gorod" value="<?php echo htmlspecialchars($gorod['PK_Gorod']); ?>">
                            <input type="text" name="Nazv_Gorod" required placeholder="Новое название" value="<?php echo htmlspecialchars($gorod['Nazv_Gorod']); ?>">
                            <select name="Nazv_Strana" required>
                                <?php foreach ($strany as $strana): ?>
                                    <option value="<?php echo htmlspecialchars($strana['Nazv_Strana']); ?>" <?php echo ($strana['Nazv_Strana'] === $gorod['Nazv_Strana']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($strana['Nazv_Strana']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="update">Обновить</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="PK_Gorod" value="<?php echo htmlspecialchars($gorod['PK_Gorod']); ?>">
                            <button type="submit" name="delete" onclick="return confirm('Вы уверены, что хотите удалить этот город?');">Удалить</button>
                        </form>
                        <div class="links">
                            <a href="flight.php?PK_Gorod=<?php echo htmlspecialchars($gorod['PK_Gorod']); ?>">Посмотреть рейсы</a>
                            <a href="hotel.php?PK_Gorod=<?php echo htmlspecialchars($gorod['PK_Gorod']); ?>">Посмотреть отели</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
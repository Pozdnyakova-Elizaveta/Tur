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
    <title>Страны</title>
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
            width: 90%;
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

        .links {
            display: inline-flex;
            gap: 10px;
            align-items: center;
        }

        select {
            background-color: rgba(255, 255, 255, 0.8); 
            color: #000; 
            padding: 10px;
            border-radius: 5px;
            width: 90%;
            border: none;
            margin-top: 5px;
        }

        option {
            background-color: #f5f5f5; 
            color: #000; 
        }

        option:hover {
            background-color: #ddd; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Страны</h1>
        <h2>Добавить страну</h2>
        <form method="POST">
            <input type="text" name="Nazv_Strana" required placeholder="Название страны">
            <button type="submit" name="add">Добавить</button>
        </form>

        <h2>Список стран</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Название страны</th>
                <th>Действия</th>
            </tr>
            <?php foreach ($strany as $strana): ?>
                <tr>
                    <td><?php echo htmlspecialchars($strana['PK_Strana']); ?></td>
                    <td><?php echo htmlspecialchars($strana['Nazv_Strana']); ?></td>
                    <td class="actions">
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
        <div class="links">
            <a href="city.php">Города</a>
        </div>
    </div>
</body>
</html>
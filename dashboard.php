<?php
session_start();
require 'config.php'; // Подключение к базе данных

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Перенаправление на страницу входа, если пользователь не авторизован
    exit;
}

// Получение информации о сотруднике
$sotrudnik_id = $_SESSION['user_id'];
$sotrudnik = [];
try {
    $stmt = $pdo->prepare("
        SELECT S.*, K.\"Nazv_Kompaniya\", D.\"Nazv_Dolzhnost\" 
        FROM \"Sotrudnik\" S 
        JOIN \"Kompaniya\" K ON S.\"PK_Kompaniya\" = K.\"PK_Kompaniya\"
        JOIN \"Dolzhnost\" D ON S.\"PK_Dolzhnost\" = D.\"PK_Dolzhnost\"
        WHERE S.\"PK_Sotrudnik\" = ?
    ");
    $stmt->execute([$sotrudnik_id]);
    $sotrudnik = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения данных сотрудника: ' . $e->getMessage();
}

// Получение данных для выпадающих списков
$companies = [];
$positions = [];
try {
    $stmt = $pdo->query("SELECT \"PK_Kompaniya\", \"Nazv_Kompaniya\" FROM \"Kompaniya\"");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT \"PK_Dolzhnost\", \"Nazv_Dolzhnost\" FROM \"Dolzhnost\"");
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения данных: ' . $e->getMessage();
}

// Обработка редактирования информации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $familia = trim($_POST['familia']);
    $imya = trim($_POST['imya']);
    $otchestvo = trim($_POST['otchestvo']);
    $email = trim($_POST['email']);
    $pk_kompaniya = intval($_POST['pk_kompaniya']);
    $pk_dolzhnost = intval($_POST['pk_dolzhnost']);

    try {
        $stmt = $pdo->prepare("
            UPDATE \"Sotrudnik\" 
            SET \"Familia\" = ?, \"Imya\" = ?, \"Otchestvo\" = ?, \"Email\" = ?, \"PK_Kompaniya\" = ?, \"PK_Dolzhnost\" = ? 
            WHERE \"PK_Sotrudnik\" = ?
        ");
        $stmt->execute([$familia, $imya, $otchestvo, $email, $pk_kompaniya, $pk_dolzhnost, $sotrudnik_id]);
        $message = 'Информация успешно обновлена!';
    } catch (PDOException $e) {
        $message = 'Ошибка обновления данных: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный Кабинет Сотрудника</title>
</head>
<body>
    <h1>Личный Кабинет</h1>

    <?php if (!empty($message)): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h2>Информация о сотруднике</h2>
    
    <form method="post">
        <input type="text" name="Familia" placeholder="Фамилия" value="<?= htmlspecialchars($sotrudnik['Familia']) ?>" required>
        <input type="text" name="Imya" placeholder="Имя" value="<?= htmlspecialchars($sotrudnik['Imya']) ?>" required>
        <input type="text" name="Otchestvo" placeholder="Отчество" value="<?= htmlspecialchars($sotrudnik['Otchestvo']) ?>" required>
        <input type="email" name="Email" placeholder="Email" value="<?= htmlspecialchars($sotrudnik['Email']) ?>" required>

        <label for="pk_kompaniya">Выберите компанию:</label>
        <select name="pk_kompaniya" required>
            <?php foreach ($companies as $company): ?>
                <option value="<?= $company['PK_Kompaniya'] ?>" <?= $company['PK_Kompaniya'] == $sotrudnik['PK_Kompaniya'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($company['Nazv_Kompaniya']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="pk_dolzhnost">Выберите должность:</label>
        <select name="pk_dolzhnost" required>
            <?php foreach ($positions as $position): ?>
                <option value="<?= $position['PK_Dolzhnost'] ?>" <?= $position['PK_Dolzhnost'] == $sotrudnik['PK_Dolzhnost'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($position['Nazv_Dolzhnost']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="edit">Сохранить изменения</button>
    </form>

    <h2>Доступные действия</h2>
    <ul>
        <li><a href="populardestinations.php">Анализ популярных направлений</a></li>
        <li><a href="tour_statistics.php">Проданные туры</a></li>
        <li><a href="sotr_register.php">Регистрация нового сотрудника</a></li>
    </ul>
    
    <p><a href="logout.php">Выйти</a></p>
</body>
</html>
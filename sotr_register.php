<?php
require 'config.php'; // Подключение к базе данных

$message = '';

// Получение данных компаний
$companies = [];
try {
    $stmt = $pdo->query("SELECT \"PK_Kompaniya\", \"Nazv_Kompaniya\" FROM \"Kompaniya\"");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения компаний: ' . $e->getMessage();
}

// Получение данных должностей
$positions = [];
try {
    $stmt = $pdo->query("SELECT \"PK_Dolzhnost\", \"Nazv_Dolzhnost\" FROM \"Dolzhnost\"");
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = 'Ошибка получения должностей: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Регистрация
    if (isset($_POST['register'])) {
        $familia = trim($_POST['familia']);
        $imya = trim($_POST['imya']);
        $otchestvo = trim($_POST['otchestvo']);
        $email = trim($_POST['email']);
        $pk_kompaniya = intval($_POST['pk_kompaniya']);
        $pk_dolzhnost = intval($_POST['pk_dolzhnost']);
        
        if ($familia && $imya && $otchestvo && filter_var($email, FILTER_VALIDATE_EMAIL) && $pk_kompaniya && $pk_dolzhnost) {
            try {
                $stmt = $pdo->prepare("INSERT INTO \"Sotrudnik\" (\"Familia\", \"Imya\", \"Otchestvo\", \"Email\", \"PK_Kompaniya\", \"PK_Dolzhnost\") VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$familia, $imya, $otchestvo, $email, $pk_kompaniya, $pk_dolzhnost]);
                $message = 'Регистрация успешна!';
            } catch (PDOException $e) {
                $message = 'Ошибка регистрации: ' . $e->getMessage();
            }
        } else {
            $message = 'Пожалуйста, заполните все поля корректно.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация Сотрудника</title>
</head>
<body>
    <h1>Регистрация Сотрудника</h1>

    <?php if ($message): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="familia" placeholder="Фамилия" required>
        <input type="text" name="imya" placeholder="Имя" required>
        <input type="text" name="otchestvo" placeholder="Отчество" required>
        <input type="email" name="email" placeholder="Электронная почта" required>
        
        <label for="pk_kompaniya">Выберите компанию:</label>
        <select name="pk_kompaniya" required>
            <option value="">--Выберите компанию--</option>
            <?php foreach ($companies as $company): ?>
                <option value="<?= $company['PK_Kompaniya'] ?>"><?= htmlspecialchars($company['Nazv_Kompaniya']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="pk_dolzhnost">Выберите должность:</label>
        <select name="pk_dolzhnost" required>
            <option value="">--Выберите должность--</option>
            <?php foreach ($positions as $position): ?>
                <option value="<?= $position['PK_Dolzhnost'] ?>"><?= htmlspecialchars($position['Nazv_Dolzhnost']) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="register">Зарегистрироваться</button>
    </form>

</body>
</html>
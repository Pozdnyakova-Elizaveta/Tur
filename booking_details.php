<?php 
require 'config.php'; 

// Обработка формы 
$bookingInformation = []; 
$errorMessage = ''; 
$putevka_id = null;
$turist_id = null;

// Проверка, передан ли идентификатор путевки через параметр URL
if (isset($_GET['putevka_id'])) {
    $putevka_id = intval($_GET['putevka_id']);
}

if ($putevka_id) {
    try {
        // Получение идентификатора туриста на основе идентификатора путевки
        $stmt = $pdo->prepare("SELECT \"PK_Turist\" FROM \"Putevka\" WHERE \"PK_Putevka\" = :putevka_id");
        $stmt->execute(['putevka_id' => $putevka_id]);
        $turist = $stmt->fetch(PDO::FETCH_ASSOC);

        // Проверяем, найден ли турист
        if ($turist) {
            $turist_id = $turist['PK_Turist'];
        } else {
            $errorMessage = 'Турист не найден для указанной путевки.';
        }
    } catch (PDOException $e) {
        // Обработка ошибок при выполнении запроса
        $errorMessage = "Ошибка при выполнении запроса";
    }
}

// Выполнение запроса информации о бронировании сразу по загрузке страницы
if ($turist_id) {
    try { 
        // Получение информации о бронировании 
        $stmt = $pdo->prepare("SELECT * FROM get_booking_information(:turist_id, :putevka_id)"); 
        $stmt->execute([ 
            'turist_id' => $turist_id, 
            'putevka_id' => $putevka_id, 
        ]); 
        $bookingInformation = $stmt->fetchAll(PDO::FETCH_ASSOC); 

        // Проверка результата 
        if (empty($bookingInformation)) { 
            $errorMessage = 'Нет данных для указанного туриста и путевки.'; 
        } 
    } catch (PDOException $e) { 
        // Обработка ошибок при выполнении запроса 
        $errorMessage = "Ошибка при выполнении запроса"; 
    } 
}
?> 

<!DOCTYPE html> 
<html lang="ru"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о бронировании</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background: linear-gradient(45deg, #49a09d, #5f2c82);
            font-family: sans-serif;
            font-weight: 100;
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

        h1, h2 {
            color: #fff;
            text-align: center;
        }

        form {
            margin-bottom: 30px;
        }

        label {
            color: #fff;
            display: block;
            margin-top: 10px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
        }

        input[type="number"] {
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
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            text-align: left;
            word-wrap: break-word;
        }

        th {
            background-color: #55608f;
        }

        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .table-wrapper {
            max-width: 100%;
            overflow-x: auto;
            display: block;
        }

        .table-wrapper table {
            width: 100%;
            min-width: 1200px; 
        }
    </style>
</head> 
<body> 
    <div class="container">
    <a href="javascript:history.back()" class="back-button">Назад</a>
        <h1>Информация о бронировании</h1>
        <?php if (!empty($errorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php elseif (!empty($bookingInformation)): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Турист</th>
                            <th>Название точки тура</th>
                            <th>Город</th>
                            <th>Отель</th>
                            <th>Номер комнаты</th>
                            <th>Транспорт (Прибытие)</th>
                            <th>Транспорт (Отправление)</th>
                            <th>Номер мест</th>
                            <th>Мероприятия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookingInformation as $info): ?>
                            <tr>
                                <td><?php echo isset($info['turist']) ? htmlspecialchars($info['turist']) : 'Нет данных'; ?></td>
                                <td><?php echo isset($info['nazvanie_tochka_tur']) ? htmlspecialchars($info['nazvanie_tochka_tur']) : 'Нет данных'; ?></td>
                                <td><?php echo isset($info['nazv_gorod']) ? htmlspecialchars($info['nazv_gorod']) : 'Нет данных'; ?></td>
                                <td><?php echo isset($info['nazv_otel']) ? htmlspecialchars($info['nazv_otel']) : 'Нет данных'; ?></td>
                                <td><?php echo isset($info['nomer_komnat']) ? htmlspecialchars($info['nomer_komnat']) : 'Нет данных'; ?></td>
                                <td><?php echo isset($info['transport_usel_pribit']) ? htmlspecialchars($info['transport_usel_pribit']) : 'Нет данных'; ?></td>
                                <td><?php echo isset($info['transport_usel_otpr']) ? htmlspecialchars($info['transport_usel_otpr']) : 'Нет данных'; ?></td>
                                <td><?php echo isset($info['nomer_mest']) ? htmlspecialchars($info['nomer_mest']) : 'Нет данных'; ?></td>
                                <td><?php echo isset($info['nazv_meropriyat']) ? htmlspecialchars($info['nazv_meropriyat']) : 'Нет данных'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body> 
</html>
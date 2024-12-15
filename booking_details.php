<?php 
require 'config.php'; 
 
// Обработка формы 
$bookingInformation = []; 
$errorMessage = ''; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $tourist_id = intval($_POST['tourist_id']); 
    $putevka_id = intval($_POST['putevka_id']); 
 
    try { 
        // Получение информации о бронировании 
        $stmt = $pdo->prepare("SELECT * FROM get_booking_information(:tourist_id, :putevka_id)"); 
        $stmt->execute([ 
            'tourist_id' => $tourist_id, 
            'putevka_id' => $putevka_id, 
        ]); 
        $bookingInformation = $stmt->fetchAll(PDO::FETCH_ASSOC); 
 
        // Проверка результата 
        if (empty($bookingInformation)) { 
            $errorMessage = 'Нет данных для указанного туриста и путевки.'; 
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
    <title>Информация о бронировании</title> 
</head> 
<body> 
    <h1>Получить информацию о бронировании</h1> 
    <form method="POST"> 
        <label for="tourist_id">Идентификатор туриста:</label> 
        <input type="number" name="tourist_id" required placeholder="Идентификатор туриста" min="1"> 
 
        <label for="putevka_id">Идентификатор путевки:</label> 
        <input type="number" name="putevka_id" required placeholder="Идентификатор путевки" min="1"> 
 
        <button type="submit">Получить информацию о бронировании</button> 
    </form> 
 
    <?php if (!empty($errorMessage)): ?> 
        <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p> 
    <?php elseif (!empty($bookingInformation)): ?> 
        <h2>Информация о бронировании</h2> 
        <table border="1"> 
            <tr> 
                <th>Турист</th> 
                <th>Название точки тура</th> <!-- Новый столбец --> 
                <th>Город</th> 
                <th>Отель</th> 
                <th>Номер комнаты</th> 
                <th>Транспорт (Прибытие)</th> 
                <th>Транспорт (Отправление)</th> 
                <th>Номер мест</th> 
                <th>Мероприятия</th> 
            </tr> 
            <?php foreach ($bookingInformation as $info): ?> 
                <tr> 
                    <td><?php echo isset($info['turist']) ? htmlspecialchars($info['turist']) : 'Нет данных'; ?></td> 
                    <td><?php echo isset($info['nazvanie_tochka_tur']) ? htmlspecialchars($info['nazvanie_tochka_tur']) : 'Нет данных'; ?></td> <!-- Данные для нового столбца --> 
                    <td><?php echo isset($info['nazv_gorod']) ? htmlspecialchars($info['nazv_gorod']) : 'Нет данных'; ?></td> 
                    <td><?php echo isset($info['nazv_otel']) ? htmlspecialchars($info['nazv_otel']) : 'Нет данных'; ?></td> 
                    <td><?php echo isset($info['nomer_komnat']) ? htmlspecialchars($info['nomer_komnat']) : 'Нет данных'; ?></td> 
                    <td><?php echo isset($info['transport_usel_pribit']) ? htmlspecialchars($info['transport_usel_pribit']) : 'Нет данных'; ?></td> 
                    <td><?php echo isset($info['transport_usel_otpr']) ? htmlspecialchars($info['transport_usel_otpr']) : 'Нет данных'; ?></td> 
                    <td><?php echo isset($info['nomer_mest']) ? htmlspecialchars($info['nomer_mest']) : 'Нет данных'; ?></td> 
                    <td><?php echo isset($info['nazv_meropriyat']) ? htmlspecialchars($info['nazv_meropriyat']) : 'Нет данных'; ?></td> 
                </tr> 
            <?php endforeach; ?> 
        </table> 
    <?php endif; ?> 
</body> 
</html>
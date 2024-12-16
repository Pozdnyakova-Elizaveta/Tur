<?php
require 'config.php';
$goroda=[];
$stmt = $pdo->query("SELECT * FROM \"Gorod\"");
$cities = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание тура</title>
    <script>
        function addPoint() {
            const container = document.getElementById('pointsContainer');
            const pointDiv = document.createElement('div');
            pointDiv.className = 'point';

            pointDiv.innerHTML = `
                <h4>Точка тура</h4>
                <label for="nazvanie_tochka">Название точки:</label>
                <input type="text" name="nazvanie_tochka[]" required><br><br>

                <label for="pk_gorod">Город:</label>
                <select name="pk_gorod[]" onchange="update(this)">
                    <option value="">Выберите город</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= $city['PK_Gorod'] ?>"><?= htmlspecialchars($city['Nazv_Gorod']) ?></option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="pk_reis">Рейс:</label>
                <select name="pk_reis[]" required>
                    <option value="">Сначала выберите город</option>
                </select><br><br>
                
                <label for="pk_otel">Отель:</label>
                <select name="pk_otel[]">
                    <option value="">Сначала выберите город</option>
                </select><br><br>
            `;

            container.appendChild(pointDiv);
        }

function update(selectElement) {
    const cityId = selectElement.value;
    const pointDiv = selectElement.closest('.point'); // Находим родительский элемент точки тура
    const flightSelect = pointDiv.querySelector('select[name="pk_reis[]"]');
    

    // Очистка списка рейсов и отелей
    flightSelect.innerHTML = '<option value="">Загрузка...</option>';

    if (cityId) {
        // Запрос на получение рейсов
        fetch(`get_flights.php?cityId=${cityId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ошибка! статус: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                flightSelect.innerHTML = '<option value="">Выберите рейс</option>';
                data.forEach(flight => {
                    flightSelect.innerHTML += `<option value="${flight.PK_Reis}">${flight.Data_Vrem_Otpr} - ${flight.Data_Vrem_Pribit} (${flight.Usel_otpr} -> ${flight.Usel_pribit})</option>`;
                });
            })
            .catch(error => {
                console.error("Ошибка при загрузке рейсов:", error);
                flightSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
            });
    } else {
        flightSelect.innerHTML = '<option value="">Сначала выберите город</option>';
    }
    const hotelSelect = pointDiv.querySelector('select[name="pk_otel[]"]');
    hotelSelect.innerHTML = '<option value="">Загрузка...</option>';
    if (cityId) { 
        fetch(`get_hotels.php?cityId=${cityId}`)
        .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ошибка! статус: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        hotelSelect.innerHTML = '<option value="">Не выбрано</option>';
                        data.forEach(hotel => {
                            hotelSelect.innerHTML += `<option value="${hotel.PK_Otel}">${hotel.Nazv_Otel}</option>`;
                        });
                    })
            .catch(error => {
                console.error("Ошибка при загрузке отелей:", error);
                hotelSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
            });
        } else{
            hotelSelect.innerHTML = '<option value="">Не выбрано</option>';
        }
}
    </script>
</head>
<body>
    <h1>Создание нового тура</h1>
    <form action="submit_tur.php" method="post">
        <label for="nazv_tur">Название тура:</label>
        <input type="text" id="nazv_tur" name="nazv_tur" required><br><br>

        <label for="data_nach">Дата начала:</label>
        <input type="date" id="data_nach" name="data_nach" required><br><br>

        <label for="data_zaver">Дата завершения:</label>
        <input type="date" id="data_zaver" name="data_zaver" required><br><br>

        <label for="vmest">Вместимость:</label>
        <input type="number" id="vmest" name="vmest" required><br><br>

        <label for="opisanie_tur">Описание тура:</label>
        <textarea id="opisanie_tur" name="opisanie_tur" required></textarea><br><br>

        <label for="pk_kompaniya">Ключ компании:</label>
        <input type="number" id="pk_kompaniya" name="pk_kompaniya" required><br><br>

        <div id="pointsContainer">
            <h3>Точки тура</h3>
            <div class="point">
                <h4>Точка тура</h4>
                <label for="nazvanie_tochka">Название точки:</label>
                <input type="text" name="nazvanie_tochka[]" required><br><br>

                <label for="pk_gorod">Город:</label>
                <select name="pk_gorod[]" onchange="update(this);">
                    <option value="">Выберите город</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= $city['PK_Gorod'] ?>"><?= htmlspecialchars($city['Nazv_Gorod']) ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <label for="pk_reis">Рейс:</label>
                <select name="pk_reis[]" required>
                    <option value="">Сначала выберите город</option>
                </select><br><br>

                <label for="pk_otel">Отель:</label>
                <select name="pk_otel[]">
                <option value="">Не выбрано</option>
                </select><br><br>

            </div>
        </div>
        
        <button type="button" onclick="addPoint()">Добавить точку тура</button><br><br>
        <button type="submit">Создать тур</button>
    </form>
</body>
</html>
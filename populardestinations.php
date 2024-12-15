<?php
require 'config.php'; 

// Количество записей на странице
$recordsPerPage = 10;

// Получаем номер текущей страницы из параметра GET (по умолчанию 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Вычисляем OFFSET для SQL-запроса
$offset = ($page - 1) * $recordsPerPage;

// Запрашиваем данные с LIMIT и OFFSET
$stmt = $pdo->prepare("SELECT * FROM analyze_popular_destinations() LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$popularDestinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Запрашиваем общее количество записей для вычисления количества страниц
$countStmt = $pdo->query("SELECT COUNT(*) FROM analyze_popular_destinations()");
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);
?>

<?php
require 'config.php'; 

$recordsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $recordsPerPage;

$stmt = $pdo->prepare("SELECT * FROM analyze_popular_destinations() LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$popularDestinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countStmt = $pdo->query("SELECT COUNT(*) FROM analyze_popular_destinations()");
$totalRecords = $countStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Популярные направления</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(45deg, #ff7e5f, #feb47b);
        }
        .container {
            width: 70%; 
            margin: 50px auto;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-top: 10px;
            font-size: 16px;
        }
        input {
            padding: 10px;
            margin-top: 5px;
            width: 100%;
            border-radius: 5px;
            border: none;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 400px; 
            overflow-y: auto; 
            display: block;
            table-layout: fixed; 
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            word-wrap: break-word; 
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        td:nth-child(1) {
            width: 40%; /* направление */
        }
        td:nth-child(2) {
            width: 30%; /* билеты */
        }
        td:nth-child(3) {
            width: 30%; /* выручка */
        }
        p {
            text-align: center;
            color: #f44336;
            font-size: 16px;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 4px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #0056b3;
        }
        .pagination .active {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Популярные направления</h1>
        <h2>Статистика по популярным направлениям</h2>
        
        <label for="search">Поиск:</label>
        <input type="text" id="search" onkeyup="searchTable()" placeholder="Поиск по направлению...">
        
        <?php if (!empty($popularDestinations)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Направление</th>
                        <th>Продано билетов</th>
                        <th>Общая выручка</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($popularDestinations as $destination): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($destination['destination']); ?></td>
                            <td><?php echo htmlspecialchars($destination['tickets_sold']); ?></td>
                            <td><?php echo htmlspecialchars($destination['total_revenue']); ?> руб.</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Нет данных о популярных направлениях.</p>
        <?php endif; ?>


        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">« Предыдущая</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Следующая »</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("search");
            filter = input.value.toUpperCase();
            table = document.querySelector("table");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>
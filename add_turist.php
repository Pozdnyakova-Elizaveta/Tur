<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $familia = $_POST['Famil_Turist'];
        $imya = $_POST['Imya_Turist'];
        $otchestvo = $_POST['Otchestvo_Turist'];
        $nomer_pasport = $_POST['Nomer_Pasport'];
        $seria_pasport = $_POST['Seria_Pasport'];
        $data_rozhd = $_POST['Data_Rozhd_Turist'];
        $pol_id = $_POST['PK_Pol']; 

        $stmt = $pdo->prepare("SELECT 1 FROM \"Turist\" WHERE \"Nomer_Pasport\" = :nomer_pasport AND \"Seria_Pasport\" = :seria_pasport");
        $stmt->execute(['nomer_pasport' => $nomer_pasport, 'seria_pasport' => $seria_pasport]);
        
        if ($stmt->fetchColumn()) {
            echo "Турист с такими паспортными данными уже существует!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO \"Turist\" (\"Famil_Turist\", \"Imya_Turist\", \"Otchestvo_Turist\", \"Nomer_Pasport\", \"Seria_Pasport\", \"Data_Rozhd_Turist\", \"PK_Pol\") VALUES (:familia, :imya, :otchestvo, :nomer_pasport, :seria_pasport, :data_rozhd, :pol_id)");
            $stmt->execute([
                'familia' => $familia, 
                'imya' => $imya, 
                'otchestvo' => $otchestvo, 
                'nomer_pasport' => $nomer_pasport, 
                'seria_pasport' => $seria_pasport, 
                'data_rozhd' => $data_rozhd, 
                'pol_id' => $pol_id
            ]);
            
            echo "Турист добавлен!";
        }
    }
}

$stmt = $pdo->query("SELECT * FROM \"Pol\"");
$pol = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить туриста</title>
</head>
<body>
    <h1>Добавить туриста</h1>
    <form method="POST">
        <label for="Famil_Turist">Фамилия:</label>
        <input type="text" name="Famil_Turist" required>
        <br>

        <label for="Imya_Turist">Имя:</label>
        <input type="text" name="Imya_Turist" required>
        <br>

        <label for="Otchestvo_Turist">Отчество:</label>
        <input type="text" name="Otchestvo_Turist" required>
        <br>

        <label for="Nomer_Pasport">Номер паспорта:</label>
        <input type="text" name="Nomer_Pasport" required>
        <br>

        <label for="Seria_Pasport">Серия паспорта:</label>
        <input type="text" name="Seria_Pasport" required>
        <br>

        <label for="Data_Rozhd_Turist">Дата рождения:</label>
        <input type="date" name="Data_Rozhd_Turist" required>
        <br>

        <label for="PK_Pol">Пол:</label>
        <select name="PK_Pol" required>
            <?php foreach ($pol as $gender): ?>
                <option value="<?php echo htmlspecialchars($gender['PK_Pol']); ?>">
                    <?php echo htmlspecialchars($gender['Nazv_Pol']); ?> 
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <button type="submit" name="add">Добавить туриста</button>
    </form>
</body>
</html>
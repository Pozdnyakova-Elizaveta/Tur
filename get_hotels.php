<?php
require 'config.php'; 

try {
    if (isset($_GET['cityId'])) {
        $cityId = (int)$_GET['cityId'];
        $stmt = $pdo->prepare("SELECT \"PK_Otel\", \"Nazv_Otel\" FROM \"Otel\" WHERE \"PK_Adres\" IN (SELECT \"PK_Adres\" FROM \"Adres\" WHERE \"PK_Gorod\" = :cityId)");
        $stmt->bindParam(':cityId', $cityId, PDO::PARAM_INT);
        $stmt->execute();
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($hotels);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
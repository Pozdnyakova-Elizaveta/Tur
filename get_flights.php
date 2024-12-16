<?php
require 'config.php'; 

try {
    if (isset($_GET['cityId'])) {
        $cityId = (int)$_GET['cityId'];
        $stmt = $pdo->prepare("SELECT r.\"PK_Reis\", r.\"Data_Vrem_Otpr\", r.\"Data_Vrem_Pribit\",
        tu_o.\"Nazv_transport_usel\" AS \"Usel_otpr\", tu_p.\"Nazv_transport_usel\" AS \"Usel_pribit\"
        FROM \"Reis\" r JOIN \"Transport_usel\" tu_o ON r.\"PK_Transport_Usel_Otpr\" = tu_o.\"PK_Transport_Usel\"
        JOIN \"Transport_usel\" tu_p ON r.\"PK_Transport_Usel_Pribit\" = tu_p.\"PK_Transport_Usel\"
        WHERE tu_p.\"PK_Gorod\" = :cityId;");
        $stmt->bindParam(':cityId', $cityId, PDO::PARAM_INT);
        $stmt->execute();
        $reis = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($reis);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
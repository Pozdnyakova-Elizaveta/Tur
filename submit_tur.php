<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nazv_tur = $_POST['nazv_tur'];
        $data_nach = $_POST['data_nach'];
        $data_zaver = $_POST['data_zaver'];
        $vmest = $_POST['vmest'];
        $opisanie_tur = $_POST['opisanie_tur'];
        $pk_kompaniya = $_POST['pk_kompaniya'];
        
        $tochki_tur = [];
        foreach ($_POST['nazvanie_tochka'] as $index => $nazvanie) {
            $tochki_tur[] = [
                'nazvanie' => $nazvanie,
                'otel' => $_POST['pk_otel'][$index] === '' ? null : $_POST['pk_otel'][$index],
                'reis' => $_POST['pk_reis'][$index],
                'gorod' => $_POST['pk_gorod'][$index]
            ];
        }

        // Преобразуем массив в JSON
        $tochki_tur_json = json_encode($tochki_tur);

        // Вызов функции создания тура
        $stmt = $pdo->prepare("SELECT sozdanie_tur(:nazv_tur, :data_nach, :data_zaver, :vmest, :opisanie_tur, :pk_kompaniya, :tochki_tur)");
        $stmt->bindParam(':nazv_tur', $nazv_tur);
        $stmt->bindParam(':data_nach', $data_nach);
        $stmt->bindParam(':data_zaver', $data_zaver);
        $stmt->bindParam(':vmest', $vmest);
        $stmt->bindParam(':opisanie_tur', $opisanie_tur);
        $stmt->bindParam(':pk_kompaniya', $pk_kompaniya);
        $stmt->bindParam(':tochki_tur', $tochki_tur_json);

        if ($stmt->execute()) {
            // Перенаправление на tur.php после успешного создания тура
            header('Location: tur.php');
            exit(); // Не забудьте остановить выполнение скрипта после перенаправления
        } else {
            echo "Ошибка при создании тура!";
        }
    }
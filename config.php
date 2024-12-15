<?php
// config.php

$host = 'localhost'; // Адрес сервера базы данных
$db   = 'tourism'; // Название вашей базы данных
$user = 'postgres'; // Имя пользователя базы данных
$pass = 'Postgres'; // Пароль пользователя базы данных

// Настройка DSN (Data Source Name)
$dsn = "pgsql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>
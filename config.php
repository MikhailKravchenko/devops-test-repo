<?php
    // Параметры подключения к БД берём из переменных окружения с безопасными значениями по умолчанию
    $db_host = getenv('DB_HOST') ?: 'db';
    $db_port = getenv('DB_PORT') ?: '3306';
    $db_name = getenv('DB_NAME') ?: 'test_zadanie';
    $db_user = getenv('DB_USER') ?: 'root';
    $db_pass = getenv('DB_PASS') ?: 'root';

    try {
        $dbh = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

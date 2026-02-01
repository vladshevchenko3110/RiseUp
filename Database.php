<?php

$db_config = [
    'host' => 'MySQL-5.7',
    'user' => 'root',
    'pass' => '',
    'db' => 'riseup',
];

$dsn = "mysql:host={$db_config['host']};dbname={$db_config['db']};charset=utf8mb4";
$opt = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

$db = new PDO($dsn, $db_config['user'], $db_config['pass'], $opt);
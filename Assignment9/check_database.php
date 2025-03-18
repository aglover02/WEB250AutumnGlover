<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new \PDO(
    'sqlite:' . __DIR__ 
    . DIRECTORY_SEPARATOR . 'database'
    . DIRECTORY_SEPARATOR . 'website.sqlite'
);

echo "Connected successfully!<br>";

$stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($tables);
echo "</pre>";

$stmt = $db->query("SELECT * FROM customers");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);

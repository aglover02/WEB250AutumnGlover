<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new pdo('sqlite::memory:');

echo "Connected successfully!<br>";

$stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
$tables = $stmt->fetchAll(pdo::FETCH_ASSOC);

echo "<h2>Tables</h2>";
echo "<pre>";
print_r($tables);
echo "</pre>";

$stmt = $db->query("SELECT * FROM customers");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Customers</h2>";
echo "<pre>";
print_r($customers);
echo "</pre>";

$stmt = $db->query("SELECT * FROM orders");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Orders</h2>";
echo "<pre>";
print_r($orders);
echo "</pre>";

$stmt = $db->query("SELECT * FROM order_details");
$orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Order Details</h2>";
echo "<pre>";
print_r($orderDetails);
echo "</pre>";

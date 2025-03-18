<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db = new \PDO(
    'sqlite:' . __DIR__
    . DIRECTORY_SEPARATOR . 'database'
    . DIRECTORY_SEPARATOR . 'website.sqlite'
);

echo "<h2>Customers</h2>";
$stmt = $db->query("SELECT * FROM customers");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($customers) {
    foreach ($customers as $customer) {
        echo "ID: {$customer['id']} - Name: {$customer['bill_fname']} {$customer['bill_lname']} - Phone: {$customer['phone']}<br>";
    }
} else {
    echo "No customers found.<br>";
}

echo "<h2>Orders</h2>";
$stmt = $db->query("SELECT * FROM orders");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($orders) {
    foreach ($orders as $order) {
        echo "Order ID: {$order['id']} - Customer ID: {$order['customer_id']} - Total Price: \${$order['total_price']} - Status: {$order['status']}<br>";
    }
} else {
    echo "No orders found.<br>";
}

echo "<h2>Order Details</h2>";
$stmt = $db->query("SELECT * FROM order_details");
$orderDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($orderDetails) {
    foreach ($orderDetails as $detail) {
        echo "Order ID: {$detail['order_id']} - Size: {$detail['size']} - Toppings: {$detail['toppings']} - Quantity: {$detail['quantity']} - Price Per Unit: \${$detail['price_per_unit']}<br>";
    }
} else {
    echo "No order details found.<br>";
}
?>

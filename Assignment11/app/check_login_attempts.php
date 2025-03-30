<?php
try {
    $db = new PDO(
        'mysql:host=web250-db;dbname=website',
        'webuser',
        'f@gd9dgjl!',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $phone = trim($_GET['phone'] ?? '');
    echo "Looking up phone: " . htmlspecialchars($phone) . "<br>";

    $stmt = $db->prepare('SELECT * FROM customers WHERE phone = :phone');
    $stmt->execute(['phone' => $phone]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<pre>Customer: ";
    print_r($customer);
    echo "</pre>";

    if ($customer) {
        $stmt2 = $db->prepare('SELECT * FROM orders WHERE customer_id = :customer_id');
        $stmt2->execute(['customer_id' => $customer['id']]);
        $orders = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>Orders: ";
        print_r($orders);
        echo "</pre>";
    } else {
        echo "No customer found with that phone number.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
<?php
try {
    $db = new PDO(
        'sqlite:' . __DIR__
        . DIRECTORY_SEPARATOR . 'database'
        . DIRECTORY_SEPARATOR . 'website.sqlite'
    );

    // Get POST data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $item_name = $_POST['item_name'];
    $size = $_POST['size'];
    $toppings = $_POST['toppings'] ?? '';
    $quantity = intval($_POST['quantity']);
    $comments = $_POST['comments'] ?? '';

    // Validate input
    if (empty($fname) || empty($lname) || empty($phone) || empty($email) || empty($item_name) || empty($size) || $quantity < 1) {
        throw new Exception('All required fields must be filled out.');
    }

    // Insert customer if they don't exist
    $stmt = $db->prepare('SELECT id FROM customers WHERE phone = :phone');
    $stmt->execute(['phone' => $phone]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        $stmt = $db->prepare('
            INSERT INTO customers (bill_fname, bill_lname, phone, email)
            VALUES (:fname, :lname, :phone, :email)
        ');
        $stmt->execute([
            'fname' => $fname,
            'lname' => $lname,
            'phone' => $phone,
            'email' => $email,
        ]);
        $customer_id = $db->lastInsertId();
    } else {
        $customer_id = $customer['id'];
    }

    // Calculate prices
    $base_price = match ($size) {
        'Small' => 8.99,
        'Medium' => 12.99,
        'Large' => 15.99,
        default => throw new Exception('Invalid size.'),
    };
    $topping_price = strlen($toppings) > 0 ? count(explode(',', $toppings)) * 1.50 : 0.0;
    $total_price = ($base_price + $topping_price) * $quantity;

    // Insert order
    $stmt = $db->prepare('
        INSERT INTO orders (customer_id, order_date, total_price, comments)
        VALUES (:customer_id, :order_date, :total_price, :comments)
    ');
    $stmt->execute([
        'customer_id' => $customer_id,
        'order_date' => date('Y-m-d H:i:s'),
        'total_price' => $total_price,
        'comments' => $comments,
    ]);
    $order_id = $db->lastInsertId();

    // Insert order details
    $stmt = $db->prepare('
        INSERT INTO order_details (order_id, item_name, size, toppings, quantity, price_per_unit, topping_price)
        VALUES (:order_id, :item_name, :size, :toppings, :quantity, :price_per_unit, :topping_price)
    ');
    $stmt->execute([
        'order_id' => $order_id,
        'item_name' => $item_name,
        'size' => $size,
        'toppings' => $toppings,
        'quantity' => $quantity,
        'price_per_unit' => $base_price,
        'topping_price' => $topping_price,
    ]);

    echo "Order placed successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}


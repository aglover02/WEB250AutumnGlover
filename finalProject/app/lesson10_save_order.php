<?php

try {
    $db = new \PDO(
        'mysql:host=web250-db;dbname=website',
        'webuser',
        'f@gd9dgjl!',
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
    );

    //read JSON input
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['customer'], $data['pizzas'])) {
        http_response_code(400);
        echo "Invalid order data.";
        exit;
    }

    $customer = $data['customer'];
    $pizzas = $data['pizzas'];

    //check if customer exists
    $stmt = $db->prepare('SELECT id FROM customers WHERE phone = :phone');
    $stmt->execute(['phone' => $customer['phone']]);
    $customerRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customerRow) {
        $stmt = $db->prepare('INSERT INTO customers (bill_fname, bill_lname, phone, email, address) VALUES (:fname, :lname, :phone, :email, :address)');
        $stmt->execute([
            'fname' => $customer['fname'],
            'lname' => $customer['lname'],
            'phone' => $customer['phone'],
            'email' => $customer['email'],
            'address' => $customer['address']
        ]);
        $customer_id = $db->lastInsertId();
    } else {
        $customer_id = $customerRow['id'];
    }

    //insert order with tax
    $subtotal = array_reduce($pizzas, fn($sum, $p) => $sum + $p['price'], 0);
    $tax = $subtotal * 0.10; 
    $total_price = $subtotal + $tax;

    $stmt = $db->prepare('INSERT INTO orders (customer_id, order_date, total_price, comments) VALUES (:customer_id, :order_date, :total_price, :comments)');
    $stmt->execute([
        'customer_id' => $customer_id,
        'order_date' => date('M-d-y H:i:s'),
        'total_price' => $total_price,
        'comments' => $customer['comments']
    ]);
    $order_id = $db->lastInsertId();

    //insert each pizza into order_details
    foreach ($pizzas as $pizza) {
        $stmt = $db->prepare('INSERT INTO order_details (order_id, item_name, size, toppings, quantity, price_per_unit, topping_price) VALUES (:order_id, :item_name, :size, :toppings, :quantity, :price_per_unit, :topping_price)');
        $stmt->execute([
            'order_id' => $order_id,
            'item_name' => "Pizza",
            'size' => $pizza['size'],
            'toppings' => implode(", ", $pizza['toppings']),
            'quantity' => $pizza['quantity'],
            'price_per_unit' => $pizza['price'] / $pizza['quantity'],
            'topping_price' => 0 //already included in price calculations
        ]);
    }

    echo json_encode(["message" => "Order saved successfully!", "order_id" => $order_id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}
<?php

try {
    $db = new \PDO(
        'mysql:host=web250-db;dbname=website',
        'webuser',
        'f@gd9dgjl!',
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
    );

    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data || !isset($data['customer'], $data['pizzas'])) {
        http_response_code(400);
        echo "Invalid order data.";
        exit;
    }

    $customer = $data['customer'];
    $pizzas = $data['pizzas'];

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

    $subtotal = array_reduce($pizzas, fn($sum, $p) => $sum + $p['price'], 0);

    //get tax rate from the external REST API using the provided zip code
    $zip = $customer['zip'];
    $tax_url = "http://assignment2example-env-1v2.eba-m9eezpmg.us-east-1.elasticbeanstalk.com/taxrates/IL/" . urlencode($zip);
    $curl = curl_init($tax_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $api_response = curl_exec($curl);
    if (curl_errno($curl)) {
         $tax_rate = 0.10; // if error occurs
    } else {
         $api_data = json_decode($api_response, true);
         $tax_rate = isset($api_data['tax_rate']) ? $api_data['tax_rate'] : 0.10;
    }
    curl_close($curl);

    // Calculate tax based on retrieved rate
    $tax = $subtotal * $tax_rate;
    $total_price = $subtotal + $tax;

    // --- Insert order with tax into orders table ---
    // Using a proper date format for later reporting (Y-m-d H:i:s)
    $stmt = $db->prepare('INSERT INTO orders (customer_id, order_date, total_price, tax, comments) VALUES (:customer_id, :order_date, :total_price, :tax, :comments)');
    $stmt->execute([
        'customer_id' => $customer_id,
        'order_date' => date('Y-m-d H:i:s'),
        'total_price' => $total_price,
        'tax' => $tax,
        'comments' => $customer['comments']
    ]);
    $order_id = $db->lastInsertId();

    foreach ($pizzas as $pizza) {
        $stmt = $db->prepare('INSERT INTO order_details (order_id, item_name, size, toppings, quantity, price_per_unit, topping_price) VALUES (:order_id, :item_name, :size, :toppings, :quantity, :price_per_unit, :topping_price)');
        $stmt->execute([
            'order_id' => $order_id,
            'item_name' => "Pizza",
            'size' => $pizza['size'],
            'toppings' => implode(", ", $pizza['toppings']),
            'quantity' => $pizza['quantity'],
            'price_per_unit' => $pizza['price'] / $pizza['quantity'],
            'topping_price' => 0 
        ]);
    }

    echo json_encode(["message" => "Order saved successfully!", "order_id" => $order_id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}

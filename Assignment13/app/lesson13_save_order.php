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

    // Fetch tax rate from external API using the customer's zip code instead of client-provided value.
    $zip = $customer['zip'] ?? '';
    if (empty($zip)) {
        http_response_code(400);
        echo json_encode(["error" => "Zip code not provided"]);
        exit;
    }

    $apiUrl = "http://assignment2example-env-1v2.eba-m9eezpmg.us-east-1.elasticbeanstalk.com/taxrates/IL/" . urlencode($zip);
    $user_agent = "YourApp/1.0 (https://yourdomain.com)";
    $options  = array("http" => array("user_agent" => $user_agent));
    $context  = stream_context_create($options);

    $apiData = file_get_contents($apiUrl, false, $context);
    if ($apiData === false) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch tax rate from API."]);
        exit;
    }

    $apiJson = json_decode($apiData, true);
    if (!isset($apiJson['EstimatedCombinedRate'])) {
        http_response_code(500);
        echo json_encode(["error" => "Tax rate not found in API response."]);
        exit;
    }

    $tax_rate = (float)$apiJson['EstimatedCombinedRate'];

    // Check if the customer already exists
    $stmt = $db->prepare('SELECT id FROM customers WHERE phone = :phone');
    $stmt->execute(['phone' => $customer['phone']]);
    $customerRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customerRow) {
        $stmt = $db->prepare('INSERT INTO customers (bill_fname, bill_lname, phone, email, address) VALUES (:fname, :lname, :phone, :email, :address)');
        $stmt->execute([
            'fname'   => $customer['fname'],
            'lname'   => $customer['lname'],
            'phone'   => $customer['phone'],
            'email'   => $customer['email'],
            'address' => $customer['address']
        ]);
        $customer_id = $db->lastInsertId();
    } else {
        $customer_id = $customerRow['id'];
    }

    // Calculate the order subtotal
    $subtotal = array_reduce($pizzas, fn($sum, $p) => $sum + $p['price'], 0);

    // Calculate tax using the fetched tax rate and compute the total price
    $tax = $subtotal * $tax_rate;
    $total_price = $subtotal + $tax;

    // Insert order with tax into orders table
    $stmt = $db->prepare('INSERT INTO orders (customer_id, order_date, total_price, tax, comments) VALUES (:customer_id, :order_date, :total_price, :tax, :comments)');
    $stmt->execute([
        'customer_id' => $customer_id,
        'order_date'  => date('Y-m-d H:i:s'),
        'total_price' => $total_price,
        'tax'         => $tax,
        'comments'    => $customer['comments']
    ]);
    $order_id = $db->lastInsertId();

    // Insert each pizza order detail into order_details table
    foreach ($pizzas as $pizza) {
        $stmt = $db->prepare('INSERT INTO order_details (order_id, item_name, size, toppings, quantity, price_per_unit, topping_price) VALUES (:order_id, :item_name, :size, :toppings, :quantity, :price_per_unit, :topping_price)');
        $stmt->execute([
            'order_id'      => $order_id,
            'item_name'     => "Pizza",
            'size'          => $pizza['size'],
            'toppings'      => implode(", ", $pizza['toppings']),
            'quantity'      => $pizza['quantity'],
            'price_per_unit'=> $pizza['price'] / $pizza['quantity'],
            'topping_price' => 0 
        ]);
    }

    echo json_encode(["message" => "Order saved successfully!", "order_id" => $order_id]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error: " . $e->getMessage()]);
}

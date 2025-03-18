<?php
$db = new \pdo(
    'sqlite:' . __DIR__
    . DIRECTORY_SEPARATOR . 'database'
    . DIRECTORY_SEPARATOR . 'website.sqlite'
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizza Order System</title>
    <script>
        let order = {
            customer: {},
            pizzas: [],
            totalPrice: 0,
            taxRate: 0.1
        };

        function addPizza() {
            const size = document.getElementById("size").value;
            const toppings = Array.from(document.querySelectorAll('input[name="toppings"]:checked')).map(t => t.value);
            const quantity = parseInt(document.getElementById("quantity").value);

            const basePrice = size === "Small" ? 8 : size === "Medium" ? 12 : 15;
            const toppingPrice = size === "Small" ? 1 : size === "Medium" ? 1.5 : 2;
            const pizzaPrice = (basePrice + toppingPrice * toppings.length) * quantity;

            const pizza = { size, toppings, quantity, price: pizzaPrice };
            order.pizzas.push(pizza);

            updateOrderSummary();
        }

        function updateOrderSummary() {
            order.totalPrice = order.pizzas.reduce((sum, pizza) => sum + pizza.price, 0);
            const tax = order.totalPrice * order.taxRate;
            const totalWithTax = order.totalPrice + tax;

            document.getElementById("order-summary").innerHTML = `
                <h3>Order Summary</h3>
                ${order.pizzas.map((pizza, index) => `
                    <p>Pizza ${index + 1}: ${pizza.size}, ${pizza.toppings.join(", ")} x ${pizza.quantity} - $${pizza.price.toFixed(2)}</p>
                `).join("")}
                <p><strong>Subtotal:</strong> $${order.totalPrice.toFixed(2)}</p>
                <p><strong>Tax (10%):</strong> $${tax.toFixed(2)}</p>
                <p><strong>Total:</strong> $${totalWithTax.toFixed(2)}</p>
            `;
        }

        function submitOrder() {
            order.customer = {
                fname: document.getElementById("fname").value,
                lname: document.getElementById("lname").value,
                address: document.getElementById("address").value,
                phone: document.getElementById("phone").value,
                comments: document.getElementById("comments").value
            };

            fetch("/Assignment9/lesson09_save_order.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(order)
            })
            .then(response => response.text())
            .then(data => {
                alert("Order submitted successfully!");
                console.log(data);
            })
            .catch(error => console.error("Error:", error));
        }
    </script>
</head>
<body>
    <h1>Pizza Order System</h1>

    <section>
        <h2>Customer Information</h2>
        <form id="customer-form" onsubmit="event.preventDefault(); submitOrder();">
            <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" required><br>

            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" required><br>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br>

            <label for="comments">Special Notes/Delivery Instructions:</label>
            <textarea id="comments" name="comments"></textarea><br>
        </form>
    </section>

    <section>
        <h2>Build Your Pizza</h2>
        <div>
            <label for="size">Size:</label>
            <select id="size" name="size" required>
                <option value="Small">Small ($8)</option>
                <option value="Medium">Medium ($12)</option>
                <option value="Large">Large ($15)</option>
            </select><br>

            <label for="toppings">Toppings:</label><br>
            <div id="toppings">
            <input type="checkbox" name="toppings" value="Pepperoni"> Pepperoni<br>
            <input type="checkbox" name="toppings" value="Mushrooms"> Mushrooms<br>
            <input type="checkbox" name="toppings" value="Onions"> Onions<br>
            <input type="checkbox" name="toppings" value="Sausage"> Sausage<br>
            <input type="checkbox" name="toppings" value="Bacon"> Bacon<br>
            <input type="checkbox" name="toppings" value="Extra Cheese"> Extra Cheese<br>
            </div>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required><br>

            <button type="button" onclick="addPizza()">Add Pizza</button>
        </div>
    </section>

    <section id="order-summary">
        <h2>Order Summary</h2>
        <p>No pizzas added yet.</p>
    </section>

    <button type="button" onclick="submitOrder()">Submit Order</button>

    <section>
        <h2>Your Orders</h2>
        <form method="GET">
            <label for="phone_lookup">Enter Phone Number:</label>
            <input type="text" id="phone_lookup" name="phone" required>
            <button type="submit">Check Orders</button>
        </form>

        <div id="customer-orders">
            <?php
            if (!empty($_GET['phone'])) {
                $stmt = $db->prepare('SELECT o.id, o.order_date, o.total_price, o.status, od.size, od.toppings, od.quantity FROM orders o JOIN order_details od ON o.id = od.order_id WHERE o.customer_id = (SELECT id FROM customers WHERE phone = :phone)');
                $stmt->execute(['phone' => $_GET['phone']]);
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($orders) {
                    foreach ($orders as $order) {
                        echo "<p>Order ID: {$order['id']}<br>";
                        echo "Date: {$order['order_date']}<br>";
                        echo "Size: {$order['size']}<br>";
                        echo "Toppings: {$order['toppings']}<br>";
                        echo "Quantity: {$order['quantity']}<br>";
                        echo "Total Price: \${$order['total_price']}<br>";
                        echo "Status: {$order['status']}</p>";
                        echo "<hr>";
                    }
                } else {
                    echo "<p>No orders found.</p>";
                }
            }
            ?>
        </div>
    </section>
</body>
</html>

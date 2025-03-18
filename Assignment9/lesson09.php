<?php
$db = new \PDO(
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+10&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles.css">
    <title>Pizza Order System</title>
    <script>
        let order = {
            customer: {},
            pizzas: [],
            totalPrice: 0,
            taxRate: 0.1
        };

        function addPizza() {
            console.log("addPizza() called");
            const size = document.getElementById("size").value;
            const toppings = Array.from(document.querySelectorAll('input[name="toppings"]:checked')).map(t => t.value);
            const quantity = parseInt(document.getElementById("quantity").value);
            console.log("size:", size);
            console.log("toppings:", toppings);
            console.log("quantity:", quantity);
            const basePrice = size === "Small" ? 8 : size === "Medium" ? 12 : 15;
            const toppingPrice = size === "Small" ? 1 : size === "Medium" ? 1.5 : 2;
            const pizzaPrice = (basePrice + toppingPrice * toppings.length) * quantity;
            console.log("pizzaPrice:", pizzaPrice);
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
                comments: document.getElementById("comments").value,
                email: document.getElementById("email").value
            };

            fetch("/router.php/lesson09_save_order", {
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

    <!-- Customer Information Section -->
    <section id="customer-info">
        <h2>Customer Information</h2>
        <form id="customer-form" onsubmit="event.preventDefault(); submitOrder();">
            <p>
                <label for="fname">First Name:</label>
                <input type="text" id="fname" name="fname" required>
            </p>
            <p>
                <label for="lname">Last Name:</label>
                <input type="text" id="lname" name="lname" required>
            </p>
            <p>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required>
            </p>
            <p>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required>
            </p>
            <p>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </p>
            <p>
                <label for="comments">Special Notes/Delivery Instructions:</label>
                <textarea id="comments" name="comments"></textarea>
            </p>
        </form>
    </section>

    <section id="pizza-builder">
        <h2>Build Your Pizza</h2>
        <div>
            <p>
                <label for="size">Size:</label>
                <select id="size" name="size" required>
                    <option value="Small">Small ($8 base + $1 per topping)</option>
                    <option value="Medium">Medium ($12 base + $1.50 per topping)</option>
                    <option value="Large">Large ($15 base + $2 per topping)</option>
                </select>
            </p>
            <p>
                <label for="toppings">Toppings (cost depends on size):</label><br>
                <div id="toppings">
                    <input type="checkbox" name="toppings" value="Pepperoni"> Pepperoni ($1 / $1.50 / $2)<br>
                    <input type="checkbox" name="toppings" value="Mushrooms"> Mushrooms ($1 / $1.50 / $2)<br>
                    <input type="checkbox" name="toppings" value="Onions"> Onions ($1 / $1.50 / $2)<br>
                    <input type="checkbox" name="toppings" value="Sausage"> Sausage ($1 / $1.50 / $2)<br>
                    <input type="checkbox" name="toppings" value="Bacon"> Bacon ($1 / $1.50 / $2)<br>
                    <input type="checkbox" name="toppings" value="Extra Cheese"> Extra Cheese ($1 / $1.50 / $2)<br>
                </div>
            </p>
            <p>
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" min="1" value="1" required>
            </p>
            <p>
                <button type="button" onclick="addPizza()">Add Pizza</button>
            </p>
        </div>
    </section>

    <section id="order-summary">
        <h2>Order Summary</h2>
        <p>No pizzas added yet.</p>
        <p><button type="button" onclick="submitOrder()">Submit Order</button></p>
    </section>

    <section id="customer-orders-section">
        <h2>Your Orders</h2>
        <form method="GET">
            <p>
                <label for="phone_lookup">Enter Phone Number:</label>
                <input type="text" id="phone_lookup" name="phone" required>
                <button type="submit">Check Orders</button>
            </p>
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

    <section id="employee-orders">
        <h2>Current Orders</h2>
        <?php
        $employeePassword = "secret123";
        function displayEmployeeLoginForm() {
            ?>
            <form method="POST">
                <p>
                    <label for="employee_password">Enter Employee Password:</label>
                    <input type="password" id="employee_password" name="employee_password" required>
                    <button type="submit">Login</button>
                </p>
            </form>
            <?php
        }
        if (isset($_POST['employee_password'])) {
            if ($_POST['employee_password'] === $employeePassword) {
                echo "<h3>Employee Orders</h3>";
                $stmt = $db->query("
                    SELECT o.id AS order_id, o.order_date, o.total_price, o.status, o.comments,
                           c.bill_fname, c.bill_lname, c.phone, c.email, c.address
                    FROM orders o
                    JOIN customers c ON o.customer_id = c.id
                    ORDER BY o.order_date DESC
                ");
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($orders) {
                    echo "<table>";
                    echo "<tr><th>Order ID</th><th>Date</th><th>Customer</th><th>Address</th><th>Phone</th><th>Email</th><th>Comments</th><th>Total Price</th><th>Status</th><th>Pizza Details</th></tr>";
                    foreach ($orders as $order) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['order_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['bill_fname'] . " " . $order['bill_lname']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['address'] ?? '') . "</td>";
                        echo "<td>" . htmlspecialchars($order['phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['comments']) . "</td>";
                        echo "<td>$" . htmlspecialchars($order['total_price']) . "</td>";
                        echo "<td>" . htmlspecialchars($order['status']) . "</td>";
                        $stmt2 = $db->prepare("SELECT * FROM order_details WHERE order_id = :order_id");
                        $stmt2->execute(['order_id' => $order['order_id']]);
                        $pizzas = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                        echo "<td>";
                        if ($pizzas) {
                            echo "<ul class='list'>";
                            foreach ($pizzas as $pizza) {
                                echo "<li>" .
                                    htmlspecialchars($pizza['size']) . " pizza with " .
                                    htmlspecialchars($pizza['toppings']) . " x " .
                                    htmlspecialchars($pizza['quantity']) .
                                    " (Price per unit: $" . htmlspecialchars($pizza['price_per_unit']) . ")" .
                                    "</li>";
                            }
                            echo "</ul>";
                        } else {
                            echo "No pizza details available.";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No orders found.</p>";
                }
            } else {
                echo "<p style='color:red;'>Incorrect password. Please try again.</p>";
                displayEmployeeLoginForm();
            }
        } else {
            displayEmployeeLoginForm();
        }
        ?>
    </section>
</body>
</html>

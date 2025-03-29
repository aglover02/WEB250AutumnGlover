<?php
session_start();
$db = new \PDO(
    'mysql:host=web250-db;dbname=website',
    'webuser',
    'f@gd9dgjl!',
    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
);

// Added logout processing for both manager and employee
if (isset($_POST['logout'])) {
    unset($_SESSION['manager_logged_in'], $_SESSION['manager_username']);
}
if (isset($_POST['logout_employee'])) {
    unset($_SESSION['employee_logged_in'], $_SESSION['employee_username']);
}

// Check for customer info cookie and decode if available
$customer_info = [];
if (isset($_COOKIE['customer_info'])) {
    $decoded = json_decode($_COOKIE['customer_info'], true);
    if (is_array($decoded)) {
        $customer_info = $decoded;
    }
}
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
            document.getElementById("order-summary").innerHTML = 
                `<h3>Order Summary</h3>
                ${order.pizzas.map((pizza, index) => 
                    `<p>Pizza ${index + 1}: ${pizza.size}, ${pizza.toppings.join(", ")} x ${pizza.quantity} - $${pizza.price.toFixed(2)}</p>`
                ).join("")}
                <p><strong>Subtotal:</strong> $${order.totalPrice.toFixed(2)}</p>
                <p><strong>Tax (10%):</strong> $${tax.toFixed(2)}</p>
                <p><strong>Total:</strong> $${totalWithTax.toFixed(2)}</p>`;
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

            fetch("/router.php/lesson11_save_order", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(order)
            })
            .then(response => response.text())
            .then(data => {
                // Set cookie to remember customer info for 30 days
                document.cookie = "customer_info=" + encodeURIComponent(JSON.stringify(order.customer)) + "; path=/; max-age=" + (60*60*24*30);
                alert("Order submitted successfully!");
                console.log(data);
            })
            .catch(error => console.error("Error:", error));
        }
    </script>
</head>
<body>
    <h1>Pizza Order System</h1>

    <section id="customer-info">
        <h2>Customer Information</h2>
        <form id="customer-form" onsubmit="event.preventDefault(); submitOrder();">
            <p>
                <label for="fname">First Name:</label>
                <input type="text" id="fname" name="fname" required value="<?php echo isset($customer_info['fname']) ? htmlspecialchars($customer_info['fname']) : ''; ?>">
            </p>
            <p>
                <label for="lname">Last Name:</label>
                <input type="text" id="lname" name="lname" required value="<?php echo isset($customer_info['lname']) ? htmlspecialchars($customer_info['lname']) : ''; ?>">
            </p>
            <p>
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required value="<?php echo isset($customer_info['address']) ? htmlspecialchars($customer_info['address']) : ''; ?>">
            </p>
            <p>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" required value="<?php echo isset($customer_info['phone']) ? htmlspecialchars($customer_info['phone']) : ''; ?>">
            </p>
            <p>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($customer_info['email']) ? htmlspecialchars($customer_info['email']) : ''; ?>">
            </p>
            <p>
                <label for="comments">Special Notes/Delivery Instructions:</label>
                <textarea id="comments" name="comments"><?php echo isset($customer_info['comments']) ? htmlspecialchars($customer_info['comments']) : ''; ?></textarea>
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
    </section>
    <p><button type="button" onclick="submitOrder()">Submit Order</button></p>

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

    <!-- Employee-only section -->
    <section id="employee-dashboard">
        <h2>Employee Dashboard (Employee Only)</h2>
        <?php        
        // Process employee login submission
        if (isset($_POST['employee_login'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $stmt = $db->prepare("SELECT password FROM employees WHERE username = :username AND status = 'employee'");
            $stmt->execute(['username' => $username]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            $login_success = false;
            if ($employee && password_verify($password, $employee['password'])) {
                $_SESSION['employee_logged_in'] = true;
                $_SESSION['employee_username'] = $username;
                $login_success = true;
            } else {
                echo "<p style='color:red;'>Invalid employee credentials.</p>";
            }
            // Record employee login attempt
            $stmt = $db->prepare("INSERT INTO login_attempts (username, role, success, attempt_time) VALUES (:username, 'employee', :success, NOW())");
            $stmt->execute(['username' => $username, 'success' => $login_success ? 1 : 0]);
        }
        
        if (!isset($_SESSION['employee_logged_in'])) {
        ?>
            <form method="POST">
                <input type="hidden" name="employee_login" value="1">
                <p>
                    <label for="username_emp">Employee Username:</label>
                    <input type="text" name="username" id="username_emp" required>
                </p>
                <p>
                    <label for="password_emp">Password:</label>
                    <input type="password" name="password" id="password_emp" required>
                </p>
                <button type="submit">Login as Employee</button>
            </form>
        <?php
        } else {
            echo "<p>Logged in as Employee: " . htmlspecialchars($_SESSION['employee_username']) . "</p>";
            echo '<form method="POST"><button type="submit" name="logout_employee" value="1">Log Out</button></form>';
            echo "<p>Employee Dashboard Content</p>";
        }
        ?>
    </section>

    <!-- Manager-only employee management section -->
    <section id="employee-management">
        <h2>Employee Management (Manager Only)</h2>
        <?php        
        // Process manager login submission with login attempt recording
        if (isset($_POST['manager_login'])) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $stmt = $db->prepare("SELECT password FROM employees WHERE username = :username AND status = 'manager'");
            $stmt->execute(['username' => $username]);
            $manager = $stmt->fetch(PDO::FETCH_ASSOC);
            $login_success = false;
            if ($manager && password_verify($password, $manager['password'])) {
                $_SESSION['manager_logged_in'] = true;
                $_SESSION['manager_username'] = $username;
                $login_success = true;
            } else {
                echo "<p style='color:red;'>Invalid manager credentials.</p>";
            }
            // Record manager login attempt
            $stmt = $db->prepare("INSERT INTO login_attempts (username, role, success, attempt_time) VALUES (:username, 'manager', :success, NOW())");
            $stmt->execute(['username' => $username, 'success' => $login_success ? 1 : 0]);
        }
        
        // If manager not logged in, show login form
        if (!isset($_SESSION['manager_logged_in'])) {
        ?>
            <form method="POST">
                <input type="hidden" name="manager_login" value="1">
                <p>
                    <label for="username">Manager Username:</label>
                    <input type="text" name="username" id="username" required>
                </p>
                <p>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </p>
                <button type="submit">Login as Manager</button>
            </form>
        <?php
        } else {
            echo "<p>Logged in as Manager: " . htmlspecialchars($_SESSION['manager_username']) . "</p>";
            // Added logout button for managers
            echo '<form method="POST"><button type="submit" name="logout" value="1">Log Out</button></form>';
            
            // Display all employee accounts
            $stmt = $db->query("SELECT id, username, full_name, status FROM employees");
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($employees) {
                echo "<h3>Employee Accounts</h3>";
                echo "<table border='1'>";
                echo "<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Status</th><th>Actions</th></tr>";
                foreach ($employees as $emp) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($emp['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($emp['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($emp['full_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($emp['status']) . "</td>";
                    echo "<td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='edit_employee' value='" . htmlspecialchars($emp['id']) . "'>
                            <button type='submit'>Edit</button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No employee accounts found.</p>";
            }
            
            // Process new employee account creation
            if (isset($_POST['create_employee'])) {
                $new_username = $_POST['new_username'] ?? '';
                $new_full_name = $_POST['new_full_name'] ?? '';
                $new_password = $_POST['new_password'] ?? '';
                $new_status = $_POST['new_status'] ?? 'employee';
                if ($new_username && $new_full_name && $new_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = $db->prepare("INSERT INTO employees (username, full_name, password, status) VALUES (:username, :full_name, :password, :status)");
                    $stmt->execute([
                        'username' => $new_username,
                        'full_name' => $new_full_name,
                        'password' => $hashed_password,
                        'status' => $new_status
                    ]);
                    echo "<p style='color:green;'>Employee account created successfully.</p>";
                } else {
                    echo "<p style='color:red;'>Please fill all required fields to create an employee account.</p>";
                }
            }
            ?>
            <h3>Create New Employee Account</h3>
            <form method="POST">
                <p>
                    <label for="new_username">Username:</label>
                    <input type="text" id="new_username" name="new_username" required>
                </p>
                <p>
                    <label for="new_full_name">Full Name:</label>
                    <input type="text" id="new_full_name" name="new_full_name" required>
                </p>
                <p>
                    <label for="new_password">Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </p>
                <p>
                    <label for="new_status">Status:</label>
                    <select id="new_status" name="new_status">
                        <option value="employee">Employee</option>
                        <option value="manager">Manager</option>
                        <option value="account disabled">Account Disabled</option>
                    </select>
                </p>
                <button type="submit" name="create_employee" value="1">Create Employee</button>
            </form>
            <?php
            // Process employee account update
            if (isset($_POST['edit_employee'])) {
                $edit_id = $_POST['edit_employee'];
                $stmt = $db->prepare("SELECT * FROM employees WHERE id = :id");
                $stmt->execute(['id' => $edit_id]);
                $emp_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($emp_to_edit) {
                    ?>
                    <h3>Edit Employee Account</h3>
                    <form method="POST">
                        <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($emp_to_edit['id']); ?>">
                        <p>
                            <label for="edit_username">Username:</label>
                            <input type="text" id="edit_username" name="edit_username" value="<?php echo htmlspecialchars($emp_to_edit['username']); ?>" required>
                        </p>
                        <p>
                            <label for="edit_full_name">Full Name:</label>
                            <input type="text" id="edit_full_name" name="edit_full_name" value="<?php echo htmlspecialchars($emp_to_edit['full_name']); ?>" required>
                        </p>
                        <p>
                            <label for="edit_password">New Password (leave blank to keep current):</label>
                            <input type="password" id="edit_password" name="edit_password">
                        </p>
                        <p>
                            <label for="edit_status">Status:</label>
                            <select id="edit_status" name="edit_status">
                                <option value="employee" <?php if ($emp_to_edit['status'] == 'employee') echo 'selected'; ?>>Employee</option>
                                <option value="manager" <?php if ($emp_to_edit['status'] == 'manager') echo 'selected'; ?>>Manager</option>
                                <option value="account disabled" <?php if ($emp_to_edit['status'] == 'account disabled') echo 'selected'; ?>>Account Disabled</option>
                            </select>
                        </p>
                        <button type="submit" name="update_employee" value="1">Update Employee</button>
                    </form>
                    <?php
                }
            }
            
            if (isset($_POST['update_employee'])) {
                $edit_id = $_POST['edit_id'];
                $edit_username = $_POST['edit_username'] ?? '';
                $edit_full_name = $_POST['edit_full_name'] ?? '';
                $edit_password = $_POST['edit_password'] ?? '';
                $edit_status = $_POST['edit_status'] ?? 'employee';
                
                if ($edit_username && $edit_full_name) {
                    if ($edit_password) {
                        $hashed_password = password_hash($edit_password, PASSWORD_BCRYPT);
                        $stmt = $db->prepare("UPDATE employees SET username = :username, full_name = :full_name, password = :password, status = :status WHERE id = :id");
                        $stmt->execute([
                            'username' => $edit_username,
                            'full_name' => $edit_full_name,
                            'password' => $hashed_password,
                            'status' => $edit_status,
                            'id' => $edit_id
                        ]);
                    } else {
                        $stmt = $db->prepare("UPDATE employees SET username = :username, full_name = :full_name, status = :status WHERE id = :id");
                        $stmt->execute([
                            'username' => $edit_username,
                            'full_name' => $edit_full_name,
                            'status' => $edit_status,
                            'id' => $edit_id
                        ]);
                    }
                    echo "<p style='color:green;'>Employee account updated successfully.</p>";
                } else {
                    echo "<p style='color:red;'>Username and Full Name are required.</p>";
                }
            }
        }
        ?>
    </section>
</body>
</html>

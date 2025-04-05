USE website;

INSERT INTO employees (
    username, full_name, password, status
) VALUES (
    'aglover02', 'Autumn Glover', '$2y$12$xc.rcUmEWn7RmHEhwya4H.f.BVuQ2Sg5bAa.Zem5EKRsT9heQHMUG', 'manager'
);

INSERT INTO customers (
    id, bill_fname, bill_lname, phone, email, address
) VALUES
    (1, 'Autumn', 'Glover', '2248303318', '', null),
    (2, 'beep', 'boop', '9999999999', 'autumn@gmail.com', null),
    (3, 'grace', 'james', '9996663333', 'grace@gmail.com', '123 orange ln'),
    (4, 'wee', 'woo', '1111111111', 'blee@bleee.com', 'laaa');

INSERT INTO orders (
    id, customer_id, order_date, total_price, status, comments
) VALUES
    (1, 1, '2025-03-18 00:22:09', 0, 'Pending', 'beep'),
    (2, 1, '2025-03-18 00:29:00', 36.3, 'Pending', 'sefawesfv'),
    (3, 2, '2025-03-18 14:37:02', 9.9, 'Pending', 'beepee'),
    (4, 1, '2025-03-18 15:35:09', 23.1, 'Pending', 'extra mushies'),
    (5, 1, '2025-03-18 15:47:45', 22, 'Pending', 'thanks'),
    (6, 3, '2025-03-18 16:09:18', 89.1, 'Pending', 'yipee'),
    (7, 4, '2025-03-18 16:11:12', 36.3, 'Pending', 'sdcnasvjsb');

INSERT INTO order_details (
    id, order_id, item_name, size, toppings, quantity, price_per_unit, topping_price
) VALUES
    (1, 2, 'Pizza', 'Small', 'Pepperoni, Mushrooms, Extra Cheese', 1, 11, 0),
    (2, 2, 'Pizza', 'Small', 'Pepperoni, Mushrooms, Extra Cheese', 1, 11, 0),
    (3, 2, 'Pizza', 'Small', 'Pepperoni, Mushrooms, Extra Cheese', 1, 11, 0),
    (4, 3, 'Pizza', 'Small', 'Pepperoni', 1, 9, 0),
    (5, 4, 'Pizza', 'Large', 'Mushrooms, Sausage, Extra Cheese', 1, 21, 0),
    (6, 5, 'Pizza', 'Small', 'Onions, Extra Cheese', 1, 10, 0),
    (7, 5, 'Pizza', 'Small', 'Onions, Extra Cheese', 1, 10, 0),
    (8, 6, 'Pizza', 'Large', 'Pepperoni, Mushrooms, Onions, Sausage, Bacon, Extra Cheese', 3, 27, 0),
    (9, 7, 'Pizza', 'Medium', 'Pepperoni, Onions, Bacon', 2, 16.5, 0);

<?php
require '../connection.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if ($order_id <= 0) {
    die('Invalid order ID');
}

// Retrieve order items
$sql_items = "SELECT oi.*, mi.name 
              FROM order_items oi 
              JOIN menu_items mi ON oi.menu_item_id = mi.id 
              WHERE oi.order_id = ?";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$order_id]);
$order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

// Retrieve all menu items
$sql_menu = "SELECT * FROM menu_items";
$stmt_menu = $pdo->prepare($sql_menu);
$stmt_menu->execute();
$menu_items = $stmt_menu->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update existing order items
    if (isset($_POST['items'])) {
        $updates = $_POST['items'];
        foreach ($updates as $item_id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity > 0) {
                $sql_update = "UPDATE order_items SET quantity = ? WHERE id = ? AND order_id = ?";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([$quantity, $item_id, $order_id]);
            }
        }
    }

    // Add new order items
    if (isset($_POST['new_items'])) {
        $new_items = $_POST['new_items'];
        foreach ($new_items as $menu_item_id => $quantity) {
            $quantity = intval($quantity);
            if ($quantity > 0) {
                $sql_insert = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) 
                               VALUES (?, ?, ?, (SELECT price FROM menu_items WHERE id = ?))";
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->execute([$order_id, $menu_item_id, $quantity, $menu_item_id]);
            }
        }
    }

    header("Location: pending_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
    <link rel="stylesheet" href="../style.css">
    <style>
       
        .tile {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
        }

        .tile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tile-header h2 {
            margin: 0;
            font-size: 1.5em;
        }

        .order-items, .menu-items {
            margin: 10px 0;
        }

        .order-item, .menu-item {
            margin: 10px 0;
        }

        .order-item label, .menu-item label {
            display: inline-block;
            width: 200px;
        }

        .order-item input, .menu-item input {
            padding: 5px;
            font-size: 1em;
            width: 60px;
            text-align: right;
        }

        .update-button, .add-button {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            background-color: #27ae60;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin-top: 20px;
        }

        .update-button:hover, .add-button:hover {
            background-color: #219150;
        }

        .back-to-menu {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            color: white;
            background-color: #3498db;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .back-to-menu:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="tile">
            <div class="tile-header">
                <h2>Update Order Items</h2>
            </div>
            <form method="post">
                <div class="order-items">
                    <h3>Current Order</h3>
                    <?php foreach ($order_items as $item): ?>
                        <div class="order-item">
                            <label for="item-<?= htmlspecialchars($item['id']) ?>"><?= htmlspecialchars($item['name']) ?></label>
                            <input type="number" name="items[<?= htmlspecialchars($item['id']) ?>]" id="item-<?= htmlspecialchars($item['id']) ?>" value="<?= htmlspecialchars($item['quantity']) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="menu-items">
                    <div class="menu-section">
                        <div class="menu">
                            <?php
                                $sql = "SELECT mi.*, c.name as category_name FROM menu_items mi
                                JOIN categories c ON mi.category_id = c.id
                                ORDER BY c.name";
                                $stmt = $pdo->query($sql);

                                $currentCategory = "";
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    if ($currentCategory != $row['category_name']) {
                                        if ($currentCategory != "") {
                                            echo "</div>";  // Close previous category div
                                            echo "</div>";  // Close previous category div
                                        }
                                        $currentCategory = $row['category_name'];
                                        echo "<h2>" . htmlspecialchars($currentCategory) . "</h2>";
                                        echo "<div class='category'>"; 
                                        echo "<div class='menu-container'>"; 
                                    }
                                
                                    echo "<div class='menu-item'>";
                                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                                    echo "<p>&#8369;" . htmlspecialchars($row['price']) . "</p>";
                                    echo "<form method='post' action='order.php'>";
                                    echo "<input type='hidden' name='item_id' value='" . htmlspecialchars($row['id']) . "'>";
                                    echo "<input type='number' name='item_quantity' value='1' min='1' class='quantity-input'>";
                                    echo "<button type='submit' name='add_to_cart' class='btn add-to-cart-btn'>Add to Cart</button>";
                                    echo "</form>";
                                    echo "</div>";
                                    
                                }
                                
                                if ($currentCategory != "") {
                                    echo "</div>";
                                    echo "</div>";  // Close last category div
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <button type="submit" class="update-button">Update Order</button>
            </form>
            <a href="pending_orders.php" class="back-to-menu">Back to Pending Orders</a>
        </div>
    </div>
</body>
</html>

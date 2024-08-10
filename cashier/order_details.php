<?php
require '../connection.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$error_message = isset($_GET['error_message']) ? $_GET['error_message'] : '';
if ($order_id <= 0) {
    die('Invalid order ID');
}

// Retrieve order details
$sql_order = "SELECT * FROM orders WHERE id = ?";
$stmt_order = $pdo->prepare($sql_order);
$stmt_order->execute([$order_id]);
$order = $stmt_order->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Order not found');
}

// Retrieve order items
$sql_items = "SELECT oi.*, mi.name 
              FROM order_items oi 
              JOIN menu_items mi ON oi.menu_item_id = mi.id 
              WHERE oi.order_id = ?";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$order_id]);
$order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total price
$total_price = 0;
foreach ($order_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Order Details</h1> 
    </header>       
    <div class="order-details-container">
        <div class="order-details">
            <div class="order-header">
                <p class="order-id">Order ID: FOS-<?= str_pad(htmlspecialchars($order['id']), 5, '0', STR_PAD_LEFT) ?></p>
            </div>
            <div class="order-items">
                <?php foreach ($order_items as $item): ?>
                    <div class="order-item">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p>Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                        <p>Price: &#8369;<?= htmlspecialchars($item['price']) ?></p>
                        <p>Total: &#8369;<?= number_format($item['price'] * $item['quantity'], 2) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="order-summary">
                <h3>Total Price: &#8369;<?= number_format($total_price, 2) ?></h3>
            </div>
            <div class="payment-section">
                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <form method="POST" action="payment_confirmation.php">
                    <input type="number" name="payment_amount" placeholder="Enter payment amount" step="0.01" required>
                    <input type="number" hidden name="order_id" value="<?php echo $order_id;?>">
                    <button type="submit">Pay Now</button><a href="pending_orders.php" class="back-to-menu">Back to Orders</a>
                </form>
            </div>
            
        </div>
    </div>
</body>
</html>

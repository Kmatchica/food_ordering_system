<?php
require '../connection.php';

// Fetch pending orders from the database
// $sql = "SELECT * FROM orders WHERE status = 'pending' ORDER BY order_date DESC";
$sql = "SELECT * FROM orders WHERE status = 'pending payment'  ORDER BY order_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
function generateOrderNumber($orderId) {
    $prefix = 'FOS-';
    // Pad the order ID with leading zeros to a total length of 5 digits
    $paddedNumber = str_pad($orderId, 5, '0', STR_PAD_LEFT);
    // Combine the prefix with the padded number
    $orderNumber = $prefix . $paddedNumber;
    return $orderNumber;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Orders</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Pending Orders(For Payment)</h1><a href="to_serve.php">Pending Orders</a>
    </header>
    <main class="pending-orders-container">
        <?php if ($pendingOrders): ?>
            <?php 
                foreach ($pendingOrders as $order): 
                $orderDate = new DateTime($order['order_date']);
                $formattedDate = $orderDate->format('F j, Y g:ia');
            ?>
                <div class="order-tile">
                    <h2>Order ID:  <?php   echo generateOrderNumber(htmlspecialchars($order['id'])); ?>
                    </h2>
                    <a href="order_details.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="view-details-btn">View Details</a>
                   
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($formattedDate); ?></p>
                    <p><strong>Ticket Number:</strong> <?php echo htmlspecialchars($order['ticket_number']); ?></p>
                    <p class="order-status"><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <div class="pending-order-buttons">
                        <a href="update_order.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="update-details-btn">Update Order Items</a>
                        <a href="cancel_order.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="cancel-details-btn">Cancel Order</a>
                
                    </div>
                        </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No pending orders.</p>
        <?php endif; ?>
    </main>
</body>
</html>

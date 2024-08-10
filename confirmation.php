<?php
require 'connection.php';

// Retrieve order details from the URL
$orderId = $_GET['order_id'] ?? null;
$ticketNumber = $_GET['ticket_number'] ?? null;

if ($orderId && $ticketNumber) {
    // Fetch order details
    $orderSql = "SELECT * FROM orders WHERE id = ? AND ticket_number = ?";
    $orderStmt = $pdo->prepare($orderSql);
    $orderStmt->execute([$orderId, $ticketNumber]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    // Fetch order items
    $itemsSql = "SELECT oi.*, mi.name 
                 FROM order_items oi 
                 JOIN menu_items mi ON oi.menu_item_id = mi.id 
                 WHERE oi.order_id = ?";
    $itemsStmt = $pdo->prepare($itemsSql);
    $itemsStmt->execute([$orderId]);
    $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Redirect if no order ID or ticket number is found
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Function to redirect after 5 seconds
        function redirectToOrderPage() {
            setTimeout(function() {
                window.location.href = 'order.php'; // Change this to your actual order page URL
            }, 10000); // 5000 milliseconds = 5 seconds
        }

        // Call the function when the page loads
        window.onload = redirectToOrderPage;
    </script>
</head>
<body>
    <header>
        <h1>Order Confirmation</h1>
    </header>
    <main class="">
        <?php if ($order): ?>
            <div class="confirmation-section">
                <h1>Thank you for your order!</h1>
                <p class="ticket">Your ticket number is: <strong><?php echo htmlspecialchars($order['ticket_number']); ?></strong></p>
                <p class="reminder">Please wait for the cashier to call your ticket number to proceed for payment</p>
                <a href="order.php" class="btn-confirmation">Back to Menu</a>
                <!-- <a href="print_ticket.php" class="btn-confirmation">Print Ticket</a> -->
            </div>
        <?php else: ?>
            <div class="error-message">
                <h2>Order not found</h2>
                <p>We could not find the order you were looking for. Please check the order ID and ticket number.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

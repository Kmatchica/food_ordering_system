<?php
require '../connection.php';

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_amount = isset($_POST['payment_amount']) ? floatval($_POST['payment_amount']) : 0.0;
    $order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0;

    if ($order_id <= 0 || $payment_amount <= 0) {
        die('Invalid order ID or payment amount');
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
    $total_price = 0.0;
    foreach ($order_items as $item) {
         $total_price += $item['price'] * $item['quantity'];
    }
    // Calculate change
    $change = $payment_amount - $total_price;


    if ($payment_amount >= $total_price) {
        // Update order status to paid
        $sql_update = "UPDATE orders SET total_amount = $total_price, status = 'pending' WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$order_id]);
        $_SESSION['payment_amount'] = $payment_amount;
        // Redirect to a confirmation page
        // header("Location: payment_confirmation.php?order_id=$order_id&payment_amount=$payment_amount");
    } else {
        
        $error_message = "Payment amount is insufficient.";
        header("Location: order_details.php?order_id=$order_id&payment_amount=$payment_amount&error_message=$error_message");
    }

}else{
    unset($_SESSION['payment_amount'] );
}

// $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
// $payment_amount = isset($_GET['payment_amount']) ? floatval($_GET['payment_amount']) : 0.0;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="../style.css">
    <style>

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .confirmation-details {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: left;
        }

        .confirmation-details h1 {
            margin: 0 0 20px;
            font-size: 1.5em;
        }

        .confirmation-details .detail {
            margin: 10px 0;
            font-size: 1.2em;
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
    
    <header>
        <h1>Payment Confirmation</h1>
    </header>
    <div class="container">
        <div class="confirmation-details">
            <h1>Payment Confirmation</h1>
            <div class="detail"><b>Order ID:</b> <?= str_pad(htmlspecialchars($order['id']), 5, '0', STR_PAD_LEFT) ?></div>
            <hr>
            <div class="detail"><b>Ticket Number:</b> <?= htmlspecialchars($order['ticket_number']) ?></div>
            <hr>
            <div class="detail"><b>Total Price:</b> &#8369;<?= number_format($total_price, 2) ?></div>
            <hr>
            <div class="detail"><b>Amount Paid:</b> &#8369;<?= number_format($payment_amount, 2) ?></div>
            <hr>
            <div class="detail"><b>Change:</b> &#8369;<?= number_format($change, 2) ?></div>
            <a href="pending_orders.php" class="back-to-menu">Back to Order list</a>
        </div>
        
    </div>
</body>
</html>

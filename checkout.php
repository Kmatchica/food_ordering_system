<?php
require 'connection.php';

// Function to generate the next ticket number
function getNextTicketNumber($pdo) {
    // Get today's date
    $today = date('Y-m-d');
    
    
    try {
        // Check if a ticket number entry exists for today
        $sql = "SELECT current_number FROM ticket_numbers WHERE date = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$today]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Entry exists, get the current number and increment it
            $currentNumber = $row['current_number'] + 1;
            $updateSql = "UPDATE ticket_numbers SET current_number = ? WHERE date = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$currentNumber, $today]);
        } else {
            // No entry for today, start with 00001
            $currentNumber = 1;
            $insertSql = "INSERT INTO ticket_numbers (date, current_number) VALUES (?, ?)";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([$today, $currentNumber]);
        }
        
        
        // Return the ticket number formatted with leading zeros
        return str_pad($currentNumber, 5, '0', STR_PAD_LEFT);
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
        return null;
    }
}

// Handle order confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_SESSION['cart'])) {
            
            // Step 1: Generate a ticket number
            echo $ticketNumber = getNextTicketNumber($pdo);
            if ($ticketNumber === null) {
                throw new Exception("Unable to generate ticket number.");
            }
            
            // Step 2: Insert a new record into the `orders` table with the ticket number
            $insertOrderSql = "INSERT INTO orders (order_date, ticket_number) VALUES (NOW(), ?)";
            $insertOrderStmt = $pdo->prepare($insertOrderSql);
            $insertOrderStmt->execute([$ticketNumber]);
            $orderId = $pdo->lastInsertId();  // Get the last inserted ID, which is the new order ID
            
            // Step 3: Insert each item into the `order_items` table
            $insertOrderItemSql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (:order_id, :menu_item_id, :quantity, :price)";
            $insertOrderItemStmt = $pdo->prepare($insertOrderItemSql);

            foreach ($_SESSION['cart'] as $itemId => $quantity) {
                // Get item details
                $itemSql = "SELECT * FROM menu_items WHERE id = ?";
                $itemStmt = $pdo->prepare($itemSql);
                $itemStmt->execute([$itemId]);
                $item = $itemStmt->fetch(PDO::FETCH_ASSOC);

                if ($item) {
                    $price = $item['price'];
                    $totalPrice = $price * $quantity;
                    $insertOrderItemStmt->bindParam(':order_id', $orderId);
                    $insertOrderItemStmt->bindParam(':menu_item_id', $itemId);
                    $insertOrderItemStmt->bindParam(':quantity', $quantity);
                    $insertOrderItemStmt->bindParam(':price', $totalPrice);
                    $insertOrderItemStmt->execute();
                }
            }
            
            
            // Clear the cart
            unset($_SESSION['cart']);
            
            // Redirect to the confirmation page with the order ID and ticket number
            header("Location: confirmation.php?order_id=" . urlencode($orderId) . "&ticket_number=" . urlencode($ticketNumber));
            exit();
    } else {
        echo "Your cart is empty.";
    }
}

// Retrieve cart items and calculate total price
$cartItems = [];
$totalPrice = 0;
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $sql = "SELECT * FROM menu_items WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_keys($_SESSION['cart']));
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cartItems as $item) {
        $quantity = $_SESSION['cart'][$item['id']];
        $totalPrice += $item['price'] * $quantity;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Checkout</h1>
    </header>
    <main class="container">
        <div class="checkout-section">
            <h2>Your Order</h2>
            <div class="cart">
                <?php
                if ($cartItems) {
                    foreach ($cartItems as $item) {
                        $quantity = $_SESSION['cart'][$item['id']];
                        echo "<div class='cart-item'>";
                        echo "<h2>" . htmlspecialchars($item['name']) . "</h2>";
                        echo "<p>Quantity: " . htmlspecialchars($quantity) . "</p>";
                        echo "<p>Price: &#8369;" . htmlspecialchars($item['price']) . "</p>";
                        echo "<p>Total: &#8369;" . number_format($item['price'] * $quantity, 2) . "</p>";
                        echo "</div>";
                    }
                    echo "<div class='cart-total'>";
                    echo "<h3>Total Price: &#8369;" . number_format($totalPrice, 2) . "</h3>";
                    echo "</div>";
                } else {
                    echo "<p>Your cart is empty.</p>";
                    echo "<p><a href='order.php' class='btn checkout-btn'>Back to Menu</a></p>";
                }
                ?>
            </div>
            <?php if (!empty($cartItems)) : ?>
                <form method="post" action="checkout.php">
                    <button type="submit" class="btn checkout-btn">Confirm Order</button>
                    <a href="order.php" class="btn checkout-btn">Back to Menu</a>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

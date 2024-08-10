<?php

require 'connection.php';

// Handle adding to cart
if (isset($_POST['add_to_cart'])) {
    $item_id = (int)$_POST['item_id'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]++;
    } else {
        $_SESSION['cart'][$item_id] = 1;
    }
    header("Location: cart.php");
    exit();
}

// Handle removing item from cart
if (isset($_POST['remove_item'])) {
    $item_id = (int)$_POST['item_id'];
    if (isset($_SESSION['cart'][$item_id])) {
        unset($_SESSION['cart'][$item_id]);
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
    }
    header("Location: cart.php");
    exit();
}

// Handle updating quantity
if (isset($_POST['increment'])) {
    $item_id = (int)$_POST['item_id'];
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]++;
    }    
    if (empty($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    header("Location: cart.php");
    exit();
} elseif (isset($_POST['decrement'])) {
    $item_id = (int)$_POST['item_id'];
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]--;
        if ($_SESSION['cart'][$item_id] <= 0) {
            unset($_SESSION['cart'][$item_id]);
        }
    }
        
    if (empty($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    header("Location: cart.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Your Cart</h1>
    </header>
    <main>
        <div class="cart">
            <?php
            if (!empty($_SESSION['cart'])) {
                $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
                $sql = "SELECT * FROM menu_items WHERE id IN ($placeholders)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_keys($_SESSION['cart']));
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($items as $item) {
                    $quantity = $_SESSION['cart'][$item['id']];
                    echo "<div class='cart-item'>";
                    echo "<h2>" . htmlspecialchars($item['name']) . "</h2>";
                    echo "<form method='post' action='cart.php' class='item-form'>";
                    echo "<input type='hidden' name='item_id' value='" . htmlspecialchars($item['id']) . "'>";
                    echo "<div class='quantity-controls'>";
                    echo "<button type='submit' name='decrement' value='-1' class='quantity-btn'>-</button>";
                    echo "<span class='quantity'>$quantity</span>";
                    echo "<button type='submit' name='increment' value='+1' class='quantity-btn'>+</button>";
                    echo "</div>";
                    echo "<button type='submit' name='remove_item' class='btn remove-btn'>Remove</button>";
                    echo "</form>";
                    echo "<p>Price: $" . htmlspecialchars($item['price']) . "</p>";
                    echo "<p>Total: $" . number_format($item['price'] * $quantity, 2) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>Your cart is empty.</p>";
            }
            ?>
            <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
            <a href="order.php" class="btn checkout-btn">Back to Menu</a>
        </div>
    </main>
</body>
</html>

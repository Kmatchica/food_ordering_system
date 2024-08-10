<?php
require 'connection.php';

// Handle adding to cart
if (isset($_POST['add_to_cart'])) {
    $item_id = (int)$_POST['item_id'];
    $item_quantity =  (int)$_POST['item_quantity'];
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] = $_SESSION['cart'][$item_id] + $item_quantity;
    } else {
        $_SESSION['cart'][$item_id] = $item_quantity;
    }
    header("Location: order.php");
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
    header("Location: order.php");
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
    header("Location: order.php");
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
    header("Location: order.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Menu</h1>
    </header>
    <div class="container">
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
        <div class="cart-section">
            <h2>Your Cart</h2>
            <div class="cart">
                <?php
                if (!empty($_SESSION['cart'])) {
                    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
                    $sql = "SELECT * FROM menu_items WHERE id IN ($placeholders)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array_keys($_SESSION['cart']));
                    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $totalPrice = 0; // Initialize total price
                    foreach ($items as $item) {
                        $quantity = $_SESSION['cart'][$item['id']];
                        $itemTotal = $item['price'] * $quantity;
                        $totalPrice += $itemTotal; // Accumulate total price

                        echo "<div class='cart-item'>";
                        echo "<h2>" . htmlspecialchars($item['name']) . "</h2>";
                        echo "<form method='post' action='order.php' class='item-form'>";
                        echo "<input type='hidden' name='item_id' value='" . htmlspecialchars($item['id']) . "'>";
                        echo "<div class='quantity-controls'>";
                        echo "<button type='submit' name='decrement' value='-1' class='quantity-btn'>-</button>";
                        echo "<span class='quantity'>$quantity</span>";
                        echo "<button type='submit' name='increment' value='+1' class='quantity-btn'>+</button>";
                        echo "<button type='submit' name='remove_item' class='btn remove-btn'>Remove</button>";
                        echo "</div>";
                        echo "</form>";
                        echo "<p class='price'>Price: &#8369;" . htmlspecialchars($item['price']) . "</p>";
                        echo "<p class='total'>Total: &#8369;" . number_format($itemTotal, 2) . "</p>";
                        echo "</div>";
                    }

                    echo "<div class='cart-total'>";
                    echo "<h3>Total Price: &#8369;" . number_format($totalPrice, 2) . "</h3>"; // Display total price
                    echo "</div>";
                } else {
                    echo "<p>Your cart is empty.</p>";
                }
                ?>
                <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
                <!-- <a href="order.php" class="btn checkout-btn">Back to Menu</a> -->
            </div>
        </div>
    </div>
</body>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const categories = document.querySelectorAll('.category');
    
    categories.forEach(category => {
      const menuContainer = category.querySelector('.menu-container');
      const items = menuContainer.querySelectorAll('.menu-item');
      
      if (items.length >= 4) {
        menuContainer.classList.add('scrollable');
      }
    });
  });
</script>
</html>

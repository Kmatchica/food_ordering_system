<?php
require '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    if ($order_id > 0) {
        $sql_cancel = "UPDATE orders SET status = 'canceled' WHERE id = ?";
        $stmt_cancel = $pdo->prepare($sql_cancel);
        $stmt_cancel->execute([$order_id]);

        // Optionally, you can delete the order items if you don't want to keep them
        // $sql_delete_items = "DELETE FROM order_items WHERE order_id = ?";
        // $stmt_delete_items = $pdo->prepare($sql_delete_items);
        // $stmt_delete_items->execute([$order_id]);
    }
}

header("Location: pending_order.php");
exit();
?>

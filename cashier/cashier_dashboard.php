<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Cashier Dashboard</h1>
        <a href="logout.php">Logout</a>
    </header>
    <div class="container">
        <div class="dashboard-card">
            <h2>Order Management</h2>
            <a href="view_orders.php">View Orders</a>
            <a href="order_status.php">Update Order Status</a>
        </div>
        <div class="dashboard-card">
            <h2>Reports</h2>
            <a href="daily_report.php">Daily Sales Report</a>
            <a href="monthly_report.php">Monthly Sales Report</a>
        </div>
        <div class="dashboard-card">
            <h2>User Settings</h2>
            <a href="change_password.php">Change Password</a>
            <a href="update_profile.php">Update Profile</a>
        </div>
    </div>
</body>
</html>

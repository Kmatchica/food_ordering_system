<?php
require '../connection.php'; // Adjust path to your database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']) ;

    // Prepare and execute the query
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        // Start a session and store user info
        $_SESSION['cashier_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: cashier_dashboard.php"); // Redirect to cashier dashboard
        exit();
    } else {
        // Invalid credentials
        header("Location: login.php?error=1");
        exit();
    }
}

<?php
session_start();
include 'db.php';

// Check if admin is actually logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['target_category'];
    $type = $_POST['adjustment_type'];
    $amount = (float)$_POST['amount'];

    // Define the Math Logic based on choice
    if ($type === 'percent') {
        // Multiplier logic: Price * 1.05 for 5%
        $math_expression = "price * (1 + (? / 100))";
    } else {
        // Addition logic: Price + 50 for 50 Birr
        $math_expression = "price + ?";
    }

    // Build the SQL based on Category
    if ($category === "all") {
        $sql = "UPDATE menu_items SET price = ROUND($math_expression, 2)";
        $params = [$amount];
    } else {
        $sql = "UPDATE menu_items SET price = ROUND($math_expression, 2) WHERE category = ?";
        $params = [$amount, $category];
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        header("Location: admin.php?bulk_success=1");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    exit();
}
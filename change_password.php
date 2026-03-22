<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];

    // 1. Check if the old password is correct
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && $old_pass === $user['password']) {
        // 2. Update to the new password
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        if ($update->execute([$new_pass])) {
            echo "<script>alert('Password updated successfully!'); window.location.href='admin.php';</script>";
        }
    } else {
        echo "<script>alert('Current password was incorrect!'); window.history.back();</script>";
    }
}
?>
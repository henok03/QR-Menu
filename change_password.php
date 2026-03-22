<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['admin_logged_in'])) {
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];

    // 1. Check current password in the 'users' table
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && $old_pass === $user['password']) {
        // 2. Update to the new password in 'users' table
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        if ($update->execute([$new_pass])) {
            echo "<script>alert('Password updated! Next time, login with your new password.'); window.location.href='admin.php';</script>";
        }
    } else {
        echo "<script>alert('Current password incorrect!'); window.history.back();</script>";
    }
}
?>
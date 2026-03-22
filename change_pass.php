<?php
session_start();
include 'db.php';

// Check if the user is logged in and the form was sent
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['admin_logged_in'])) {
    
    $new_pass = $_POST['new_pass'];

    if (!empty($new_pass)) {
        // Direct update - no 'old_pass' check required
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        
        if ($update->execute([$new_pass])) {
            echo "<script>
                    alert('Password updated successfully! Use your new password next time you log in.'); 
                    window.location.href='admin.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Database error: Could not update password.'); 
                    window.history.back();
                  </script>";
        }
    } else {
        echo "<script>alert('Please enter a password.'); window.history.back();</script>";
    }
} else {
    // If not logged in, kick them back to login page
    header("Location: login.php");
    exit();
}
?>
<?php
session_start();
include 'db.php'; 

if (isset($_POST['login'])) {
    $password = $_POST['password'];
    
    // Check the 'users' table instead of 'admin_users'
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = 'admin' AND password = ?");
    $stmt->execute([$password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Wrong Password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #121212;
            --card: #2c2a2a;
            --accent: #ff4757;
            --text: #ffffff;
        }

     body {
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: 'Inter', sans-serif;
    /* This creates a deep matte look with a subtle central glow */
    background-color: #0f1115;
    background-image: radial-gradient(circle at center, #1c1f26 0%, #0f1115 100%);
    background-attachment: fixed;
}

        .login-card {
            background: var(--card);
            padding: 50px 40px;
            border-radius: 20px;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
        }

        h2 {
            color: var(--text);
            margin-bottom: 8px;
            font-weight: 600;
        }

        p {
            color: #888;
            font-size: 14px;
            margin-bottom: 30px;
        }

        input {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            background: #2a2a2a;
            border: 1px solid #333;
            border-radius: 10px;
            color: white;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }

        input:focus {
            border-color: var(--accent);
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.9;
            transform: scale(0.98);
        }

        .error {
            color: var(--accent);
            font-size: 13px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Admin Login</h2>
        <p>Enter password to continue</p>

        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="password" name="password" placeholder="Password" required autofocus>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
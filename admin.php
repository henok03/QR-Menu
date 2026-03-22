<?php
session_start();
// Security check: Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Admin | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #ff4757;
            --secondary: #2f3542;
            --sidebar-bg: #1e2229;
            --content-bg: #f1f2f6;
            --white: #ffffff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }

        body { display: flex; min-height: 100vh; background: var(--content-bg); color: var(--secondary); }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px 0;
            transition: 0.3s;
        }

        .sidebar-header { text-align: center; padding: 20px; border-bottom: 1px solid #343a40; margin-bottom: 20px; }
        
        .nav-link {
            padding: 15px 25px;
            color: #a4b0be;
            text-decoration: none;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .nav-link i { margin-right: 15px; width: 20px; }
        .nav-link:hover, .nav-link.active { background: var(--primary); color: white; }

        /* Content Area */
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }

        .tab-content { display: none; animation: fadeIn 0.4s ease; }
        .tab-content.active { display: block; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Card Styling */
        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: 0 auto;
        }

        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i { position: absolute; left: 15px; top: 15px; color: var(--primary); }
        input, textarea { width: 100%; padding: 12px 15px 12px 45px; border: 2px solid #edeff2; border-radius: 10px; outline: none; }
        
        button {
            width: 100%; padding: 12px; background: var(--primary); color: white; 
            border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s;
        }
        button:hover { opacity: 0.9; }

        .logout-btn { margin-top: auto; color: #ff4757; }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; padding: 10px 0; }
            .nav-link { padding: 10px 15px; font-size: 14px; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-qrcode"></i> QR MENU</h3>
        </div>
        <div class="nav-link active" onclick="showTab('view-items')"><i class="fas fa-list"></i> View Menu</div>
        <div class="nav-link" onclick="showTab('add-item')"><i class="fas fa-plus-circle"></i> Add New Item</div>
        <div class="nav-link" onclick="showTab('settings')"><i class="fas fa-cog"></i> Settings</div>
        <a href="logout.php" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        
        <div id="view-items" class="tab-content active">
            <h2>Your Current Menu</h2>
            <p>Manage existing items here.</p>
            <br>
            <div class="card">
                <p style="text-align:center;">Menu list will appear here using PHP fetch.</p>
            </div>
        </div>

        <div id="add-item" class="tab-content">
            <div class="card">
                <h2>Add New Product</h2>
                <p>Fill in the details for the cafe menu.</p>
                <form action="insert.php" method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <i class="fas fa-hamburger"></i>
                        <input type="text" name="item_name" placeholder="Item Name" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-money-bill"></i>
                        <input type="number" name="item_price" placeholder="Price (ETB)" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-image"></i>
                        <input type="file" name="food_image" accept="image/*" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-pen"></i>
                        <textarea name="item_desc" placeholder="Description..."></textarea>
                    </div>
                    <button type="submit">Upload Product</button>
                </form>
            </div>
        </div>

        <div id="settings" class="tab-content">
            <div class="card">
                <h2>Account Settings</h2>
                <p>Change your admin security password.</p>
                <form action="change_password.php" method="POST">
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="old_pass" placeholder="Current Password" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-key"></i>
                        <input type="password" name="new_pass" placeholder="New Password" required>
                    </div>
                    <button type="submit" style="background: var(--secondary);">Update Password</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function showTab(tabId) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            // Remove active class from links
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabId).classList.add('active');
            // Set nav link to active
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
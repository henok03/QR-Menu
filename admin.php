<?php
session_start();
include 'db.php'; 

// Security check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Fetch stats
$totalItems = $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn() ?: 0;
$menuItems = $pdo->query("SELECT * FROM menu_items ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Admin | Responsive Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
     :root {
    --bg: #1c1e21;           /* True dark background */
    --sidebar-bg: #131416;   /* Dark navy/slate sidebar */
    --card-bg: #1c222d;      /* Slightly lighter slate for cards */
    --accent: #0099aa;       /* Indigo/Electric Blue for primary buttons */
    --text-primary: #f8fafc; /* Crisp white text */
    --text-secondary: #94a3b8;/* Muted slate-blue text */
    --border: #2e3748;       /* Darker border for separation */
    --header-h: 70px;
}

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }

        body { 
            background-color: var(--bg);
            color: var(--text-primary);
            overflow-x: hidden;
            display: flex;
        }

        /* MOBILE HEADER - Only shows on Small Screens */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--header-h);
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 20px;
            z-index: 1000;
            align-items: center;
            justify-content: space-between;
        }

        .menu-toggle { font-size: 24px; color: var(--accent); cursor: pointer; }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            height: 100vh;
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1100;
        }

        .sidebar-header { padding: 30px 20px; text-align: center; border-bottom: 1px solid var(--border); }
        .sidebar-header h3 { font-weight: 600; letter-spacing: -1px; }

        .nav-links { flex: 1; padding: 20px 0; }
        .nav-link {
            padding: 16px 25px;
            color: var(--text-secondary);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: 0.2s;
            cursor: pointer;
            border-left: 4px solid transparent;
            font-weight: 500;
        }
        .nav-link i { margin-right: 15px; width: 20px; font-size: 18px; }
        .nav-link.active, .nav-link:hover {
            color: var(--accent);
            background: rgba(255, 71, 87, 0.05);
            border-left-color: var(--accent);
        }

        .logout-btn { color: var(--accent); border-top: 1px solid var(--border); }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            padding: 40px;
            min-height: 100vh;
            max-width: 1200px;
            margin: 0 auto;
        }

        .tab-content { display: none; animation: slideIn 0.3s ease-out; }
        .tab-content.active { display: block; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }

        h2 { font-size: 28px; margin-bottom: 8px; }
        .subtitle { color: var(--text-secondary); font-size: 14px; margin-bottom: 35px; }

        /* DASHBOARD STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; }
        .stat-card {
            background: var(--card-bg); padding: 25px; border-radius: 20px;
            border: 1px solid var(--border); display: flex; align-items: center;
        }
        .stat-card i { background: rgba(255, 71, 87, 0.1); color: var(--accent); padding: 15px; border-radius: 15px; margin-right: 20px; font-size: 24px; }
        .stat-card h3 { font-size: 24px; margin-top: 5px; }
        .stat-card span { color: var(--text-secondary); font-size: 14px; }

        /* MENU GRID */
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 25px; }
        .menu-item { background: var(--card-bg); border-radius: 20px; overflow: hidden; border: 1px solid var(--border); }
        .item-img { width: 100%; height: 180px; object-fit: cover; }
        .item-info { padding: 20px; }
        .item-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .item-price { color: var(--accent); font-weight: 600; font-size: 18px; }
        .item-cat { font-size: 12px; color: #747d8c; background: #252a33; padding: 4px 10px; border-radius: 20px; }
        
        .action-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; }
        .btn { padding: 10px; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; text-align: center; font-size: 14px; }
        .btn-edit { background: #3b82f6; color: white; }
        .btn-delete { background: rgba(255, 71, 87, 0.1); color: var(--accent); border: 1px solid var(--accent); }

        /* FORM STYLING */
        .card { background: var(--sidebar-bg); padding: 30px; border-radius: 24px; border: 1px solid var(--border); max-width: 600px; }
        .input-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; color: var(--text-secondary); }
        input, select, textarea {
            width: 100%; padding: 14px; background: var(--bg); border: 1px solid var(--border);
            border-radius: 12px; color: white; outline: none; font-size: 16px;
        }
        input:focus { border-color: var(--accent); }
        .btn-submit { width: 100%; background: var(--accent); color: white; padding: 16px; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; }

        /* OVERLAY FOR MOBILE */
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1050; backdrop-filter: blur(4px); }

        /* MEDIA QUERIES - THE "FRIENDLY" PART */
        @media (max-width: 992px) {
            .mobile-header { display: flex; }
            .sidebar { position: fixed; left: -280px; top: 0; }
            .sidebar.active { left: 0; }
            .main-content { padding: 100px 20px 40px; width: 100%; }
            .overlay.active { display: block; }
        }

        @media (max-width: 480px) {
            .menu-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
            h2 { font-size: 24px; }
        }
    </style>
</head>
<body>

    <div class="mobile-header">
        <h3><i class="fas fa-qrcode"></i> QR MENU</h3>
        <i class="fas fa-bars menu-toggle" onclick="toggleSidebar()"></i>
    </div>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-qrcode"></i> QR MENU</h3>
        </div>
        <div class="nav-links">
            <div class="nav-link active" onclick="showTab('dashboard')"><i class="fas fa-chart-line"></i> Dashboard</div>
            <div class="nav-link" onclick="showTab('view-items')"><i class="fas fa-utensils"></i> Menu List</div>
            <div class="nav-link" onclick="showTab('add-item')"><i class="fas fa-plus-circle"></i> Add Item</div>
            <div class="nav-link" onclick="showTab('settings')"><i class="fas fa-user-shield"></i> Security</div>
        </div>
        <a href="login.php" class="nav-link logout-btn"><i class="fas fa-power-off"></i> Logout</a>
    </div>

    <div class="main-content">
        
        <div id="dashboard" class="tab-content active">
            <h2>Dashboard</h2>
            <p class="subtitle">Quick overview of your cafe performance.</p>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-box"></i>
                    <div><span>Total Items</span><h3><?php echo $totalItems; ?></h3></div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-layer-group"></i>
                    <div><span>Categories</span><h3>4</h3></div>
                </div>
            </div>
        </div>

  <div id="view-items" class="tab-content">
    <h2>Your Menu</h2>
    <p class="subtitle">Edit prices and manage your digital catalog.</p>

    <div class="search-container" style="margin-bottom: 25px;">
        <div style="position: relative; max-width: 400px;">
            <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-secondary);"></i>
            <input type="text" id="menuSearch" onkeyup="filterMenu()" placeholder="Search by name or category..." 
                   style="padding-left: 45px; width: 100%; background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; height: 45px;">
        </div>
    </div>
    
    <div class="menu-grid" id="menuGrid">
        <?php if(count($menuItems) > 0): ?>
            <?php foreach($menuItems as $item): ?>
            <div class="menu-item">
                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                     class="item-img" 
                     onerror="this.src='https://via.placeholder.com/300x200?text=Food+Image'">
                
                <div class="item-info">
                    <div class="item-top">
                        <strong class="item-name"><?php echo htmlspecialchars($item['name']); ?></strong>
                        
                        <span class="item-cat item-category" style="font-size: 12px; color: #747d8c; background: #252a33; padding: 4px 10px; border-radius: 20px;">
                            <?php echo htmlspecialchars($item['category'] ?? 'General'); ?>
                        </span>
                    </div>
                    
                    <span class="item-price"><?php echo $item['price']; ?> ETB</span>
                    
                    <div class="action-btns">
                        <button class="btn btn-edit" onclick="window.location.href='edit.php?id=<?php echo $item['id']; ?>'">Edit</button>
                        <button class="btn btn-delete" onclick="if(confirm('Delete?')) window.location.href='delete.php?id=<?php echo $item['id']; ?>'">Delete</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--text-secondary);">Your menu is currently empty.</p>
        <?php endif; ?>
    </div>
</div>
       <div id="add-item" class="tab-content">
    <div class="card">
        <h2>New Item</h2>
        <form action="insert.php" method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Item Name</label>
                <input type="text" name="item_name" placeholder="e.g. Cheese Burger" required>
            </div>
            
            <div class="input-group">
                <label>Category</label>
                <select name="category" required>
                    <option value="Burgers">Burgers</option>
                    <option value="Pizza">Pizza</option>
                    <option value="Cold Drinks">Cold Drinks</option>
                    <option value="Hot Drinks">Hot Drinks</option>
                    <option value="Desserts">Desserts</option>
                </select>
            </div>

            <div class="input-group">
                <label>Price (ETB)</label>
                <input type="number" name="item_price" placeholder="0.00" required>
            </div>

            <div class="input-group">
                <label>Description</label>
                <textarea name="item_desc" placeholder="What's inside? (e.g. Beef, Onion, Cheese)"></textarea>
            </div>

            <div class="input-group">
                <label>Image</label>
                <input type="file" name="food_image" accept="image/*" required>
            </div>

            <button type="submit" class="btn-submit">Save Product</button>
        </form>
    </div>
</div>
        <div id="settings" class="tab-content">
            <div class="card">
                <h2>Security</h2>
                <p class="subtitle">Update your admin login details.</p>
                <form action="change_pass.php" method="POST">
                    <div class="input-group"><label>New Password</label><input type="password" name="new_pass" required></div>
                    <button type="submit" class="btn-submit" style="background:#3b82f6">Update Security</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
        }

        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');

            // Auto-close sidebar on mobile after clicking
            if (window.innerWidth <= 992) toggleSidebar();
        }
        function filterMenu() {
    // Get the search text
    let input = document.getElementById('menuSearch');
    let filter = input.value.toLowerCase();
    
    // Get all menu item cards
    let menuGrid = document.getElementById('menuGrid');
    let items = menuGrid.getElementsByClassName('menu-item');

    // Loop through cards and hide those that don't match
    for (let i = 0; i < items.length; i++) {
        let name = items[i].querySelector('.item-name').innerText.toLowerCase();
        let category = items[i].querySelector('.item-category').innerText.toLowerCase();
        
        if (name.includes(filter) || category.includes(filter)) {
            items[i].style.display = ""; // Show
        } else {
            items[i].style.display = "none"; // Hide
        }
    }
}
    </script>
</body>
</html>
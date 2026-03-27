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
        /* Light Mode Variables */
:root {
    /* Premium Dark Mode (Deep Slate & Teal) */
    --bg: #0f172a;           /* Deep midnight blue */
    --sidebar-bg: #1e293b;   /* Slate blue sidebar */
    --card-bg: #1e293b;      /* Matching slate for cards */
    --accent: #2dd4bf;       /* Vibrant Teal - very modern */
    --accent-hover: #14b8a6; 
    --text-primary: #f1f5f9; /* Soft white (less eye strain) */
    --text-secondary: #94a3b8;/* Muted slate */
    --border: #334155;       /* Subtle border */
    --header-h: 70px;
}

body.light-mode {
    /* Clean Light Mode (Soft Gray & Indigo) */
    --bg: #f8fafc;           /* Very light blue-gray */
    --sidebar-bg: #ffffff;   /* Pure white sidebar */
    --card-bg: #ffffff;      /* White cards */
    --accent: #6366f1;       /* Indigo accent */
    --accent-hover: #4f46e5;
    --text-primary: #1b2233; /* Deep navy text */
    --text-secondary: #64748b;/* Muted gray-blue */
    --border: #e2e8f0;       /* Soft border */
}

/* Toggle Switch Styling */
.settings-controls {
    display: flex;
    gap: 10px;
    padding: 15px 25px;
    border-bottom: 1px solid var(--border);
}

.control-btn {
    background: var(--card-bg);
    border: 1px solid var(--border);
    color: var(--text-primary);
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: 0.3s;
}

.control-btn:hover {
    border-color: var(--accent);
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
        .item-img { width: 100%; height: 180px; object-fit: cover; background: #334155; }
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
/* Styling the actual input text (the "No file chosen" part) */
input[type="file"] {
    color: var(--text-primary); /* This ensures the 'No file chosen' matches your theme */
    cursor: pointer;
}

/* Styling the "Choose File" button part */
input[type="file"]::file-selector-button {
    background: var(--bg);
    color: var(--text-primary);
    border: 1px solid var(--border);
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    margin-right: 15px;
    transition: 0.3s;
}

input[type="file"]::file-selector-button:hover {
    background: var(--border);
}

/* Specific fix for Light Mode to ensure visibility */
body.light-mode input[type="file"] {
    color: #1e293b; /* Force a dark slate color in light mode */
}

body.light-mode input[type="file"]::file-selector-button {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #1e293b;
}
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
        /* Glassmorphism Card Container */
.bulk-adjuster-card {
    background: var(--card-bg);
    border-radius: 20px;
    padding: 25px;
    margin-top: 30px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow: hidden;
}

/* Red Accent Glow at the top */
.bulk-adjuster-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 4px;
    background: linear-gradient(90deg, transparent, var(--accent), transparent);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.icon-box {
    background: rgba(255, 71, 87, 0.1);
    color: var(--accent);
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.card-header h3 { margin: 0; font-size: 1.2rem; }
.card-header p { margin: 0; color: var(--text-secondary); font-size: 0.85rem; }

/* Responsive Grid System */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    align-items: flex-end;
}

/* Force the button to be wider on small screens */
@media (max-width: 600px) {
    .form-grid { grid-template-columns: 1fr; }
}

.form-field label {
    font-size: 0.8rem;
    color: var(--text-secondary);
    font-weight: 500;
    margin-bottom: 8px;
    display: block;
}

.form-field select, 
.form-field input {
    background: #31353e !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    transition: all 0.3s ease;
}

.form-field select:focus, 
.form-field input:focus {
    border-color: var(--accent) !important;
    box-shadow: 0 0 10px rgba(255, 71, 87, 0.1);
}

/* Premium Button Styling */
.apply-btn {
    background: var(--accent);
    color: white;
    border: none;
    padding: 14px 25px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.apply-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(255, 71, 87, 0.3);
    filter: brightness(1.1);
}

.apply-btn:active { transform: translateY(0); }
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
        <div class="settings-controls">
    <button class="control-btn" onclick="toggleTheme()" id="themeBtn">
        <i class="fas fa-sun"></i>
    </button>
    <button class="control-btn" onclick="toggleLang()" id="langBtn">
        EN / አማ
    </button>
</div>
        <div class="nav-links">
    <div class="nav-link active" onclick="showTab('dashboard')">
        <i class="fas fa-chart-line"></i> 
        <span data-lang="nav_dashboard">Dashboard</span>
    </div>
    <div class="nav-link" onclick="showTab('view-items')">
        <i class="fas fa-utensils"></i> 
        <span data-lang="nav_menu">Menu List</span>
    </div>
    <div class="nav-link" onclick="showTab('add-item')">
        <i class="fas fa-plus-circle"></i> 
        <span data-lang="nav_add">Add Item</span>
    </div>
    <div class="nav-link" onclick="showTab('settings')">
        <i class="fas fa-user-shield"></i> 
        <span data-lang="nav_security">Security</span>
    </div>
</div>
        <a href="login.php" class="nav-link logout-btn"><i class="fas fa-power-off"></i> Logout</a>
    </div>

   <div class="main-content">
    <div id="dashboard" class="tab-content active">
        <h2 data-lang="dash_title">Dashboard</h2>
        <p class="subtitle" data-lang="dash_subtitle">Quick overview of your cafe performance.</p>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <div><span data-lang="total_items">Total Items</span><h3><?php echo $totalItems; ?></h3></div>
            </div>
            <div class="stat-card">
                <i class="fas fa-layer-group"></i>
                <div><span data-lang="categories">Categories</span><h3>4</h3></div>

            </div>
            
        </div>
    <div class="bulk-adjuster-card">
    <div class="card-header">
        <div class="icon-box"><i class="fas fa-coins"></i></div>
        <div>
            <h3 data-lang="bulk_title">Bulk Price Adjuster</h3>
            <p data-lang="bulk_sub">Update menu prices instantly across categories</p>
        </div>
    </div>

    <form action="bulk_action.php" method="POST" class="bulk-form">
        <div class="form-grid">
            <div class="form-field">
                <label><i class="fas fa-tags"></i> <span data-lang="lbl_sel_cat">Select Category</span></label>
                <select name="target_category">
                    <option value="all" data-lang="opt_all">Entire Menu</option>
                    <option value="Burgers">Burgers</option>
                    <option value="Pizza">Pizza</option>
                    <option value="Drinks">Drinks</option>
                    <option value="Hot Drinks">Hot Drinks</option>
                </select>
            </div>

            <div class="form-field">
                <label><i class="fas fa-sliders-h"></i> <span data-lang="lbl_adj_type">Adjustment Type</span></label>
                <select name="adjustment_type">
                    <option value="flat" data-lang="opt_flat">Flat Birr (ETB)</option>
                    <option value="percent" data-lang="opt_percent">Percentage (%)</option>
                </select>
            </div>

            <div class="form-field">
                <label><i class="fas fa-plus"></i> <span data-lang="lbl_amt_add">Amount to Add</span></label>
                <input type="number" name="amount" step="0.01" placeholder="50.00" required>
            </div>

            <div class="form-field action-button-container">
                <button type="submit" class="apply-btn">
                    <i class="fas fa-check-circle"></i> <span data-lang="btn_apply">Apply Update</span>
                </button>
            </div>
        </div>
    </form>

</div>
    </div>


    <div id="view-items" class="tab-content">
        <h2 data-lang="menu_title">Your Menu</h2>
        <p class="subtitle" data-lang="menu_subtitle">Edit prices and manage your digital catalog.</p>

        <div class="search-container" style="margin-bottom: 25px;">
            <div style="position: relative; max-width: 400px;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-secondary);"></i>
                <input type="text" id="menuSearch" onkeyup="filterMenu()" placeholder="Search..." 
                       style="padding-left: 45px; width: 100%; background: var(--card-bg); border: 1px solid var(--border); border-radius: 12px; height: 45px;">
            </div>
        </div>
        
        <div class="menu-grid" id="menuGrid">
            <?php if(count($menuItems) > 0): ?>
                <?php foreach($menuItems as $item): ?>
                <div class="menu-item">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" class="item-img" onerror="this.src='https://via.placeholder.com/300x200?text=Food+Image'">
                    <div class="item-info">
                        <div class="item-top">
                            <strong class="item-name"><?php echo htmlspecialchars($item['name']); ?></strong>
                            <span class="item-cat item-category"><?php echo htmlspecialchars($item['category'] ?? 'General'); ?></span>
                        </div>
                        <span class="item-price"><?php echo $item['price']; ?> ETB</span>
                        <div class="action-btns">
                            <button class="btn btn-edit" onclick="window.location.href='edit.php?id=<?php echo $item['id']; ?>'" data-lang="btn_edit">Edit</button>
                            <button class="btn btn-delete" onclick="if(confirm('Delete?')) window.location.href='delete.php?id=<?php echo $item['id']; ?>'" data-lang="btn_delete">Delete</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-secondary);" data-lang="empty_menu">Your menu is currently empty.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="add-item" class="tab-content">
        <div class="card">
            <h2 data-lang="add_title">New Item</h2>
            <form action="insert.php" method="POST" enctype="multipart/form-data">
                <div class="input-group">
                    <label data-lang="lbl_item_name">Item Name</label>
                    <input type="text" name="item_name" required>
                </div>
                <div class="input-group">
                    <label data-lang="lbl_category">Category</label>
                    <select name="category" required>
                        <option value="Burgers">Burgers</option>
                        <option value="Pizza">Pizza</option>
                        <option value="Cold Drinks">Cold Drinks</option>
                        <option value="Hot Drinks">Hot Drinks</option>
                        <option value="Desserts">Desserts</option>
                    </select>
                </div>
                <div class="input-group">
                    <label data-lang="lbl_price">Price (ETB)</label>
                    <input type="number" name="item_price" required>
                </div>
                <div class="input-group">
                    <label data-lang="lbl_desc">Description</label>
                    <textarea name="item_desc"></textarea>
                </div>
                <div class="input-group">
                    <label data-lang="lbl_image">Image</label>
                    <input type="file" name="food_image" accept="image/*" required>
                </div>
                <button type="submit" class="btn-submit" data-lang="btn_save">Save Product</button>
            </form>
        </div>
    </div>

    <div id="settings" class="tab-content">
        <div class="card">
            <h2 data-lang="sec_title">Security</h2>
            <p class="subtitle" data-lang="sec_subtitle">Enter a new password to update your login.</p>
            <form action="change_pass.php" method="POST">
                <div class="input-group">
                    <label data-lang="lbl_new_pass">New Password</label>
                    <input type="password" name="new_pass" required>
                </div>
                <button type="submit" class="btn-submit" style="background:#3b82f6" data-lang="btn_update_pass">Update Password Now</button>
            </form>
        </div>
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
// 1. LANGUAGE DATA


// 1. Sidebar Toggle for Mobile
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
}

// 2. Tab Switching Logic
function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    
    document.getElementById(tabId).classList.add('active');
    // Find the link that was clicked and make it active
    const clickedLink = Array.from(document.querySelectorAll('.nav-link'))
                             .find(link => link.getAttribute('onclick')?.includes(tabId));
    if(clickedLink) clickedLink.classList.add('active');

    if (window.innerWidth <= 992) toggleSidebar();
}

// 3. Search/Filter Logic
function filterMenu() {
    let input = document.getElementById('menuSearch');
    let filter = input.value.toLowerCase();
    let items = document.getElementsByClassName('menu-item');

    for (let i = 0; i < items.length; i++) {
        let name = items[i].querySelector('.item-name').innerText.toLowerCase();
        let category = items[i].querySelector('.item-category').innerText.toLowerCase();
        items[i].style.display = (name.includes(filter) || category.includes(filter)) ? "" : "none";
    }
}

// --- PREFERENCES LOGIC (Theme & Language) ---

// Initialize Variables from LocalStorage


// 1. SETTINGS & TRANSLATIONS
let isDark = localStorage.getItem('theme') !== 'light';
let currentLang = localStorage.getItem('lang') || 'en';

const translations = {
    'en': {
        'nav_dashboard': 'Dashboard', 'nav_menu': 'Menu List', 'nav_add': 'Add Item', 'nav_security': 'Security',
        'dash_title': 'Dashboard', 'dash_subtitle': 'Quick overview of your cafe performance.',
        'total_items': 'Total Items', 'categories': 'Categories',
        'bulk_title': 'Bulk Price Adjuster', 'bulk_sub': 'Update menu prices instantly across categories',
        'lbl_sel_cat': 'Select Category', 'lbl_adj_type': 'Adjustment Type', 'lbl_amt_add': 'Amount to Add',
        'btn_apply': 'Apply Update', 'opt_all': 'Entire Menu', 'opt_flat': 'Flat Birr (ETB)', 'opt_percent': 'Percentage (%)',
        'menu_title': 'Your Menu', 'menu_subtitle': 'Edit prices and manage your digital catalog.',
        'btn_edit': 'Edit', 'btn_delete': 'Delete', 'empty_menu': 'Your menu is currently empty.',
        'add_title': 'New Item', 'lbl_item_name': 'Item Name', 'lbl_category': 'Category',
        'lbl_price': 'Price (ETB)', 'lbl_desc': 'Description', 'lbl_image': 'Image', 'btn_save': 'Save Product',
        'sec_title': 'Security', 'sec_subtitle': 'Enter a new password to update your login.',
        'lbl_new_pass': 'New Password', 'btn_update_pass': 'Update Password Now'
    },
    'am': {
        'nav_dashboard': 'ዳሽቦርድ', 'nav_menu': 'የምግብ ዝርዝር', 'nav_add': 'አዲስ ጨምር', 'nav_security': 'ደህንነት',
        'dash_title': 'ዳሽቦርድ', 'dash_subtitle': 'የካፌዎ አፈጻጸም አጭር መግለጫ።',
        'total_items': 'ጠቅላላ እቃዎች', 'categories': 'ምድቦች',
        'bulk_title': 'የዋጋ ማስተካከያ', 'bulk_sub': 'የምግቦችን ዋጋ በጅምላ ለመጨመር ወይም ለመቀነስ',
        'lbl_sel_cat': 'ምድብ ይምረጡ', 'lbl_adj_type': 'የማስተካከያ ዓይነት', 'lbl_amt_add': 'የሚጨመረው መጠን',
        'btn_apply': 'ዋጋውን አዘምን', 'opt_all': 'ሁሉንም ምግብ', 'opt_flat': 'መደበኛ ብር (ETB)', 'opt_percent': 'ፐርሰንት (%)',
        'menu_title': 'የእርስዎ ሜኑ', 'menu_subtitle': 'ዋጋዎችን ያስተካክሉ እና ዲጂታል ካታሎግዎን ያስዳድሩ።',
        'btn_edit': 'አስተካክል', 'btn_delete': 'ሰርዝ', 'empty_menu': 'የእርስዎ ሜኑ በአሁኑ ጊዜ ባዶ ነው።',
        'add_title': 'አዲስ እቃ', 'lbl_item_name': 'የእቃው ስም', 'lbl_category': 'ምድብ',
        'lbl_price': 'ዋጋ (ብር)', 'lbl_desc': 'መግለጫ', 'lbl_image': 'ምስል', 'btn_save': 'ምርቱን አስቀምጥ',
        'sec_title': 'ደህንነት', 'sec_subtitle': 'መግቢያዎን ለማዘመን አዲስ የይለፍ ቃል ያስገቡ።',
        'lbl_new_pass': 'አዲስ የይለፍ ቃል', 'btn_update_pass': 'የይለፍ ቃል አሁን ከይር'
    }
};

// 2. UI FUNCTIONS
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
}

function showTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    
    document.getElementById(tabId).classList.add('active');
    
    // Highlight the correct nav link
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => {
        if(link.getAttribute('onclick') && link.getAttribute('onclick').includes(tabId)) {
            link.classList.add('active');
        }
    });

    if (window.innerWidth <= 992) toggleSidebar();
}

function filterMenu() {
    let input = document.getElementById('menuSearch');
    let filter = input.value.toLowerCase();
    let items = document.getElementsByClassName('menu-item');

    for (let i = 0; i < items.length; i++) {
        let name = items[i].querySelector('.item-name').innerText.toLowerCase();
        let category = items[i].querySelector('.item-category').innerText.toLowerCase();
        items[i].style.display = (name.includes(filter) || category.includes(filter)) ? "" : "none";
    }
}

// 3. THEME & LANGUAGE LOGIC
function toggleTheme() {
    isDark = !isDark;
    document.body.classList.toggle('light-mode', !isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    document.getElementById('themeBtn').innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
}

function toggleLang() {
    currentLang = (currentLang === 'en') ? 'am' : 'en';
    localStorage.setItem('lang', currentLang);
    applyLanguage();
}

function applyLanguage() {
    document.querySelectorAll('[data-lang]').forEach(el => {
        const key = el.getAttribute('data-lang');
        if (translations[currentLang] && translations[currentLang][key]) {
            el.innerText = translations[currentLang][key];
        }
    });

    const searchInput = document.getElementById('menuSearch');
    if(searchInput) {
        searchInput.placeholder = currentLang === 'en' ? "Search..." : "ፈልግ...";
    }

    document.getElementById('langBtn').innerText = currentLang === 'en' ? "EN / አማ" : "አማ / EN";
    // Using Nyala or Abyssinica SIL for Amharic readability
    document.body.style.fontFamily = (currentLang === 'am') ? "'Nyala', 'Abyssinica SIL', sans-serif" : "'Inter', sans-serif";
}

// 4. INITIALIZE
window.onload = () => {
    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light-mode');
        isDark = false;
    }
    document.getElementById('themeBtn').innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    applyLanguage();
};
</script>
   
</body>
</html>
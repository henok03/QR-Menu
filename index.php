<?php
include 'db.php';
// Fetch all items from your database
$stmt = $pdo->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY id DESC");
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>QR Menu</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Caveat:wght@700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    

    
    <style>
        body { transition: background-color 0.3s, color 0.3s; }


        
        .food-card { 
            transition: transform 0.2s, box-shadow 0.2s; 
            animation: fade-in-up 0.4s ease-out both;
        }
        .food-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        
        /* Fixed hidden class with animation */
        .hidden-item { 
            display: none !important; 
            animation: none !important;
        }
        
        /* Category button animation */
        .category-btn {
            transition: all 0.2s ease;
            animation: slide-in 0.3s ease-out both;
        }
        .category-btn:hover {
            transform: scale(1.05);
        }
        
        /* Search bar animation */
        #searchContainer {
            transition: all 0.3s ease;
        }
        
        /* Image loading animation */
        .item-img {
            transition: transform 0.3s ease;
        }
        .food-card:hover .item-img {
            transform: scale(1.03);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }

        /* ===== HERO STYLES ===== */
        .hero-section {
            display: flex;
            min-height: calc(100vh - 64px);
            background: linear-gradient(135deg, #fafaf8 0%, #f0f7f0 50%, #e8f5e8 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-left {
            flex: 1;
            padding: 60px 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 2;
        }
        .hero-right {
            flex: 1;
            position: relative;
            min-height: 700px;
        }
        .hero-tagline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(0,178,7,0.08);
            border-radius: 30px;
            font-size: 0.9rem;
            color: #008000;
            font-weight: 500;
            margin-bottom: 24px;
            width: fit-content;
            animation: fadeInUp 0.6s ease both;
        }
        .hero-tagline svg { width: 16px; height: 16px; color: #00b207; }
        .hero-tagline .scan-word { color: #00b207; font-weight: 700; }
        .hero-title-main {
            font-size: 3.8rem;
            font-weight: 800;
            line-height: 1.15;
            color: #1a1a2e;
            margin-bottom: 20px;
            animation: fadeInUp 0.6s ease 0.1s both;
        }
        .hero-title-main .instantly {
            font-family: 'Caveat', cursive;
            color: #00b207;
            font-size: 4.5rem;
            position: relative;
            display: inline-block;
        }
        .hero-title-main .instantly::after {
            content: '';
            position: absolute;
            bottom: 2px; left: 0; right: 0;
            height: 4px;
            background: #00b207;
            border-radius: 2px;
            opacity: 0.4;
        }
        .hero-subtitle-text {
            font-size: 1.05rem;
            color: #666;
            line-height: 1.7;
            max-width: 420px;
            margin-bottom: 32px;
            animation: fadeInUp 0.6s ease 0.2s both;
        }
        .hero-btns {
            display: flex;
            gap: 16px;
            margin-bottom: 32px;
            flex-wrap: wrap;
            animation: fadeInUp 0.6s ease 0.3s both;
        }
        .hero-btn-primary {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 28px;
            background: #00b207; color: white;
            border: none; border-radius: 12px;
            font-size: 1rem; font-weight: 600; cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,178,7,0.3);
        }
        .hero-btn-primary:hover { background: #009906; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,178,7,0.4); }
        .hero-btn-primary svg { width: 20px; height: 20px; }
        .hero-btn-secondary {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 28px;
            background: white; color: #333;
            border: 1px solid #e0e0e0; border-radius: 12px;
            font-size: 1rem; font-weight: 600; cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .hero-btn-secondary:hover { border-color: #00b207; color: #00b207; transform: translateY(-2px); }
        .hero-btn-secondary svg { width: 20px; height: 20px; }
        .hero-customers {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 32px;
            animation: fadeInUp 0.6s ease 0.4s both;
        }
        .hero-avatars { display: flex; }
        .hero-avatars img {
            width: 40px; height: 40px;
            border-radius: 50%; border: 3px solid white;
            margin-left: -10px; object-fit: cover;
        }
        .hero-avatars img:first-child { margin-left: 0; }
        .hero-cat-pills {
            display: flex; gap: 8px; flex-wrap: wrap;
            margin-bottom: 32px;
            animation: fadeInUp 0.6s ease 0.5s both;
        }
        .hero-cat-pill {
            display: flex; flex-direction: column; align-items: center; gap: 6px;
            padding: 12px 16px; background: white;
            border-radius: 12px; border: 1px solid #f0f0f0;
            cursor: pointer; transition: all 0.3s; min-width: 80px;
        }
        .hero-cat-pill:hover { border-color: #00b207; box-shadow: 0 4px 12px rgba(0,178,7,0.15); transform: translateY(-2px); }
        .hero-cat-pill svg { width: 24px; height: 24px; color: #00b207; }
        .hero-cat-pill span { font-size: 0.75rem; font-weight: 600; color: #555; white-space: nowrap; }
        .hero-stats-bar {
            display: flex; gap: 24px;
            padding: 20px 28px;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.05);
            animation: fadeInUp 0.6s ease 0.6s both;
        }
        .hero-stat-item {
            display: flex; align-items: center; gap: 12px;
            padding-right: 24px;
            border-right: 1px solid #e0e0e0;
        }
        .hero-stat-item:last-child { border-right: none; padding-right: 0; }
        .hero-stat-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }
        .hero-stat-icon svg { width: 22px; height: 22px; }
        .hero-stat-icon.green { background: rgba(0,178,7,0.1); }
        .hero-stat-icon.green svg { color: #00b207; }
        .hero-stat-icon.orange { background: rgba(255,152,0,0.1); }
        .hero-stat-icon.orange svg { color: #ff9800; }
        .hero-stat-icon.blue { background: rgba(33,150,243,0.1); }
        .hero-stat-icon.blue svg { color: #2196f3; }
        .hero-stat-icon.purple { background: rgba(156,39,176,0.1); }
        .hero-stat-icon.purple svg { color: #9c27b0; }
        .hero-stat-num { font-size: 1.2rem; font-weight: 800; color: #1a1a2e; line-height: 1.2; }
        .hero-stat-lbl { font-size: 0.7rem; color: #888; font-weight: 500; }
        /* Hero right */
        .hero-right-bg {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, #0a3d0a 0%, #0d5c0d 30%, #004d00 100%);
            clip-path: polygon(15% 0, 100% 0, 100% 100%, 0% 100%);
        }
        .hero-right-content { position: relative; z-index: 2; width: 100%; height: 100%; }
        .hero-qr-card {
            position: absolute; left: 80px; top: 50%;
            transform: translateY(-50%);
            background: white; border-radius: 20px;
            padding: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center; z-index: 10;
            animation: heroFloatUp 3s ease-in-out infinite;
        }
        .hero-qr-card img { width: 180px; height: 180px; border-radius: 12px; margin-bottom: 12px; }
        .hero-qr-card p { font-family: 'Caveat', cursive; font-size: 1.3rem; color: #333; }
        .hero-qr-card p span { color: #00b207; text-decoration: underline; text-decoration-color: #00b207; }
        .hero-phone-mockup {
            position: absolute; right: 120px; top: 50%;
            transform: translateY(-50%);
            width: 280px; height: 570px;
            background: #1a1a1a; border-radius: 40px; padding: 12px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
            z-index: 10;
            animation: heroFloatUp 3s ease-in-out infinite 0.5s;
        }
        .hero-phone-screen { width: 100%; height: 100%; background: white; border-radius: 30px; overflow: hidden; position: relative; }
        .hero-phone-notch { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 120px; height: 28px; background: #1a1a1a; border-radius: 0 0 16px 16px; z-index: 5; }
        .hero-phone-header { padding: 40px 20px 16px; background: linear-gradient(135deg, #00b207, #009906); }
        .hero-phone-logo { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .hero-phone-logo-icon { width: 24px; height: 24px; background: white; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        .hero-phone-logo-text { color: white; font-weight: 700; font-size: 0.85rem; }
        .hero-phone-welcome h3 { color: white; font-size: 1.1rem; font-weight: 700; }
        .hero-phone-welcome p { color: white; font-size: 0.7rem; opacity: 0.9; margin-top: 2px; }
        .hero-phone-search { margin: 12px 20px; padding: 10px 14px; background: #f5f5f5; border-radius: 10px; font-size: 0.7rem; color: #999; display: flex; align-items: center; gap: 8px; }
        .hero-phone-search svg { width: 14px; height: 14px; color: #999; }
        .hero-phone-popular { padding: 12px 20px 0; }
        .hero-phone-popular h4 { font-size: 0.8rem; font-weight: 700; color: #333; margin-bottom: 10px; }
        .hero-phone-dish { display: flex; align-items: center; gap: 10px; padding: 8px; background: #f9f9f9; border-radius: 12px; margin-bottom: 8px; }
        .hero-phone-dish-img { width: 52px; height: 52px; border-radius: 10px; object-fit: cover; }
        .hero-phone-dish-info { flex: 1; }
        .hero-phone-dish-name { font-size: 0.7rem; font-weight: 600; color: #333; }
        .hero-phone-dish-rating { display: flex; align-items: center; gap: 2px; font-size: 0.6rem; color: #666; margin-top: 2px; }
        .hero-phone-dish-rating svg { width: 10px; height: 10px; color: #ffb800; }
        .hero-phone-dish-price { font-size: 0.75rem; font-weight: 700; color: #333; margin-top: 2px; }
        .hero-phone-add-btn { width: 26px; height: 26px; background: #00b207; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem; font-weight: 700; }
        .hero-phone-nav { position: absolute; bottom: 0; left: 0; right: 0; display: flex; justify-content: space-around; padding: 10px 0; background: white; border-top: 1px solid #f0f0f0; }
        .hero-phone-nav-item { display: flex; flex-direction: column; align-items: center; gap: 2px; font-size: 0.55rem; color: #999; }
        .hero-phone-nav-item.active { color: #00b207; }
        .hero-phone-nav-item svg { width: 18px; height: 18px; }
        .hero-food-burger { position: absolute; top: 40px; right: 40px; width: 180px; height: 180px; border-radius: 50%; object-fit: cover; box-shadow: 0 10px 40px rgba(0,0,0,0.3); animation: heroFloatUp 4s ease-in-out infinite 0.2s; z-index: 10; }
        .hero-food-pizza { position: absolute; bottom: 60px; right: 20px; width: 220px; border-radius: 20px; object-fit: cover; box-shadow: 0 10px 40px rgba(0,0,0,0.3); animation: heroFloatUp 4s ease-in-out infinite 0.8s; z-index: 10; }
        .hero-food-salad { position: absolute; bottom: 20px; left: 40px; width: 140px; height: 140px; border-radius: 50%; object-fit: cover; box-shadow: 0 10px 40px rgba(0,0,0,0.3); animation: heroFloatUp 4s ease-in-out infinite 1.2s; z-index: 10; }
        .hero-leaf { position: absolute; z-index: 5; opacity: 0.7; }
        .hero-leaf svg { fill: #4caf50; }
        .hl-1 { top: 100px; right: 200px; animation: heroLeaf 6s ease-in-out infinite; }
        .hl-2 { top: 250px; left: 600px; animation: heroLeaf 5s ease-in-out infinite 1s; }
        .hl-3 { bottom: 200px; left: 700px; animation: heroLeaf 7s ease-in-out infinite 0.5s; }
        .hl-4 { top: 180px; right: 400px; animation: heroLeaf 4s ease-in-out infinite 1.5s; }
        .hl-5 { bottom: 150px; right: 350px; animation: heroLeaf 5s ease-in-out infinite 0.8s; }
        .hl-6 { top: 350px; right: 150px; animation: heroLeaf 6s ease-in-out infinite 1.2s; }
        .hero-arrow-svg { position: absolute; left: 550px; top: 50%; transform: translateY(-50%); z-index: 8; opacity: 0.4; }
        @keyframes heroFloatUp {
            0%, 100% { transform: translateY(-50%); }
            50% { transform: translateY(calc(-50% - 10px)); }
        }
        @keyframes heroLeaf {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(10deg); }
        }
        /* Hero responsive */
        @media (max-width: 1200px) {
            .hero-left { padding: 60px 30px 40px; }
            .hero-title-main { font-size: 3rem; }
            .hero-title-main .instantly { font-size: 3.5rem; }
            .hero-qr-card { left: 40px; }
            .hero-phone-mockup { right: 80px; }
        }
        @media (max-width: 900px) {
            .hero-section { flex-direction: column; }
            .hero-left { padding: 40px 24px 40px; }
            .hero-right { min-height: 600px; }
            .hero-right-bg { clip-path: none; }
            .hero-title-main { font-size: 2.4rem; }
            .hero-title-main .instantly { font-size: 3rem; }
            .hero-stats-bar { flex-wrap: wrap; gap: 16px; }
            .hero-stat-item { border-right: none; }
            .hero-qr-card { left: 20px; padding: 16px; }
            .hero-qr-card img { width: 120px; height: 120px; }
            .hero-phone-mockup { right: 20px; width: 220px; height: 440px; }
            .hero-food-burger { width: 120px; height: 120px; }
            .hero-food-pizza { width: 160px; }
            .hero-food-salad { width: 100px; height: 100px; }
            .hero-cat-pills { gap: 6px; }
            .hero-cat-pill { min-width: 65px; padding: 8px 10px; }
        }
        @media (max-width: 600px) {
            .hero-stats-bar { padding: 14px 16px; }
            .hero-left { padding: 30px 16px 30px; }
        }
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateX(-10px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-lightBg text-gray-800 dark:bg-darkBg dark:text-gray-100 min-h-screen flex flex-col">

<nav class="sticky top-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-sm px-4 py-3">
    <div class="max-w-4xl mx-auto flex justify-between items-center">
        
        <h1 class="flex items-center gap-1 text-xl font-bold text-primary tracking-tighter">
            <img src="ChatGPT Image May 23, 2026, 10_01_59 AM.png" 
                 alt="QrMenu Logo" 
                 style="display: block; height: 50px; width: 100px; object-fit: contain;">
            <span>QR<span class="text-gray-800 dark:text-white">Menu</span></span>
        </h1>

        <div class="flex items-center gap-3">
            <button onclick="toggleSearch()" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition transform hover:scale-110">
                <i class="fas fa-search"></i>
            </button>
            
            <select id="langSwitch" onchange="changeLanguage()" class="bg-transparent border border-gray-300 dark:border-gray-600 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="en" class="text-amber-500">EN</option>
                <option value="am" class="text-amber-500">አማ</option>
            </select>

            <button onclick="toggleTheme()" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition transform hover:scale-110">
                <i id="themeIcon" class="fas fa-moon"></i>
            </button>
        </div>
    </div>

    <div id="searchContainer" class="hidden max-w-4xl mx-auto mt-3 px-2">
        <div class="relative">
            <input type="text" id="searchInput" onkeyup="filterMenu()" placeholder="Search food..." 
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary focus:outline-none transition-all">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>
</nav>

    <!-- ================= HERO SECTION ================= -->
    <header id="heroSection">
    <section class="hero-section">

        <!-- HERO LEFT -->
        <div class="hero-left">
            <div class="hero-tagline">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.2L22 12l-7.6 2.8L12 22l-2.4-7.2L2 12l7.6-2.8z"/></svg>
                Smart Dining. Just a <span class="scan-word">Scan</span> Away.
            </div>

            <h1 class="hero-title-main" data-i18n="heroTitle">
                Delicious Food,<br>
                <span class="instantly">Instantly</span> on Your Phone.
            </h1>

            <p class="hero-subtitle-text" data-i18n="heroSubtitle">
                Scan the QR code, explore our menu, place your order and enjoy your favorite food.
            </p>

            <div class="hero-btns">
                <button onclick="scrollToMenu()" class="hero-btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="3" height="3"/><path d="M21 14v3h-3"/><path d="M21 21h-3"/></svg>
                    <span data-i18n="viewMenuBtn">View Menu</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </button>
            </div>

            <div class="hero-customers">
                <div class="hero-avatars">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face" alt="Customer 1">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&h=80&fit=crop&crop=face" alt="Customer 2">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=80&h=80&fit=crop&crop=face" alt="Customer 3">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&h=80&fit=crop&crop=face" alt="Customer 4">
                </div>
                <div>
                    <div style="font-weight:700;font-size:0.9rem;color:#1a1a2e;">500+ Happy Customers</div>
                    <div style="display:flex;gap:2px;margin-top:2px;">
                        <?php for($i=0;$i<5;$i++): ?>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#ffb800"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>


            <div class="hero-stats-bar">
                <div class="hero-stat-item">
                    <div class="hero-stat-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div><div class="hero-stat-num">500+</div><div class="hero-stat-lbl">Happy Customers</div></div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon orange">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 010 8h-1"/><path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z"/><path d="M6 1v3"/><path d="M10 1v3"/><path d="M14 1v3"/></svg>
                    </div>
                    <div><div class="hero-stat-num">120+</div><div class="hero-stat-lbl">Delicious Dishes</div></div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div><div class="hero-stat-num">5-10 min</div><div class="hero-stat-lbl">Average Delivery</div></div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-icon purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                    </div>
                    <div><div class="hero-stat-num">4.9/5</div><div class="hero-stat-lbl">Customer Rating</div></div>
                </div>
            </div>
        </div>

        <!-- HERO RIGHT -->
        <div class="hero-right">
            <div class="hero-right-bg"></div>

            <!-- Floating Leaves -->
            <div class="hero-leaf hl-1"><svg width="30" height="30" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg></div>
            <div class="hero-leaf hl-2"><svg width="24" height="24" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg></div>
            <div class="hero-leaf hl-3"><svg width="20" height="20" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg></div>
            <div class="hero-leaf hl-4"><svg width="16" height="16" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg></div>
            <div class="hero-leaf hl-5"><svg width="28" height="28" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg></div>
            <div class="hero-leaf hl-6"><svg width="22" height="22" viewBox="0 0 24 24"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg></div>

            <!-- QR Card -->
            <div class="hero-qr-card">
                <img src="https://image.qwenlm.ai/public_source/8a2020f7-268c-4e2b-8f2f-500225bd57d0/107f9da37-460b-4858-8cc5-1fbf4f87a6a6.png" alt="QR Code" onerror="this.style.display='none'">
                <p><span>Scan</span> to view menu</p>
            </div>

            <!-- Phone Mockup -->
            <div class="hero-phone-mockup">
                <div class="hero-phone-screen">
                    <div class="hero-phone-notch"></div>
                    <div class="hero-phone-header">
                        <div class="hero-phone-logo">
                            <div class="hero-phone-logo-icon">🐼</div>
                            <span class="hero-phone-logo-text">QR Menu<Menu></Menu></span>
                        </div>
                        <div class="hero-phone-welcome">
                            <h3>Welcome Back! 👋</h3>
                            <p>What would you like to eat?</p>
                        </div>
                    </div>
                    <div class="hero-phone-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        Search your favorite food...
                    </div>
                    <div class="hero-phone-popular">
                        <h4>Popular Dishes</h4>
                        <div class="hero-phone-dish">
                            <img src="https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=100&h=100&fit=crop" class="hero-phone-dish-img" alt="Chicken">
                            <div class="hero-phone-dish-info">
                                <div class="hero-phone-dish-name">Spicy Grilled Chicken</div>
                                <div class="hero-phone-dish-rating"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg> 4.8</div>
                                <div class="hero-phone-dish-price">120 ETB</div>
                            </div>
                            <div class="hero-phone-add-btn">+</div>
                        </div>
                        <div class="hero-phone-dish">
                            <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=100&h=100&fit=crop" class="hero-phone-dish-img" alt="Burger">
                            <div class="hero-phone-dish-info">
                                <div class="hero-phone-dish-name">Beef BBQ Burger</div>
                                <div class="hero-phone-dish-rating"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg> 4.7</div>
                                <div class="hero-phone-dish-price">95 ETB</div>
                            </div>
                            <div class="hero-phone-add-btn">+</div>
                        </div>
                        <div class="hero-phone-dish">
                            <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop" class="hero-phone-dish-img" alt="Pizza">
                            <div class="hero-phone-dish-info">
                                <div class="hero-phone-dish-name">Cheesy Pizza</div>
                                <div class="hero-phone-dish-rating"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg> 4.6</div>
                                <div class="hero-phone-dish-price">110 ETB</div>
                            </div>
                            <div class="hero-phone-add-btn">+</div>
                        </div>
                    </div>
                    <div class="hero-phone-nav">
                        <div class="hero-phone-nav-item active">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>Home
                        </div>
                        <div class="hero-phone-nav-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Menu
                        </div>
                        <div class="hero-phone-nav-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/></svg>Orders
                        </div>
                        <div class="hero-phone-nav-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Profile
                        </div>
                    </div>
                </div>
            </div>

            <!-- Food Images -->
            <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=400&fit=crop" class="hero-food-burger" alt="Burger">
            <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=300&fit=crop" class="hero-food-pizza" alt="Pizza">
            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300&h=300&fit=crop" class="hero-food-salad" alt="Salad">

            <!-- Curved Arrow -->
            <svg class="hero-arrow-svg" width="120" height="80" viewBox="0 0 120 80">
                <path d="M10 40 C 30 10, 60 10, 80 30 C 100 50, 100 70, 110 70" stroke="#666" stroke-width="2" fill="none" stroke-dasharray="4,4"/>
                <polygon points="105,65 115,70 108,75" fill="#666"/>
            </svg>
        </div>
    </section>
    </header>

    <!-- ================= MENU SECTION ================= -->
    <main id="menuSection" class="flex-grow max-w-4xl mx-auto w-full px-4 py-8">
        
        <!-- Categories -->
        <div class="flex overflow-x-auto gap-3 mb-8 pb-2 no-scrollbar sticky top-[70px] z-40 bg-lightBg dark:bg-darkBg pt-2" id="categoryContainer">
            <!-- Categories injected by JS -->
        </div>

        <!-- PHP Food Items Loop -->
        <div id="foodGrid" class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4 max-w-4xl mx-auto">
            
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $index => $item): ?>
                    <!-- Added data-category with normalized value for JS filtering -->
                    <?php 
                        $normalizedName = strtolower(trim($item['name']));
                        $normalizedCategory = strtolower(trim(str_replace([' ', '-'], '_', $item['category'])));
                    ?>
                    <div class="food-card bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden flex flex-col" 
                         style="animation-delay: <?php echo $index * 0.05; ?>s"
                         data-name="<?php echo htmlspecialchars($normalizedName); ?>" 
                         data-category="<?php echo htmlspecialchars($normalizedCategory); ?>">
                        
                        <div class="h-48 overflow-hidden relative group">
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="w-full h-full object-cover item-img">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <span class="absolute bottom-2 right-2 bg-primary text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                <?php echo htmlspecialchars($item['price']); ?> ETB
                            </span>
                        </div>
                        
                        <div class="p-4 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-lg mb-1 text-gray-800 dark:text-white">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </h3>   
                            
                                <p class="text-xs text-primary uppercase tracking-wide mb-2 font-semibold">
                                    <?php echo htmlspecialchars($item['category']); ?>
                                </p>
                                <?php if(!empty($item['description'])): ?>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                        <?php echo htmlspecialchars($item['description']); ?>
                                    </p>
        
                                <?php endif; ?>
                            </div>
                            
                            
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-10 animate-fade-in">
                    <i class="fas fa-utensils text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No menu items available yet.</p>
                </div>
            <?php endif; ?>

        </div>

        <!-- Empty State (Shown by JS if search fails) -->
        <div id="emptyState" class="hidden text-center py-10 animate-fade-in">
            <i class="fas fa-cookie-bite text-6xl text-gray-300 mb-4 animate-bounce"></i>
            <p class="text-xl text-gray-500" data-i18n="noItems">No items found.</p>
            <button onclick="clearFilters()" class="mt-4 text-primary hover:underline">Clear filters</button>
        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-auto">
        <div class="max-w-4xl mx-auto px-4 py-6 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; 2026 QR Menu. <br>
                <span data-i18n="footerText">Made by Qr Menu</span>
            </p>
        </div>
    </footer>

    <!-- ================= JAVASCRIPT LOGIC ================= -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        primary: '#09b111', 
                        secondary: '#2EC4B6', 
                        darkBg: '#1a1a1a',
                        lightBg: '#f8f9fa'
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.4s ease-out',
                        'fade-in': 'fadeIn 0.3s ease-out',
                        'slide-in': 'slideIn 0.3s ease-out'
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-10px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' }
                        }
                    }
                }
            }
        }
        // --- TRANSLATIONS ---
        const translations = {
            en: {
                heroTitle: "Delicious Food, Instantly on Your Phone.",
                heroSubtitle: "Scan the QR code, explore our menu, place your order and enjoy your favorite food.",
                viewMenuBtn: "View Menu",
                noItems: "No items found.",
                footerText: "Made by Time Burger.",
                catAll: "All",
                catBurgers: "Burgers",
                catPizzas: "Pizzas", 
                catDesserts: "Desserts",
                catHot: "Hot Drinks",
                catCold: "Cold Drinks",
                addToOrder: "Order"
            },
            am: {
                heroTitle: "ጣፋጭ ምግብ፣ ወዲያውኑ ስልክዎ ላይ።",
                heroSubtitle: "QR ኮዱን ይቃኙ፣ ሜኑን ያስሱ፣ ምርጥ ምግብዎን ይዘዙ።",
                viewMenuBtn: "ሜኑ ይመልከቱ",
                noItems: "ምንም አይተኛ አልተገኘም።",
                footerText: "በታይም በርገር የተሰራ።",
                catAll: "ሁሉም",
                catBurgers: "በርገሮች",
                catPizzas: "ፒዛዎች",
                catDesserts: "ጣፋጭ ምግቦች", 
                catHot: "ሞቃት መጠጦች",
                catCold: "ቀዝቃዛ መጠጦች",
                addToOrder: "እዘዝ"
            }
        };

        let currentLang = 'en';
        let currentCategory = 'all';

        // --- THEME & LANGUAGE ---
        function toggleTheme() {
            document.documentElement.classList.toggle('dark');
            const icon = document.getElementById('themeIcon');
            if (document.documentElement.classList.contains('dark')) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
            // Save preference
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        }

        function changeLanguage() {
            currentLang = document.getElementById('langSwitch').value;
            updateTexts();
            renderCategories();
        }

        function updateTexts() {
            const t = translations[currentLang];
            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (t[key]) el.innerHTML = t[key];
            });
            document.getElementById('searchInput').placeholder = currentLang === 'en' ? "Search burgers, pizza..." : "በርገር፣ ፒዛ ይፈልጉ...";
        }

        // --- CATEGORY RENDERING ---
        function renderCategories() {
            const cats = [
                { id: 'all', label: translations[currentLang].catAll },
                { id: 'burgers', label: translations[currentLang].catBurgers },
                { id: 'pizza', label: translations[currentLang].catPizzas },
                { id: 'desserts', label: translations[currentLang].catDesserts },
                { id: 'hot_drinks', label: translations[currentLang].catHot },
                { id: 'cold_drinks', label: translations[currentLang].catCold }
            ];

            const container = document.getElementById('categoryContainer');
            container.innerHTML = cats.map((cat, index) => `
                <button onclick="setCategory('${cat.id}')" 
                    class="category-btn whitespace-nowrap px-5 py-2 rounded-full text-sm font-semibold transition shadow-sm
                    ${currentCategory === cat.id 
                        ? 'bg-primary text-white shadow-primary/30' 
                        : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'}"
                    style="animation-delay: ${index * 0.05}s">
                    ${cat.label}
                </button>
            `).join('');
        }

        function setCategory(catId) {
            currentCategory = catId.toLowerCase().trim();
            renderCategories();
            filterMenu();
            
            // Smooth scroll to menu if on mobile
            if (window.innerWidth < 768) {
                document.getElementById('menuSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            setCategory('all');
        }

        // --- SEARCH & FILTER LOGIC (FIXED) ---
        function filterMenu() {
            const searchVal = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.food-card');
            let visibleCount = 0;

            cards.forEach((card, index) => {
                const name = (card.getAttribute('data-name') || '').toLowerCase().trim();
                const category = (card.getAttribute('data-category') || '').toLowerCase().trim();
                
                // Normalize currentCategory for comparison
                const normalizedCurrentCat = currentCategory.toLowerCase().trim();
                
                // Check matches
                const matchesSearch = searchVal === '' || name.includes(searchVal);
                const matchesCategory = normalizedCurrentCat === 'all' || 
                                       category === normalizedCurrentCat ||
                                       category.replace(/_/g, '') === normalizedCurrentCat.replace(/_/g, '');

                if (matchesSearch && matchesCategory) {
                    card.classList.remove('hidden-item');
                    card.style.animation = `fade-in-up 0.4s ease-out ${index * 0.03}s both`;
                    visibleCount++;
                } else {
                    card.classList.add('hidden-item');
                    card.style.animation = 'none';
                }
            });

            // Toggle Empty State
            const emptyState = document.getElementById('emptyState');
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        function scrollToMenu() {
            document.getElementById('menuSection').scrollIntoView({ behavior: 'smooth' });
        }

        function toggleSearch() {
            const container = document.getElementById('searchContainer');
            const wasHidden = container.classList.contains('hidden');
            container.classList.toggle('hidden');
            
            if (!container.classList.contains('hidden')) {
                document.getElementById('searchInput').focus();
                // Add slide-down animation
                container.style.animation = 'slideIn 0.3s ease-out';
            }
        }

        
        // --- INITIALIZATION ---
        window.addEventListener('DOMContentLoaded', () => {
            // Load saved theme
            if (localStorage.getItem('theme') === 'dark' || 
                (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.getElementById('themeIcon').classList.replace('fa-moon', 'fa-sun');
            }
            
            updateTexts();
            renderCategories();
            
            // Add entrance animation to hero
            document.querySelector('#heroSection .relative').classList.add('fade-in');
        });

        // Close search when clicking outside on mobile
        document.addEventListener('click', (e) => {
            const searchContainer = document.getElementById('searchContainer');
            const searchBtn = e.target.closest('button[onclick="toggleSearch()"]');
            
            if (!searchBtn && !searchContainer.contains(e.target) && !searchContainer.classList.contains('hidden')) {
                if (window.innerWidth < 768) {
                    searchContainer.classList.add('hidden');
                }
            }
        });
    </script>
</body>
</html>
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
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
        
        @keyframes fadeInUp {
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
        
        <h1 class="flex items-center gap-2 text-xl font-bold text-primary tracking-tighter">
            <img src="photo_2026-03-29_14-22-48-removebg-preview.png" 
                 alt="ShegaKitchen Logo" 
                 style="height: 40px; width: auto; object-fit: contain;">
            <span>Shega<span class="text-gray-800 dark:text-white">Kitchen</span></span>
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
    <header id="heroSection" class="relative w-full h-[60vh] flex items-center justify-center overflow-hidden bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="relative z-10 text-center px-4">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-2 drop-shadow-lg" data-i18n="heroTitle">Fast Menu, <br> Fast Food.</h2>
            <p class="text-gray-200 text-lg mb-6" data-i18n="heroSubtitle">Scan, Order, Enjoy.</p>
            <button onclick="scrollToMenu()" class="bg-primary hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-full shadow-lg transform transition hover:scale-105 active:scale-95">
                <span data-i18n="viewMenuBtn">View Menu</span> <i class="fas fa-arrow-down ml-2 animate-bounce"></i>
            </button>
        </div>
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
                <span data-i18n="footerText">Made by Shega Kitchen</span>
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
                        primary: '#FF9F1C', 
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
                heroTitle: "Fast Menu, <br> Fast Food.",
                heroSubtitle: "Scan, Order, Enjoy.",
                viewMenuBtn: "View Menu",
                noItems: "No items found.",
                footerText: "Made by Shega Kitchen",
                catAll: "All",
                catBurgers: "Burgers",
                catPizzas: "Pizzas", 
                catDesserts: "Desserts",
                catHot: "Hot Drinks",
                catCold: "Cold Drinks",
                addToOrder: "Order"
            },
            am: {
                heroTitle: "ፈጣን ዝርዝር,<br>ፈጣን ምግብ።",
                heroSubtitle: "ያንብቡ፣ ያዙ፣ ይውሰዱ።",
                viewMenuBtn: "ሜኑ ይመልከቱ",
                noItems: "ምንም አይተኛ አልተገኘም።",
                footerText: "በሸጋ ኩሽና የተሰራ።",
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
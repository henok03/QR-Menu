<?php
include 'db.php';

// Fetch all categories or items from your database
$stmt = $pdo->query("SELECT * FROM menu_items WHERE is_available = 1 ORDER BY id DESC");
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gold: #d4af37; --dark: #1a1a1a; --white: #ffffff; }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
        
        body { background-color: #f8f9fa; color: var(--dark); }

        /* Hero Header */
        .header {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            height: 250px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .header h1 { font-family: 'Playfair Display', serif; font-size: 32px; letter-spacing: 2px; }
        .header p { font-size: 14px; opacity: 0.9; margin-top: 5px; }

        /* Menu Container */
        .menu-container { padding: 20px; max-width: 800px; margin: -40px auto 0; }

        .item-card {
            background: var(--white);
            border-radius: 20px;
            display: flex;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .item-image { width: 35%; min-width: 120px; position: relative; }
        .item-image img { width: 100%; height: 100%; object-fit: cover; }

        .item-details { width: 65%; padding: 15px; display: flex; flex-direction: column; justify-content: center; }
        .item-details h3 { font-size: 18px; color: var(--dark); margin-bottom: 5px; }
        .item-details .description { font-size: 12px; color: #777; margin-bottom: 10px; line-height: 1.4; }
        
        .price-tag { 
            font-weight: 700; 
            color: var(--gold); 
            font-size: 18px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Order Button (Optional for now) */
        .order-btn {
            background: var(--dark);
            color: white;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            text-decoration: none;
        }

        /* Floating QR Info */
        .footer-info { text-align: center; padding: 40px 20px; color: #999; font-size: 12px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>PREMIUM CAFE</h1>
        <p><i class="fas fa-map-marker-alt"></i> Debre Birhan, Ethiopia</p>
    </div>

    <div class="menu-container">
        <?php foreach ($items as $item): ?>
            <div class="item-card">
                <div class="item-image">
                    <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['name']; ?>">
                </div>
                <div class="item-details">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>
                    <div class="price-tag">
                        <span><?php echo $item['price']; ?> ETB</span>
                        <a href="#" class="order-btn">View</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="footer-info">
        <p>Scan for digital menu & ordering</p>
        <p>&copy; 2026 Powered by YourDevCompany</p>
    </div>

</body>
</html>
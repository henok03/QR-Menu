<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) { 
    header("Location: admin.php"); 
    exit(); 
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) { 
    echo "Item not found."; 
    exit(); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $desc = $_POST['description'];
    
    $image_path = $item['image_path']; 

    // FIXED: List is now at the top so PHP sees it!
    $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['image']['name'];
        $file_tmp_name = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_exts)) {
            if ($file_size < 5000000) {
                $new_file_name = uniqid('img_', true) . '.' . $file_ext;
                $upload_dir = 'uploads/';

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($file_tmp_name, $upload_dir . $new_file_name)) {
                    // Delete old image file to save space
                    if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                        unlink($item['image_path']);
                    }
                    $image_path = $upload_dir . $new_file_name;
                }
            }
        }
    }

    $sql = "UPDATE menu_items SET name=?, price=?, category=?, description=?, image_path=? WHERE id=?";
    $pdo->prepare($sql)->execute([$name, $price, $category, $desc, $image_path, $id]);

    header("Location: admin.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #0f1115;
            --card-bg: #1c1f26;
            --accent: #ff4757;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --border: rgba(255, 255, 255, 0.05);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }

        body { 
            background-color: var(--bg); 
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .edit-card { 
            background: var(--card-bg); 
            padding: 30px; 
            border-radius: 24px; 
            border: 1px solid var(--border);
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        h2 { margin-bottom: 20px; font-size: 24px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        h2 i { color: var(--accent); }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-size: 14px; color: var(--text-secondary); }

        input, select, textarea { 
            width: 100%; 
            padding: 14px; 
            border-radius: 12px; 
            border: 1px solid var(--border); 
            background: var(--bg); 
            color: white; 
            outline: none; 
            font-size: 16px;
            transition: border-color 0.2s;
        }

        input:focus, select:focus, textarea:focus { border-color: var(--accent); }
        textarea { resize: vertical; min-height: 100px; }

        .btn-group { display: flex; flex-direction: column; gap: 10px; margin-top: 30px; }

        .save-btn { 
            background: var(--accent); 
            color: white; 
            border: none; 
            padding: 16px; 
            border-radius: 12px; 
            font-weight: 600; 
            font-size: 16px; 
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .save-btn:hover { opacity: 0.9; }

        .cancel-btn { 
            text-align: center; 
            text-decoration: none; 
            color: var(--text-secondary); 
            font-size: 14px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="edit-card">
        <h2><i class="fas fa-edit"></i> Edit Item</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <option value="Burgers" <?php if($item['category'] == 'Burgers') echo 'selected'; ?>>Burgers</option>
                    <option value="Pizza" <?php if($item['category'] == 'Pizza') echo 'selected'; ?>>Pizza</option>
                    <option value="Drinks" <?php if($item['category'] == 'Drinks') echo 'selected'; ?>>Drinks</option>
                    <option value="Hot Drinks" <?php if($item['category'] == 'Hot Drinks') echo 'selected'; ?>>Hot Drinks</option>
                    <option value="Main Course" <?php if($item['category'] == 'Main Course') echo 'selected'; ?>>Main Course</option>
                </select>
            </div>

            <div class="form-group">
                <label>Price (ETB)</label>
                <input type="number" name="price" value="<?php echo $item['price']; ?>" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
<div class="form-group">
    <label>Current Image</label>
    <?php if (!empty($item['image_path'])): ?>
        <img src="<?php echo $item['image_path']; ?>" style="width: 120px; border-radius: 12px; margin-bottom: 10px; border: 1px solid var(--border);">
    <?php else: ?>
        <p style="font-size: 12px; color: var(--text-secondary);">No image uploaded.</p>
    <?php endif; ?>
    
    <label>Change Image</label>
    <input type="file" name="image" accept="image/png, image/jpeg, image/jpg, image/gif, image/webp">
    <p style="font-size: 12px; color: var(--text-secondary); margin-top: 5px;">Leave blank to keep the current image.</p>
</div>
            <div class="btn-group">
                <button type="submit" class="save-btn">Update Details</button>
                <a href="admin.php" class="cancel-btn">Back to Dashboard</a>
            </div>
        </form>
    </div>
    
</body>
</html>
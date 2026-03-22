<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['item_name'];
    $category = $_POST['category']; // Capture the new category field
    $price    = $_POST['item_price'];
    $desc     = $_POST['item_desc'] ?? ''; 

    $target_dir = "uploads/";
    
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = time() . "_" . basename($_FILES["food_image"]["name"]);
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["food_image"]["tmp_name"], $target_file)) {
        try {
            // Updated SQL to include the 'category' column
            $sql = "INSERT INTO menu_items (name, category, price, description, image_path) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            // Execute with all 5 values
            $stmt->execute([$name, $category, $price, $desc, $target_file]);
            
            echo "Success! Item added with image. <a href='admin.php'>Back to Dashboard</a>";
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }
    } else {
        echo "Error: Failed to upload image. Check folder permissions.";
    }
}
?>
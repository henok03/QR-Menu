<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['item_name'];
    $price = $_POST['item_price'];
    $desc = $_POST['item_desc'];

    // 1. Handle Image Upload
    $target_dir = "uploads/";
    
    // Create the 'uploads' folder if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Give the image a unique name using time() so files don't overwrite each other
    $image_name = time() . "_" . basename($_FILES["food_image"]["name"]);
    $target_file = $target_dir . $image_name;

    // Check if the file is actually uploaded
    if (move_uploaded_file($_FILES["food_image"]["tmp_name"], $target_file)) {
        
        try {
            // 2. Insert into Database (Including the image_path)
            $sql = "INSERT INTO menu_items (name, price, description, image_path) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            
            // We save the $target_file string (e.g., "uploads/171111000_burger.jpg")
            $stmt->execute([$name, $price, $desc, $target_file]);
            
            echo "Success! Item added with image. <a href='admin.php'>Back to Dashboard</a>";
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
        }

    } else {
        echo "Error: Failed to upload the image file. Make sure the 'uploads' folder is writable.";
    }
}
?>
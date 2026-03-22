<?php
session_start();
include 'db.php';

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Optional: Delete the actual image file from the folder first
        $stmt = $pdo->prepare("SELECT image_path FROM menu_items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        
        if ($item && file_exists($item['image_path'])) {
            unlink($item['image_path']); // This removes the file from /uploads/
        }

        // Delete from database
        $sql = "DELETE FROM menu_items WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        header("Location: admin.php?deleted=1");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: admin.php");
}
?>
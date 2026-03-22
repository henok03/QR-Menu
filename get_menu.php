<?php
include 'db.php';

$stmt = $pdo->query("SELECT * FROM menu_items");
$menu = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($menu);
?>
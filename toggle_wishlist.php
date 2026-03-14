<?php
session_start();
include("config/db.php");

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if($product_id <= 0){
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID.']);
    exit;
}

// Check if product already in wishlist
$stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    // Remove from wishlist
    $row = $result->fetch_assoc();
    $delete_stmt = $conn->prepare("DELETE FROM wishlist WHERE id = ?");
    $delete_stmt->bind_param("i", $row['id']);
    $delete_stmt->execute();
    echo json_encode(['status' => 'removed']);
} else {
    // Add to wishlist
    $insert_stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $insert_stmt->bind_param("ii", $user_id, $product_id);
    if($insert_stmt->execute()){
        echo json_encode(['status' => 'added']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add to wishlist.']);
    }
}
?>
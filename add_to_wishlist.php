<?php
session_start();
include("config/db.php");

// Only logged-in users can add to wishlist
if(!isset($_SESSION['user_id'])){
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get product ID from POST
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if($product_id <= 0){
    echo json_encode(['status' => 'error', 'message' => 'Invalid product.']);
    exit;
}

// Check if the product is already in the wishlist
$stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    echo json_encode(['status' => 'error', 'message' => 'Product already in wishlist.']);
    exit;
}

// Insert into wishlist
$stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $product_id);

if($stmt->execute()){
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Could not add to wishlist.']);
}
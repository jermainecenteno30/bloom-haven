<?php
session_start();
include("config/db.php");

if(!isset($_SESSION['user_id'])){
    die(json_encode(['status'=>'error','message'=>'Login required']));
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity   = intval($_POST['quantity']) ?: 1;

// Check if item is already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    // Update quantity
    $row = $result->fetch_assoc();
    $newQty = $row['quantity'] + $quantity;
    $update = $conn->prepare("UPDATE cart_items SET quantity=? WHERE id=?");
    $update->bind_param("ii", $newQty, $row['id']);
    $update->execute();
} else {
    // Insert new cart item
    $insert = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?,?,?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();
}

echo json_encode(['status'=>'success']);
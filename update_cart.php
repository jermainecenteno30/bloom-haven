<?php
session_start();
include("config/db.php");

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['status'=>'error','message'=>'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = intval($_POST['cart_id']);
$change = intval($_POST['change']);

// Get current quantity
$result = $conn->query("SELECT quantity, product_id FROM cart_items WHERE id=$cart_id AND user_id=$user_id");
if($result->num_rows === 0){
    echo json_encode(['status'=>'error','message'=>'Item not found']);
    exit;
}
$row = $result->fetch_assoc();
$new_qty = $row['quantity'] + $change;

// Remove or update quantity
if($new_qty <= 0){
    $conn->query("DELETE FROM cart_items WHERE id=$cart_id AND user_id=$user_id");
} else {
    $conn->query("UPDATE cart_items SET quantity=$new_qty WHERE id=$cart_id AND user_id=$user_id");
}

// Recalculate subtotal and total
$subtotal = $row['quantity'] + $change > 0 ? $row['quantity'] + $change : 0;
$price = $conn->query("SELECT price FROM products WHERE id=".$row['product_id'])->fetch_assoc()['price'];
$subtotal = $price * max($new_qty, 0);

$total_result = $conn->query("SELECT SUM(c.quantity*p.price) AS total FROM cart_items c JOIN products p ON c.product_id=p.id WHERE c.user_id=$user_id");
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'] ?? 0;

echo json_encode([
    'status'=>'success',
    'quantity'=>$new_qty,
    'subtotal'=>$subtotal,
    'total'=>$total
]);
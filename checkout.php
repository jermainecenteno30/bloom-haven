<?php
session_start();
include("config/db.php");

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    die("Please login to checkout.");
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $full_name = $conn->real_escape_string($_POST['full_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);

    // Fetch cart items for the user
    $cart_result = $conn->query("SELECT c.product_id, c.quantity, p.price 
                                 FROM cart_items c
                                 JOIN products p ON c.product_id = p.id
                                 WHERE c.user_id = $user_id");

    if($cart_result->num_rows > 0){
        $total = 0;
        $items = [];

        while($row = $cart_result->fetch_assoc()){
            $items[] = $row;
            $total += $row['price'] * $row['quantity'];
        }

        // Insert into orders table
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, created_at) VALUES (?, ?, 'pending', NOW())");
        $stmt->bind_param("id", $user_id, $total);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Insert order items
        $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach($items as $item){
            $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_item->execute();
        }

        // Clear cart
        $conn->query("DELETE FROM cart_items WHERE user_id = $user_id");

        $message = "Order placed successfully! Your Order ID is: $order_id";
    } else {
        $message = "Your cart is empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - Bloom Haven</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Checkout</h2>

<?php if($message != ""): ?>
    <p><?php echo $message; ?></p>
<?php else: ?>
<form method="post" action="">
<input type="text" name="full_name" placeholder="Full Name" required><br><br>
<input type="text" name="address" placeholder="Delivery Address" required><br><br>

<select name="payment_method" required>
<option value="Credit Card">Credit Card</option>
<option value="PayPal">PayPal</option>
<option value="Cash on Delivery">Cash on Delivery</option>
</select>

<br><br>

<button type="submit">Place Order</button>
</form>
<?php endif; ?>

</body>
</html>
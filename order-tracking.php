<?php
// Include database connection
include("config/db.php");

$status = "";
$order_id = "";

// Check if user submitted an order ID
if(isset($_POST['order_id'])){
    $order_id = intval($_POST['order_id']);
    
    // Prepared statement for security
    $stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $status = $row['status'];
    } else {
        $status = "Order not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Tracking - Bloom Haven</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<h2>Track Order</h2>

<form method="post" action="">
    <input type="text" name="order_id" placeholder="Enter Order ID" value="<?php echo htmlspecialchars($order_id); ?>" required>
    <button type="submit">Track</button>
</form>

<?php if($status != ""): ?>
    <p>Status: <?php echo htmlspecialchars($status); ?></p>
<?php endif; ?>

</body>
</html>
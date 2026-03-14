<?php
session_start();
include("config/db.php");

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    die("Please login to view your cart.");
}

$user_id = $_SESSION['user_id'];
$total = 0;

// Fetch cart items from database
$result = $conn->query("
    SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.image, c.quantity
    FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

$cart_items = [];
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $cart_items[] = $row;
        $total += $row['price'] * $row['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cart - Bloom Haven</title>
<link rel="stylesheet" href="css/style.css?v=1.0">
<style>
.product-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    padding: 20px;
}
.product {
    border: 1px solid #ddd;
    padding: 20px;
    text-align: center;
    background: #fff;
    border-radius: 10px;
}
.product img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}
.quantity-controls {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}
.quantity-controls button {
    padding: 5px 10px;
    border: none;
    background: #ff7aa8;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
}
.quantity-controls button:hover {
    background: #e35b8c;
}
</style>
</head>
<body>

<header>
<h1>Bloom Haven</h1>
<nav>
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="cart.php">Cart</a>
</nav>
</header>

<h2>Your Shopping Cart</h2>

<div class="product-grid" id="cartItems">
<?php
if(!empty($cart_items)){
    foreach($cart_items as $item){
        $cart_id = $item['cart_id'];
        $name  = addslashes($item['name']);
        $price = number_format($item['price'], 2);
        $image = !empty($item['image']) ? $item['image'] : 'default.jpg';
        $quantity = intval($item['quantity']);
        $subtotal = number_format($item['price'] * $quantity, 2);

        echo "
        <div class='product' data-cartid='$cart_id'>
            <img src='images/$image' alt='$name'>
            <h3>$name</h3>
            <p>Price: $$price</p>
            <div class='quantity-controls'>
                <button onclick='updateQuantity($cart_id, -1)'>−</button>
                <span id='qty-$cart_id'>$quantity</span>
                <button onclick='updateQuantity($cart_id, 1)'>+</button>
            </div>
            <p>Subtotal: $<span id='subtotal-$cart_id'>$subtotal</span></p>
        </div>
        ";
    }
} else {
    echo "<p style='text-align:center;'>Your cart is empty.</p>";
}
?>
</div>

<h3 style="text-align:center;">Total: $<span id="totalAmount"><?php echo number_format($total,2); ?></span></h3>
<div style="text-align:center; margin:20px;">
    <a href="checkout.php" class="btn">Proceed to Checkout</a>
</div>

<script>
// Update quantity via AJAX
function updateQuantity(cartId, change){
    fetch('update_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'cart_id=' + cartId + '&change=' + change
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success'){
            if(data.quantity <= 0){
                // Remove product from DOM
                let el = document.querySelector('.product[data-cartid="'+cartId+'"]');
                el.remove();
            } else {
                // Update quantity and subtotal
                document.getElementById('qty-'+cartId).innerText = data.quantity;
                document.getElementById('subtotal-'+cartId).innerText = parseFloat(data.subtotal).toFixed(2);
            }
            // Update total
            document.getElementById('totalAmount').innerText = parseFloat(data.total).toFixed(2);
        } else {
            alert(data.message);
        }
    });
}
</script>

</body>
</html>
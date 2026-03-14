<?php
session_start();
include("config/db.php");

// Fetch latest 9 products for 3x3 grid
$result = $conn->query("SELECT * FROM products ORDER BY id ASC LIMIT 9");

// Count items in cart
$cart_count = 0;
$user_wishlist = [];
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    
    // Cart count
    $cart_result = $conn->query("SELECT SUM(quantity) AS total_qty FROM cart_items WHERE user_id = $user_id");
    $cart_row = $cart_result->fetch_assoc();
    $cart_count = $cart_row['total_qty'] ?? 0;

    // Fetch wishlist products
    $wishlist_result = $conn->query("SELECT product_id FROM wishlist WHERE user_id = $user_id");
    if($wishlist_result){
        while($row = $wishlist_result->fetch_assoc()){
            $user_wishlist[] = $row['product_id'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bloom Haven</title>
<link rel="stylesheet" href="css/style.css?v=1.0">
</head>
<body>

<header>
<h1>Bloom Haven</h1>
<nav>
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="wishlist.php">Wishlist</a>
    <a href="cart.php">Cart<?php if($cart_count>0) echo " ($cart_count)"; ?></a>
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
    <?php endif; ?>
</nav>
</header>

<section class="hero">
<h2>Fresh Flowers for Every Occasion</h2>
<p>Beautiful bouquets delivered with love.</p>
<a href="shop.php" class="btn">Shop Now</a>
</section>

<section class="products">
<h2>Best Sellers</h2>
<div class="product-grid">
<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $id    = $row['id'];
        $name  = addslashes($row['name']);
        $price = $row['price'];
        $image = !empty($row['image']) ? $row['image'] : 'default.jpg';
        $heart_active = in_array($id, $user_wishlist) ? 'active' : '';

        echo "
        <div class='product'>
            <img src='images/$image' alt='$name'>
            <h3>".$row['name']."</h3>
            <p>$$price</p>";

        if(isset($_SESSION['user_id'])){
            echo "
            <button onclick=\"confirmAddToCart($id)\">Add to Cart</button>
            <button class='heart-btn' onclick='toggleWishlist($id, this)'>
                <span class='heart $heart_active'>&#10084;</span>
            </button>";
        } else {
            echo "<a href='login.php' class='btn'>Login to Buy</a>";
        }

        echo "</div>";
    }
} else {
    echo "<p>No products available.</p>";
}
?>
</div>
</section>

<footer>
<p>© 2026 Bloom Haven</p>
</footer>

<script src="js/cart.js"></script>
<script src="js/wishlist.js"></script>
<script>
// Confirmation before adding to cart
function confirmAddToCart(productId){
    let add = confirm("Do you want to add this item to your cart?");
    if(add){
        addToCartDB(productId);
    }
}

// Add to Cart via database
function addToCartDB(productId){
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success'){
            alert('Added to cart!');
            location.reload(); // refresh cart count in header
        } else {
            alert(data.message);
        }
    });
}

// Toggle wishlist (add/remove) and keep heart red
function toggleWishlist(productId, btn){
    fetch('toggle_wishlist.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(res => res.json())
    .then(data => {
        let heart = btn.querySelector('.heart');
        if(data.status === 'added'){
            heart.classList.add('active');
        } else if(data.status === 'removed'){
            heart.classList.remove('active');
        } else {
            alert(data.message);
        }
    });
}
</script>

</body>
</html>
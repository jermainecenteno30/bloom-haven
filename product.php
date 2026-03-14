<?php
// Include database connection
include("config/db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Shop - Bloom Haven</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
<h1>Bloom Haven Shop</h1>
<nav>
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="wishlist.php">Wishlist</a>
    <a href="cart.php">Cart</a>
</nav>
</header>

<section class="products">
<h2>Flower Bouquets</h2>
<div class="product-grid">
<?php
$result = $conn->query("SELECT * FROM products");

if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $id    = intval($row['id']);
        $name  = addslashes($row['name']);        // safe for JS
        $price = $row['price'];                   // keep numeric
        $image = !empty($row['image']) ? $row['image'] : 'default.jpg'; // fallback

        echo "
        <div class='product'>
            <a href='product_detail.php?id=$id'>
                <img src='images/$image' alt='$name'>
                <h3>".$row['name']."</h3>
            </a>
            <p>$$price</p>
            <button onclick=\"addToCart('$name', $price)\">Add to Cart</button>
        </div>
        ";
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
</body>
</html>
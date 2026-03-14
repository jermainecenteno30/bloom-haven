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
<link rel="stylesheet" href="css/shop.css">
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

<h2>Flower Bouquets</h2>

<div class="shop-grid">

<?php
$result = $conn->query("SELECT * FROM products");
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $name = addslashes($row['name']); // safe for JS
        $price = $row['price'];
        $image = !empty($row['image']) ? $row['image'] : 'default.jpg'; // keep original filename

        echo "
        <div class='product'>
            <img src='images/$image' alt='$name'>
            <h3>$name</h3>
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

<script src="js/cart.js"></script>
</body>
</html>
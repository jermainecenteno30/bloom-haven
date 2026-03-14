<?php
session_start();
include("config/db.php");

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    die("Please login to view your wishlist.");
}

$user_id = $_SESSION['user_id'];

// Handle remove from wishlist
if(isset($_GET['remove'])){
    $wishlist_id = intval($_GET['remove']);
    $conn->query("DELETE FROM wishlist WHERE id = $wishlist_id AND user_id = $user_id");
}

// Fetch wishlist items
$result = $conn->query("
    SELECT w.id AS wishlist_id, p.id AS product_id, p.name, p.price, p.image
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE w.user_id = $user_id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Wishlist - Bloom Haven</title>
<link rel="stylesheet" href="css/style.css?v=1.0">
</head>
<body>

<header>
<h1>Bloom Haven</h1>
<nav>
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="wishlist.php">Wishlist</a>
    <a href="cart.php">Cart</a>
</nav>
</header>

<h2>Your Wishlist</h2>

<div class="product-grid">
<?php
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $name  = addslashes($row['name']); // safe for JS if needed
        $price = $row['price'];
        $image = !empty($row['image']) ? $row['image'] : 'default.jpg';

        echo "
        <div class='product'>
            <img src='images/$image' alt='$name'>
            <h3>".$row['name']."</h3>
            <p>Price: $$price</p>
            <a href='wishlist.php?remove=".$row['wishlist_id']."' class='btn'>Remove</a>
        </div>
        ";
    }
} else {
    echo "<p style='text-align:center;'>You haven't added any flowers to your wishlist yet.</p>";
}
?>
</div>

<footer>
<p>© 2026 Bloom Haven</p>
</footer>

</body>
</html>
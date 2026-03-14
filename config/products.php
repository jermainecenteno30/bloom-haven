<?php
include("config/db.php");

$result = $conn->query("SELECT * FROM products");

while ($row = $result->fetch_assoc()) {
    echo "
    <div class='product'>
        <img src='images/".$row['image_url']."' alt='".$row['name']."'>
        <h3>".$row['name']."</h3>
        <p>$".$row['price']."</p>
        <button>Add to Cart</button>
    </div>
    ";
}
?>
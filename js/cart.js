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
            location.reload(); // optional: reload to update cart count in header
        } else {
            alert(data.message);
        }
    });
}
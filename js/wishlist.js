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
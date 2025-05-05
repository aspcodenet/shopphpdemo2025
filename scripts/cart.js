function addToCart(productId) {
    // Simulate adding to cart
    console.log(`Product ${productId} added to cart`);

    fetch(`/api/addToCart?productId=${productId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Network response was not ok');
        }
    }).then(data => {
        console.log('Product added to cart:', data);
        document.getElementById('cartCount').innerText =  data.cartCount
        console.log(data.bestTeam)
        // Optionally update the UI or show a success message
    }).catch(error => {
        console.error('There was a problem with the fetch operation:', error);
        // Optionally show an error message to the user
    })
}


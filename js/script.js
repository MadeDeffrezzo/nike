
function openModal() {
    document.getElementById("modal").style.display = "block";
}

function closeModal() {
    document.getElementById("modal").style.display = "none";
}

function updateQuantity(productId, oldSize, newQuantity) {
    fetch('/functions/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update_quantity&product_id=${productId}&size=${oldSize}&quantity=${newQuantity}`
    }).then(() => location.reload());
}

function updateSize(productId, oldSize, newSize) {
    fetch('/functions/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update_size&product_id=${productId}&old_size=${oldSize}&new_size=${newSize}`
    }).then(() => location.reload());
}
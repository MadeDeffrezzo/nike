document.querySelectorAll('.quantity-btn').forEach(button => {
    button.addEventListener('click', function() {
        const input = this.parentElement.querySelector('.quantity-input');
        let value = parseInt(input.value);
        
        if (this.dataset.action === 'increase') {
            input.value = value + 1;
        } else if (this.dataset.action === 'decrease' && value > 1) {
            input.value = value - 1;
        }
    });
});
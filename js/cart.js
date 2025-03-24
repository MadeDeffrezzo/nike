// cart.js
async function addToCart(productId, productName, productPrice, productImage, quantity, size, discount = 0) {
    try {
        // Получаем количество из поля ввода, если не передано напрямую
        const quantityInput = document.getElementById(`quantity-${productId}`);
        quantity = quantity || (quantityInput ? parseInt(quantityInput.value) : 1);

        // Валидация входных данных
        if (!productId || !productName || !productPrice || !productImage) {
            throw new Error('Не хватает данных о товаре');
        }
        if (quantity < 1) {
            throw new Error('Количество должно быть больше 0');
        }
        if (!size) {
            throw new Error('Выберите размер');
        }

        // Подготовка данных для отправки
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('id', productId); // Оставил 'id' для совместимости с твоим cart.php
        formData.append('product_id', productId); // Добавил 'product_id' для явности
        formData.append('name', productName);
        formData.append('price', productPrice);
        formData.append('image', productImage);
        formData.append('quantity', quantity);
        formData.append('size', size);
        formData.append('discount', discount);

        // Отправка запроса через Fetch API
        const response = await fetch('/functions/cart.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Ошибка сервера: ${response.status}`);
        }

        const result = await response.text();
        alert(result); // Показываем ответ от сервера
        // Можно перенаправить в профиль, если нужно
        // window.location.href = "/pages/profile.php";
    } catch (error) {
        alert(error.message || 'Ошибка при добавлении товара в корзину');
        console.error('Add to cart error:', error);
    }
}



async function updateQuantity(productId, size, quantity, price, oldPrice) {
    try {
        // Валидация
        quantity = parseInt(quantity);
        if (quantity < 1) {
            throw new Error('Количество должно быть больше 0');
        }

        // Обновляем цены на основной странице
        const priceWithDiscountElement = document.getElementById(`price-with-discount-${productId}`);
        const priceWithoutDiscountElement = document.getElementById(`price-without-discount-${productId}`);
        
        const totalPriceWithDiscount = (price * quantity).toFixed(2).replace('.', ',');
        const totalPriceWithoutDiscount = (oldPrice * quantity).toFixed(2).replace('.', ',');
        
        priceWithDiscountElement.textContent = `${totalPriceWithDiscount}₽ со скидкой`;
        priceWithoutDiscountElement.textContent = `${totalPriceWithoutDiscount}₽ без скидки`;

        // Отправляем запрос на сервер для обновления количества в базе данных
        const formData = new FormData();
        formData.append('action', 'update_quantity');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('size', size);

        const response = await fetch('/functions/cart.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Ошибка сервера: ${response.status}`);
        }

        // Обновляем модальное окно и общие итоги
        await updateModalItem(productId, size, quantity, price);
        await updateCartTotals();

        showNotification('Количество обновлено');
    } catch (error) {
        showNotification(error.message || 'Ошибка при обновлении количества', true);
        console.error('Update quantity error:', error);
    }
}

// Функция для обновления конкретного товара в модальном окне
async function updateModalItem(productId, size, quantity, price) {
    const itemTotalElement = document.getElementById(`order-item-total-${productId}-${size}`);
    if (itemTotalElement) {
        const totalPrice = (price * quantity).toFixed(2).replace('.', ',');
        itemTotalElement.textContent = totalPrice;

        // Обновляем количество в тексте элемента
        const itemElement = document.getElementById(`order-item-${productId}-${size}`);
        const textParts = itemElement.textContent.split(' x ');
        const nameAndSize = textParts[0];
        itemElement.textContent = `${nameAndSize} x ${quantity} = ${totalPrice}₽`;
    }
}

async function removeFromCart(productId, size, elementId) {
    try {
        // Подготовка данных для отправки
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('product_id', productId);
        formData.append('size', size);

        // Отправка запроса на сервер
        const response = await fetch('/functions/cart.php', {
            method: 'POST', // Используем POST вместо GET для единообразия
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Ошибка сервера: ${response.status}`);
        }

        const result = await response.text();
        // Удаляем элемент из DOM, если удаление прошло успешно
        const itemElement = document.getElementById(elementId);
        if (itemElement) {
            itemElement.remove();
        }

        // Обновляем итоговые значения корзины
        updateCartTotals();

        // Показываем уведомление
        showNotification('Товар удален из корзины');
    } catch (error) {
        showNotification(error.message || 'Ошибка при удалении товара', true);
        console.error('Remove from cart error:', error);
    }
}

// Функция для обновления итоговых значений (добавлена для полноты)
async function updateCartTotals() {
    try {
        const response = await fetch('/functions/get_cart_totals.php');
        if (!response.ok) throw new Error('Ошибка при получении итогов');
        const totals = await response.json();
        
        // Обновляем итоги на основной странице
        document.getElementById('total-quantity').textContent = totals.quantity;
        document.getElementById('total-price').textContent = totals.priceWithDiscount.toFixed(2).replace('.', ',') + '₽';
        document.getElementById('total-discount').textContent = totals.discount.toFixed(2).replace('.', ',') + '₽';
        
        // Обновляем итоговую сумму в модальном окне
        const orderTotalElement = document.getElementById('order-total');
        if (orderTotalElement) {
            orderTotalElement.textContent = totals.priceWithDiscount.toFixed(2).replace('.', ',');
        }
    } catch (error) {
        console.error('Update totals error:', error);
    }
}

// Уведомление (добавлено для удобства)
function showNotification(message, isError = false) {
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px; padding: 10px 20px;
        background: ${isError ? '#ffcccc' : '#ccffcc'}; border-radius: 5px;
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function openModalAndValidate() {
    const modal = document.getElementById('modal');
    modal.style.display = 'block';
}

document.getElementById('order-form').addEventListener('submit', function(event) {
    const deliveryAddress = document.getElementById('delivery-address').value.trim();

    // Валидация адреса
    if (!validateAddress(deliveryAddress)) {
        event.preventDefault(); // Останавливаем отправку формы
        return;
    }
});

function validateAddress(address) {
    // Проверка на пустое поле
    if (!address) {
        showNotification('Пожалуйста, введите адрес доставки', true);
        document.getElementById('delivery-address').focus();
        return false;
    }

    // Проверка минимальной длины (10 символов)
    if (address.length < 10) {
        showNotification('Адрес должен содержать не менее 10 символов', true);
        document.getElementById('delivery-address').focus();
        return false;
    }

    // Проверка на наличие букв и цифр
    const hasLetters = /[a-zA-Zа-яА-Я]/.test(address);
    const hasNumbers = /\d/.test(address);
    if (!hasLetters || !hasNumbers) {
        showNotification('Адрес должен содержать буквы и цифры (например, ул. Ленина 5)', true);
        document.getElementById('delivery-address').focus();
        return false;
    }

    return true; // Адрес прошел валидацию
}

function showNotification(message, isError = false) {
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; padding: 10px 20px;
        background: ${isError ? '#ffcccc' : '#ccffcc'}; border-radius: 5px;
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function changeQuantity(productId, size, change, price, oldPrice) {
    const input = document.getElementById(`quantity-${productId}`);
    let newQuantity = parseInt(input.value) + change;
    if (newQuantity < 1) newQuantity = 1; // Не даем количеству стать меньше 1
    input.value = newQuantity;
    updateQuantity(productId, size, newQuantity, price, oldPrice);
}
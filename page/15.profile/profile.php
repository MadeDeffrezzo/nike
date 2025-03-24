<?php

include_once '../../functions/header.php';
// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    die("Пожалуйста, войдите в систему, чтобы увидеть корзину.");
}


$user_id = (int)$_SESSION['user_id'];

$totalQuantity = 0;
$totalSumWithDiscount = 0;
$totalSumWithoutDiscount = 0;
$totalDiscount = 0;

$query = "SELECT fullname, email, role, avatar FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $query);
if ($user_result === false) {
    die("Ошибка в запросе к базе данных (users): " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($user_result);

// Загружаем корзину пользователя из базы данных
$query = "SELECT c.product_id, c.quantity, c.size, p.name, p.price, p.old_price, p.image_url 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = $user_id";
$result = mysqli_query($conn, $query);
if ($result === false) {
    die("Ошибка в запросе к базе данных (cart): " . mysqli_error($conn));
}

$cart = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cart[] = [
        'id' => $row['product_id'],
        'name' => $row['name'],
        'image_url' => $row['image_url'],
        'price' => $row['price'],
        'old_price' => $row['old_price'],
        'quantity' => $row['quantity'],
        'size' => $row['size']
    ];
}

// Подсчитываем итоговые суммы
foreach ($cart as $product) {
    $discount = $product['old_price'] - $product['price'];
    $totalQuantity += $product['quantity'];
    $totalSumWithoutDiscount += $product['old_price'] * $product['quantity'];
    $totalDiscount += $discount * $product['quantity'];
    $totalSumWithDiscount += $product['price'] * $product['quantity'];
}

// Отображение уведомления
if (isset($_SESSION['notification'])) {
    $notification = $_SESSION['notification'];
    echo "<div style='padding: 10px; margin: 10px; border-radius: 5px;";
    if ($notification['type'] === 'success') {
        echo " background-color: #d4edda; color: #155724;'>";
    } else {
        echo " background-color: #f8d7da; color: #721c24;'>";
    }
    echo htmlspecialchars($notification['message']) . "</div>";
    unset($_SESSION['notification']); // Удаляем уведомление после показа
}

$user_id = (int)$_SESSION['user_id'];

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header_footer.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="profile.css">
    <title>Товар</title>
</head>

<body>

    <main>
        <section>

            <div class="boxmain">
            <div class="profile-container">
                    <img class="avatar" src="<?php echo $user['avatar'] ? '/img/avatars/' . $user['avatar'] : '/img/img-av.png'; ?>" alt="Аватар">
                    <p class="pz"><?php echo htmlspecialchars($user['role']); ?></p>
                    <p class="pz"><?php echo htmlspecialchars($user['fullname']); ?></p>
                    <p class="pz"><?php echo htmlspecialchars($user['email']); ?></p>
                    <a href="/index.php"><p class="menu-item">Главная</p></a>
                    <a href="/page/17.orders/orders.php"><p class="menu-item">Мои заказы</p></a>
                    <a href="/page/15.profile/profile.php"><p class="menu-item vib">Корзина</p></a>
                    <a href="/page/18.settings/settings.php"><p class="menu-item">Настройки</p></a>
                 </div>
                    <div class="cont-zak">

                        <form id="order-form" method="POST" action="/functions/submit_order.php">
    <div class="cont-ofor">
        <div class="item-cont">
            <?php if (empty($cart)): ?>
                <p>Корзина пуста</p>
            <?php else: ?>
                <?php foreach ($cart as $index => $product): ?>
    <div class="item-zak" id="cart-item-<?php echo $product['id']; ?>-<?php echo $product['size']; ?>">
        <div class="img-zak">
            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>" width="100px">
            <div class="item-nazv">
                <p><?php echo $product['name']; ?></p>
                <button onclick="removeFromCart(<?php echo $product['id']; ?>, '<?php echo $product['size']; ?>', 'cart-item-<?php echo $product['id']; ?>-<?php echo $product['size']; ?>')">
                    <img src="/img/trash.png" alt="trash" width="20px">
                </button>
            </div>
            </div>
            <div class="item-price">
                <?php
                $priceWithDiscount = $product['price'];
                $totalPriceWithDiscount = $priceWithDiscount * $product['quantity'];
                $totalPriceWithoutDiscount = $product['old_price'] * $product['quantity'];
                ?>
                <p id="price-with-discount-<?php echo $product['id']; ?>"><?php echo number_format($totalPriceWithDiscount, 2, ',', ' '); ?>₽ со скидкой</p>
                <p id="price-without-discount-<?php echo $product['id']; ?>"><?php echo number_format($totalPriceWithoutDiscount, 2, ',', ' '); ?>₽ без скидки</p>
            </div>
            <div class="item-kolvo">
    <div class="quantity-control">
        <button type="button" class="quantity-btn minus" onclick="changeQuantity(<?php echo $product['id']; ?>, '<?php echo $product['size']; ?>', -1, <?php echo $product['price']; ?>, <?php echo $product['old_price']; ?>)">−</button>
        <input type="number" id="quantity-<?php echo $product['id']; ?>" 
               name="quantity[<?php echo $product['id']; ?>]" 
               value="<?php echo $product['quantity']; ?>" 
               min="1" 
               onchange="updateQuantity(<?php echo $product['id']; ?>, '<?php echo $product['size']; ?>', this.value, <?php echo $product['price']; ?>, <?php echo $product['old_price']; ?>)">
        <button type="button" class="quantity-btn plus" onclick="changeQuantity(<?php echo $product['id']; ?>, '<?php echo $product['size']; ?>', 1, <?php echo $product['price']; ?>, <?php echo $product['old_price']; ?>)">+</button>
    </div>
</div>
                            <div class="razmer">
                                <h3>Размер</h3>
                                <div class="radio-input">
                                    <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size): ?>
                                        <label>
                                            <input value="<?php echo $size; ?>" 
                                                   name="size[<?php echo $product['id']; ?>]" 
                                                   type="radio" 
                                                   <?php echo ($product['size'] === $size) ? 'checked' : ''; ?> 
                                                   onchange="updateSize(<?php echo $product['id']; ?>, '<?php echo $product['size']; ?>', this.value)">
                                            <span><?php echo $size; ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <input type="hidden" name="product_id[]" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="price[<?php echo $product['id']; ?>]" value="<?php echo $product['price']; ?>">

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="oform">
            <div class="oform-header">
                            <h1>Перейти к оформлению</h1>
            </div>

            <div class="totalprice">
            <h4>Ваша корзина</h4>
            <p>Товары (<span id="total-quantity"><?php echo $totalQuantity; ?></span>) 
               (<span id="total-price"><?php echo number_format($totalSumWithDiscount, 2, ',', ' '); ?>₽</span>)</p>
            <p>Скидка: <span id="total-discount"><?php echo number_format($totalDiscount, 2, ',', ' '); ?>₽</span></p>
            </div>
            <div class="oform-footer">
                <div class="btn-oform">
                    <button type="button" onclick="openModalAndValidate()">Оформить заказ</button>        </div>
                </div>
             </div>
        <div id="modal" class="modal" style="display: none;">
        <div class="modal-content">
    <div class="modal-header">
        <h2>Оформление заказа</h2>
        <span class="close" onclick="document.getElementById('modal').style.display='none'">&times;</span>
    </div>
    <div class="modal-body">
        <div class="order-summary">
            <h3>Ваши товары:</h3>
            <div id="order-items" class="order-items-list">
                <?php foreach ($cart as $product): ?>
                    <?php 
                    $priceWithDiscount = $product['price'];
                    $totalPriceWithDiscount = $priceWithDiscount * $product['quantity'];
                    ?>
                    <p id="order-item-<?php echo $product['id']; ?>-<?php echo $product['size']; ?>" class="order-item">
                        <span class="item-name"><?php echo htmlspecialchars($product['name']); ?></span>
                        <span class="item-details">(<?php echo $product['size']; ?>) - <?php echo number_format($priceWithDiscount, 2, ',', ' '); ?>₽ x <?php echo $product['quantity']; ?></span>
                        <span class="item-total" id="order-item-total-<?php echo $product['id']; ?>-<?php echo $product['size']; ?>">
                            <?php echo number_format($totalPriceWithDiscount, 2, ',', ' '); ?>₽
                        </span>
                    </p>
                <?php endforeach; ?>
            </div>
            <div class="order-total">
                <span>Итого:</span>
                <span id="order-total"><?php echo number_format($totalSumWithDiscount, 2, ',', ' '); ?>₽</span>
            </div>
        </div>
        <div class="delivery-section">
            <h3>Адрес доставки</h3>
            <input type="text" id="delivery-address" name="delivery_address" placeholder="Например: ул. Ленина 5" required>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="submit-btn">Подтвердить заказ</button>
    </div>
</div>
</div>
    </div>
</form>
</div>

            </section>
    </main>

    <footer>
        <div class="footer_top">
            <div class="footer_logo">
                <a href="../../index.php">
                    <img src="../../img/pngwing 14.png" alt="">
                </a>
            </div>

            <div class="footer_nav">
                <div class="footer_nbox">
                    <a href="../2.help/help.php">
                        <h3>Помощь</h3>
                    </a>
                    <ul>
                        <li><a href="/page/17.orders/orders.php">Мои заказы</a></li>
                        <li><a href="../7.Delivery/Delivery.php">Условия доставки</a></li>
                        <li><a href="../10.gift_certificates/gift_certificates.php">Подарочные сертификаты</a></li>
                    </ul>
                </div>

                <div class="footer_nbox">
                    <a href="../1.Onas/onas.php">
                        <h3>О нас</h3>
                    </a>

                    <ul>
                        <li><a href="">Журнал</a></li>
                        <li><a href="">Публичная оферта</a></li>
                        <li><a href="../9.job/job.php">Вакансии</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer_bottom">
            <p>© 2023 Nike, Inc. All Rights Reserved</p>
        </div>
    </footer>
        <!-- <script src="/js/cart.js"></script> 
        <script src="/js/modal.js"></script>  -->
        <script src="/js/cart.js"></script> <!-- Убедитесь, что путь правильный -->
</body>

</html>
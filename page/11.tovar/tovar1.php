<?php 
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

include_once '../../functions/header.php';

// Получаем ID товара из URL
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($product_id <= 0) {
    die("Товар не выбран.");
}

// Запрос для получения информации о конкретном товаре
$query = "SELECT * FROM Products WHERE id = $product_id";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    echo "Товар не найден.";
    $product = null;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header_footer.css">
    <link rel="stylesheet" href="tover.css">
    <title><?php echo $product ? htmlspecialchars($product['name']) : 'Товар'; ?></title>
</head>
<body>
    <main>
        <div class="mxw1400">
            <?php if ($product): ?>
                <div class="tovar_contener">
                    <div class="tovar_box">
                        <div class="tovar_name">
                            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                        </div>
                        <div class="img_tovar">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                    </div>
                    <div class="tovar_box1">
                        <div class="name">
                            <h2><?php echo htmlspecialchars($product['brand']); ?></h2>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                        </div>
                        <div class="cena">
                            <h4><?php echo number_format($product['price'], 2, ',', ' '); ?>₽</h4>
                            <p><?php echo number_format($product['old_price'], 2, ',', ' '); ?>₽</p>
                        </div>
                        <div class="razmer">
                            <h3>Размер</h3>
                            <select id="size-<?php echo $product['id']; ?>" class="size-select">
                                <?php foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size): ?>
                                    <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="quantity">
                            <h3>Введите количество</h3>
                                <div class="quantity-control">
                                    <button class="quantity-btn minus" data-action="decrease">-</button>
                                    <input type="number" id="quantity-<?php echo $product['id']; ?>" value="1" min="1" class="quantity-input">
                                    <button class="quantity-btn plus" data-action="increase">+</button>
                                </div>
                        </div>
                        <div class="button">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button onclick="addToCart(
                                    <?php echo $product['id']; ?>, 
                                    '<?php echo addslashes($product['name']); ?>', 
                                    <?php echo $product['price']; ?>, 
                                    '<?php echo addslashes($product['image_url']); ?>', 
                                    document.getElementById('quantity-<?php echo $product['id']; ?>').value, 
                                    document.getElementById('size-<?php echo $product['id']; ?>').value
                                )">В корзину</button>
                            <?php else: ?>
                                <a href="/page/14.auth/auth.php" class="btn-tvr">Войдите, чтобы добавить</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <h1 class="da">Характеристики</h1>
                <div class="tovar_specifications">
                    <div class="speif">
                        <div class="info_box">
                            <span>Состав . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . </span>
                            <p><?php echo htmlspecialchars($product['composition']); ?></p>
                        </div>
                        <div class="info_box">
                            <span>Размер товара на модели . . . . . . . . . . . . . . . . . </span>
                            <p><?php echo htmlspecialchars($product['model_size']); ?></p>
                        </div>
                        <div class="info_box">
                            <span>Параметры модели . . . . . . . . . . . . . . . . . . . . . .</span>
                            <p><?php echo htmlspecialchars($product['model_parameters']); ?></p>
                        </div>
                        <div class="info_box">
                            <span>Рост модели на фото . . . . . . . . . . . . . . . . . . . . .</span>
                            <p><?php echo htmlspecialchars($product['model_height']); ?> см</p>
                        </div>
                        <div class="info_box">
                            <span>Длина . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .</span>
                            <p><?php echo htmlspecialchars($product['length']); ?> см</p>
                        </div>
                        <div class="info_box">
                            <span>Длина рукава . . . . . . . . . . . . . . . . . . . . . . . . . . . </span>
                            <p><?php echo htmlspecialchars($product['sleeve_length']); ?> см</p>
                        </div>
                        <div class="info_box">
                            <span>Сезон  . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .</span>
                            <p><?php echo htmlspecialchars($product['season']); ?></p>
                        </div>
                        <div class="info_box">
                            <span>Артикул . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . </span>
                            <p><?php echo htmlspecialchars($product['article']); ?></p>
                        </div>
                    </div>
                    <img class="img-tvr" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="">
                </div>
            <?php else: ?>
                <p>Товар не найден.</p>
            <?php endif; ?>
        </div>
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
                    <a href="../2.help/help.php"><h3>Помощь</h3></a>
                    <ul>
                        <li><a href="">Мои заказы</a></li>
                        <li><a href="../7.Delivery/Delivery.php">Условия доставки</a></li>
                        <li><a href="../10.gift_certificates/gift_certificates.php">Подарочные сертификаты</a></li>
                    </ul>
                </div>
                <div class="footer_nbox">
                    <a href="../1.Onas/onas.php"><h3>О нас</h3></a>
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
    <script src="/js/cart.js"></script>
    <script src="/js/btn.js"></script>
</body>
</html>
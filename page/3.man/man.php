<?php 
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

include_once '../../functions/header.php';
// Запрос для получения информации о детских товарах
$category = 'Мужское'; // Устанавливаем категорию
$query = "SELECT * FROM Products WHERE category = '$category'";
$result = $conn->query($query);
// Проверка результата запроса
if ($result->num_rows > 0) {
    // Получаем данные всех товаров
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    echo "Товары не найдены.";
}
// Закрытие соединения
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header_footer.css">
    <link rel="stylesheet" href="man.css">
    <link rel="stylesheet" href="../../css/cr.css">
    <title>Мужская одежда</title>
    <style>
        .product_box {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .product_box:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

    <main>
       <section id="prodict">
        <div class="nav_block">
            <h2>Мужское</h2>
        </div>
        <div class="mxw1400">
            <div class="product_content">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product_box" onclick="window.location.href='/page/11.tovar/tovar1.php?product_id=<?php echo $product['id']; ?>'">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="cena">
                                <h4><?php echo number_format($product['price'], 2, ',', ' '); ?>₽</h4>
                                <p><?php echo number_format($product['old_price'], 2, ',', ' '); ?>₽</p>
                                <span><?php echo $product['discount']; ?>%</span>
                            </div>
                            <!-- Убраны поля количества и размера, так как добавление в корзину будет на странице товара -->
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Товары не найдены.</p>
                <?php endif; ?>
            </div>
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
                        <li><a href="">Мои заказы</a></li>
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
</body>
</html>
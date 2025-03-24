<?php
include_once 'functions/header.php';
include_once 'functions/header_bottom.php';

$query = "SELECT * FROM Products ORDER BY RAND() LIMIT 4";
        $result = mysqli_query($conn, $query);
        $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Главная</title>
</head>

<body>

    <main>
        <section id="nav">
            <div class="mxw1400">
                <div class="contener_nav">

                    <a href="page/3.man/man.php">
                        <div class="nav_box">
                            <img src="img/image 1.png" alt="">
                            <button>Мужчинам</button>
                        </div>
                    </a>

                    <a href="page/4.women/women.php">
                        <div class="nav_box">
                            <img src="img/image 2.png" alt="">
                            <button>Женщинам</button>
                        </div>
                    </a>
                    
                    <a href="page/5.kids/kids.php">
                        <div class="nav_box">
                            <img src="img/image 3.png" alt="">
                            <button>Детям</button>
                        </div>
                    </a>
                </div>

                <div class="contener_text">
                    <div class="text_box">
                        <img src="img/pngwing 9.png" alt="" class="img1">
                        <h3>Доставка на следующий день</h3>
                        <p>В Москве и 60 других крупных городах России вы получитесвой заказ уже на следующий день!Более подробную информацию об условиях доставки в ваш город можно найти здесь.</p>
                    </div>

                    <div class="text_box">
                        <img src="img/pngwing 8.png" alt="" class="img2">
                        <h3>Примерка перед покупкой</h3>
                        <p>Интернет-магазин Nike даёт возможность примерить одежду, обувь и другие товары перед оплатой заказа курьеру. Оплачивайте только то, что вам подошло и понравилось!</p>
                    </div>

                    <div class="text_box">
                        <img src="img/pngwing 10.png" alt="" class="img3">
                        <h3>Удобные способы оплаты</h3>
                        <p>Вы можете оплатить покупки не только наличными, но и банковской картой. У всех курьеров Nike при себе есть терминал для оплаты картами.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="Popular">
           
        <div class="mxw1400">
    <h2>Популярное</h2>
    <div class="product_content">
        <?php
        if (!empty($products)) {
            foreach ($products as $product) {
        ?>
                <a href="/page/11.tovar/tovar1.php?product_id=<?php echo $product['id']; ?>">
                    <div class="product_box">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="cena">
                            <h4><?php echo number_format($product['price'], 2, ',', ' '); ?>₽</h4>
                            <p><?php echo number_format($product['old_price'], 2, ',', ' '); ?>₽</p>
                            <span><?php echo $product['discount']; ?>%</span>
                        </div>
                    </div>
                </a>
        <?php
            }
        } else {
            echo "<p>Товары не найдены.</p>";
        }
        ?>
    </div>
</div>
        </section>

        <section id="poster">
            <img src="img/image 14.png" alt="" class="poster">
        </section>
    </main>
    <script src="/js/cart.js"></script> <!-- Убедитесь, что путь правильный -->
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
</body>

</html>
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Пожалуйста, войдите в систему, чтобы увидеть ваши заказы.");
}

include_once '../../functions/header.php';

$user_id = (int)$_SESSION['user_id'];

$query = "SELECT fullname, email, role, avatar FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $query);
if ($user_result === false) {
    die("Ошибка в запросе к базе данных (users): " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($user_result);

$query = "SELECT id, address, products, total_amount, order_status, created_at 
          FROM orders 
          WHERE user_id = $user_id 
          ORDER BY created_at DESC";
$orders_result = mysqli_query($conn, $query);
if ($orders_result === false) {
    die("Ошибка в запросе к базе данных (orders): " . mysqli_error($conn));
}

$orders = [];
while ($row = mysqli_fetch_assoc($orders_result)) {
    $orders[] = $row;
}

mysqli_close($conn);

// Функция для форматирования списка товаров
function formatProducts($productsJson) {
    $products = json_decode($productsJson, true);
    if (!$products) return "Товары не указаны";
    
    $formatted = [];
    foreach ($products as $product) {
        $formatted[] = sprintf(
            "%s, %s x%d - %s₽",
            htmlspecialchars($product['name']),
            htmlspecialchars($product['size']),
            $product['quantity'],
            number_format($product['price'], 2, ',', ' ')
        );
    }
    return implode("; ", $formatted);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header_footer.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="orders.css">
    <title>Мои заказы</title>
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
                    <a href="/page/17.orders/orders.php"><p class="menu-item vib">Мои заказы</p></a>
                    <a href="/page/15.profile/profile.php"><p class="menu-item">Корзина</p></a>
                    <a href="/page/18.settings/settings.php"><p class="menu-item">Настройки</p></a>
                 </div>
                <div class="cont-zak">
                    <div class="orders-container">
                        <h1>Мои заказы</h1>
                        <?php if (empty($orders)): ?>
                            <p>У вас пока нет заказов.</p>
                        <?php else: ?>
                            <table class="orders-table">
                                <thead>
                                    <tr>
                                        <th>Номер заказа</th>
                                        <th>Адрес доставки</th>
                                        <th>Товары</th>
                                        <th>Сумма</th>
                                        <th>Статус</th>
                                        <th>Дата</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo htmlspecialchars($order['address']); ?></td>
                                            <td><?php echo formatProducts($order['products']); ?></td>
                                            <td><?php echo number_format($order['total_amount'], 2, ',', ' '); ?>₽</td>
                                            <td class="status-<?php echo $order['order_status']; ?>">
                                                <?php 
                                                switch ($order['order_status']) {
                                                    case 'pending': echo 'В обработке'; break;
                                                    case 'completed': echo 'Завершен'; break;
                                                    case 'cancelled': echo 'Отменен'; break;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
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
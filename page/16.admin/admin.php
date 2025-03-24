<?php
include_once '../../functions/header.php';

// Проверка роли администратора
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /page/14.auth/auth.php");
    exit;
}

// Обработка изменения статуса заказа только при нажатии кнопки "Обновить"
if (isset($_POST['update_status'])) {
    if (isset($_POST['order_id']) && isset($_POST['new_status'])) {
        $order_id = (int)$_POST['order_id'];
        $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
        
        $valid_statuses = ['pending', 'completed', 'cancelled'];
        if (in_array($new_status, $valid_statuses)) {
            $update_query = "UPDATE orders SET order_status = '$new_status' WHERE id = $order_id";
            
            if (mysqli_query($conn, $update_query)) {
                if (mysqli_affected_rows($conn) > 0) {
                    echo "<p style='color: green;'>Статус заказа #$order_id успешно обновлён на '$new_status'.</p>";
                } else {
                    echo "<p style='color: orange;'>Запрос выполнен, но ничего не обновлено.</p>";
                }
            } else {
                echo "<p style='color: red;'>Ошибка обновления статуса: " . mysqli_error($conn) . "</p>";
            }
        } else {
            echo "<p style='color: red;'>Недопустимый статус: $new_status</p>";
        }
    } else {
        echo "<p style='color: red;'>Не переданы order_id или new_status.</p>";
    }
}

// Получение 5 последних заказов
$orders_query = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5";
$orders_result = mysqli_query($conn, $orders_query);
if ($orders_result === false) {
    die("Ошибка запроса заказов: " . mysqli_error($conn));
}

// Поиск заказа по ID
$order_search = '';
$order_search_result = null;
if (isset($_POST['search_order_id']) && !empty($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];
    $order_search = "SELECT * FROM orders WHERE id = $order_id";
    $order_search_result = mysqli_query($conn, $order_search);
    if ($order_search_result === false) {
        die("Ошибка поиска заказа: " . mysqli_error($conn));
    }
}

// Получение всех товаров в корзинах
$cart_query = "SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id";
$cart_result = mysqli_query($conn, $cart_query);
if ($cart_result === false) {
    die("Ошибка запроса корзины: " . mysqli_error($conn));
}
function formatProducts($productsJson) {
    $products = json_decode($productsJson, true);
    if (!$products || !is_array($products)) return "Товары не указаны";
    
    $formatted = [];
    foreach ($products as $product) {
        // Форматируем строку без лишних символов
        $formatted[] = htmlspecialchars($product['name']) . " " . 
                      htmlspecialchars($product['size']) . " x" . 
                      $product['quantity'] . " - " . 
                      number_format($product['price'], 2, ',', ' ') . "₽";
    }
    return implode("; ", $formatted);
}


// Обработка удаления товара из products
if (isset($_POST['delete_product'])) {
    $product_id = (int)$_POST['product_id'];
    $delete_query = "DELETE FROM products WHERE id = $product_id";
    
    if (mysqli_query($conn, $delete_query)) {
        if (mysqli_affected_rows($conn) > 0) {
            echo "<p style='color: green;'>Товар успешно удалён из каталога.</p>";
        } else {
            echo "<p style='color: orange;'>Товар не найден.</p>";
        }
    } else {
        echo "<p style='color: red;'>Ошибка удаления товара: " . mysqli_error($conn) . "</p>";
    }
}

// Получение списка товаров
$products_query = "SELECT * FROM products ORDER BY id DESC";
$products_result = mysqli_query($conn, $products_query);
if ($products_result === false) {
    die("Ошибка запроса товаров: " . mysqli_error($conn));
}

?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../../css/header_footer.css">
    <title>Админ-панель</title>
</head>
<body>

    <main>
        <section class="admin-section">
            <h2>Добавить новый товар</h2>
            <form action="../../functions/add_product.php" method="POST" class="add-product-form">
                <div class="form-group">
                    <label>Название: <input type="text" name="name" required></label>
                </div>
                <div class="form-group">
                    <label>Бренд: <input type="text" name="brand" required></label>
                </div>
                <div class="form-group">
                    <label>Описание: <textarea name="description" required></textarea></label>
                </div>
                <div class="form-group">
                    <label>Состав: <input type="text" name="composition"></label>
                </div>
                <div class="form-group">
                    <label>Размер на модели: <input type="text" name="model_size"></label>
                </div>
                <div class="form-group">
                    <label>Параметры модели: <input type="text" name="model_parameters"></label>
                </div>
                <div class="form-group">
                    <label>Рост модели (см): <input type="number" name="model_height"></label>
                </div>
                <div class="form-group">
                    <label>Длина (см): <input type="number" name="length"></label>
                </div>
                <div class="form-group">
                    <label>Длина рукава (см): <input type="number" name="sleeve_length"></label>
                </div>
                <div class="form-group">
                    <label>Сезон: <input type="text" name="season"></label>
                </div>
                <div class="form-group">
                    <label>Артикул: <input type="text" name="article"></label>
                </div>
                <div class="form-group">
                    <label>URL изображения: <input type="text" name="image_url" required></label>
                </div>
                <div class="form-group">
                    <label>Категория: <input type="text" name="category" required></label>
                </div>
                <div class="form-group">
                    <label>Цена: <input type="number" name="price" step="0.01" required></label>
                </div>
                <div class="form-group">
                    <label>Старая цена: <input type="number" name="old_price" step="0.01"></label>
                </div>
                <div class="form-group">
                    <label>Скидка: <input type="number" name="discount" step="0.01"></label>
                </div>
                <button type="submit" class="submit-btn">Добавить товар</button>
            </form>
        </section>
<section class="admin-section">
    <h2>Список товаров</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Бренд</th>
                <th>Категория</th>
                <th>Цена</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = mysqli_fetch_assoc($products_result)): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['brand']); ?></td>
                    <td><?php echo htmlspecialchars($product['category']); ?></td>
                    <td><?php echo number_format($product['price'], 2, ',', ' '); ?>₽</td>
                    <td>
                        <form method="POST" class="delete-form" onsubmit="return confirm('Вы уверены, что хотите удалить этот товар?');">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" name="delete_product" class="update-btn">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</section>

        <section class="admin-section">
            <h2>Последние заказы</h2>
            <form method="POST" class="search-form">
                <label>Поиск по ID заказа: <input type="number" name="order_id"></label>
                <button type="submit" name="search_order_id" class="search-btn">Найти</button>
            </form>

            <?php if ($order_search && mysqli_num_rows($order_search_result) > 0): ?>
                <h3>Результат поиска:</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Пользователь</th>
                            <th>Адрес</th>
                            <th>Товары</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($order_search_result)): ?>
                            <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($order['address']); ?></td>
                        <td><?php echo formatProducts($order['products']); ?></td>
                        <td><?php echo number_format($order['total_amount'], 2, ',', ' '); ?>₽</td>
                        <td><?php echo $order['order_status']; ?></td>
                        <td>
                                    <form method="POST" class="status-form">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="new_status">
                                            <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>В обработке</option>
                                            <option value="completed" <?php echo $order['order_status'] === 'completed' ? 'selected' : ''; ?>>Завершен</option>
                                            <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                                        </select>
                                        <button type="submit" name="update_status" class="update-btn">Обновить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php elseif ($order_search): ?>
                <p class="error">Заказ с таким ID не найден.</p>
            <?php endif; ?>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Адрес</th>
                        <th>Товары</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Перезапрашиваем результат, так как предыдущий уже был использован
                    $orders_result = mysqli_query($conn, $orders_query);
                    while ($order = mysqli_fetch_assoc($orders_result)): 
                    ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo $order['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($order['address']); ?></td>
                            <td><?php echo formatProducts($order['products']); ?></td>
                            <td><?php echo number_format($order['total_amount'], 2, ',', ' '); ?>₽</td>
                            <td><?php echo $order['order_status']; ?></td>
                            <td>
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="new_status">
                                        <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>В обработке</option>
                                        <option value="completed" <?php echo $order['order_status'] === 'completed' ? 'selected' : ''; ?>>Завершен</option>
                                        <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                                    </select>
                                    <button type="submit" name="update_status" class="update-btn">Обновить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            
        </section>

        <section class="admin-section">
            <h2>Товары в корзинах</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Пользователь</th>
                        <th>Товар</th>
                        <th>Количество</th>
                        <th>Цена</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cart = mysqli_fetch_assoc($cart_result)): ?>
                        <tr>
                            <td><?php echo $cart['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($cart['name']); ?></td>
                            <td><?php echo $cart['quantity']; ?></td>
                            <td><?php echo number_format($cart['price'] * $cart['quantity'], 2, ',', ' '); ?>₽</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
<script>
    document.querySelectorAll('.status-form select').forEach(function(select) {
        select.addEventListener('change', function(event) {
            event.preventDefault(); // Предотвращаем любые действия при выборе
        });
    });
</script>
</html>

<?php mysqli_close($conn); ?>
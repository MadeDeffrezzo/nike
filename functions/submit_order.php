<?php
session_start();

$conn = new mysqli("localhost", "root", "", "nike");
if ($conn->connect_error) {
    $_SESSION['error'] = "Ошибка подключения к базе данных: " . $conn->connect_error;
    header("Location: ../../page/15.profile/profile.php");
    exit;
}


if (!isset($_SESSION['user_id'])) {
    header("Location: ../../page/14.auth/auth.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Неверный метод запроса";
    header("Location: ../../page/15.profile/profile.php");
    exit;
}
$address = trim($_POST['delivery_address'] ?? '');
if (empty($address)) {
    $_SESSION['error'] = "Ошибка: Укажите адрес доставки";
    header("Location: ../../page/15.profile/profile.php");
    exit;
}
$address = mysqli_real_escape_string($conn, $address);
$products = [];
$total_amount = 0;

$query = "SELECT c.product_id, c.quantity, c.size, p.name, p.price, p.old_price 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = $user_id";
$result = mysqli_query($conn, $query);
if (!$result) {
    $_SESSION['error'] = "Ошибка запроса к корзине: " . mysqli_error($conn);
    header("Location: ../../page/15.profile/profile.php");
    exit;
}
if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Ошибка: Корзина пуста";
    header("Location: ../../page/15.profile/profile.php");
    exit;
}
while ($row = mysqli_fetch_assoc($result)) {
    $product_id = $row['product_id'];
    $quantity = (int)($_POST['quantity'][$product_id] ?? $row['quantity']);
    
    $size = isset($_POST['size'][$product_id]) ? trim($_POST['size'][$product_id]) : $row['size'];
    if (empty($size)) {
        $_SESSION['error'] = "Ошибка: Размер не указан для товара {$row['name']}";
        header("Location: ../../page/15.profile/profile.php");
        exit;
    }
    $size = mysqli_real_escape_string($conn, $size);
    if ($quantity <= 0) {
        $_SESSION['error'] = "Ошибка: Некорректное количество для товара {$row['name']}";
        header("Location: ../../page/15.profile/profile.php");
        exit;
    }
    $price = (float)$row['price'];
    $total_amount += $price * $quantity;
    $products[] = [
        'id' => $product_id,
        'name' => $row['name'],
        'quantity' => $quantity,
        'size' => $size, 
        'price' => $price,
        'old_price' => (float)$row['old_price']
    ];
}
$products_json = mysqli_real_escape_string($conn, json_encode($products, JSON_UNESCAPED_UNICODE));
$query = "INSERT INTO orders (user_id, address, products, total_amount, order_status) 
          VALUES ($user_id, '$address', '$products_json', $total_amount, 'pending')";
if (mysqli_query($conn, $query)) {
    $clear_query = "DELETE FROM cart WHERE user_id = $user_id";
    if (mysqli_query($conn, $clear_query)) {
        $_SESSION['success'] = "Заказ успешно оформлен! ID: " . mysqli_insert_id($conn);
    } else {
        $_SESSION['error'] = "Ошибка при очистке корзины: " . mysqli_error($conn);
    }
} else {
    $_SESSION['error'] = "Ошибка при сохранении заказа: " . mysqli_error($conn);
}
$delivery_address = trim($_POST['delivery_address']);
if (strlen($delivery_address) < 10 || !preg_match('/[a-zA-Zа-яА-Я]/', $delivery_address) || !preg_match('/\d/', $delivery_address)) {
    die("Ошибка: некорректный адрес доставки");
}
mysqli_close($conn);
header("Location: ../../page/15.profile/profile.php");
exit;
?>
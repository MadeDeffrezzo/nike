<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$user_id = (int)$_SESSION['user_id'];

$totalQuantity = 0;
$totalSumWithDiscount = 0;
$totalDiscount = 0;

$query = "SELECT c.quantity, p.price, p.old_price 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = $user_id";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $totalQuantity += $row['quantity'];
    $totalSumWithDiscount += $row['price'] * $row['quantity'];
    $totalDiscount += ($row['old_price'] - $row['price']) * $row['quantity'];
}

mysqli_close($conn);

header('Content-Type: application/json');
echo json_encode([
    'quantity' => $totalQuantity,
    'priceWithDiscount' => $totalSumWithDiscount,
    'discount' => $totalDiscount
]);
?>
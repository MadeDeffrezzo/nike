<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    echo "Пожалуйста, войдите в систему!";
    exit;
}
$user_id = (int)$_SESSION['user_id'];

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $product_id = (int)($_POST['id'] ?? $_POST['product_id']);
    $quantity = (int)$_POST['quantity'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);

    $query = "SELECT quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$size'";
    $result = mysqli_query($conn, $query);

    if ($result === false) {
        die("Ошибка в запросе: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $new_quantity = $row['quantity'] + $quantity;
        $query = "UPDATE cart SET quantity = $new_quantity WHERE user_id = $user_id AND product_id = $product_id AND size = '$size'";
    } else {
        $query = "INSERT INTO cart (user_id, product_id, quantity, size) VALUES ($user_id, $product_id, $quantity, '$size')";
    }

    if (mysqli_query($conn, $query)) {
        echo "Товар успешно добавлен в корзину!";
    } else {
        echo "Ошибка при добавлении товара: " . mysqli_error($conn);
    }
}
elseif ($action === 'remove') {
    $product_id = (int)$_POST['product_id'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $query = "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$size'";
    if (mysqli_query($conn, $query)) {
        echo "Товар успешно удален из корзины!";
    } else {
        echo "Ошибка при удалении товара: " . mysqli_error($conn);
    }
}
elseif ($action === 'update_quantity') {
    $product_id = (int)$_POST['product_id'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $quantity = (int)$_POST['quantity'];
    if ($quantity > 0) {
        $query = "UPDATE cart SET quantity = $quantity WHERE user_id = $user_id AND product_id = $product_id AND size = '$size'";
        mysqli_query($conn, $query);
    }
}
elseif ($action === 'update_size') {
    $product_id = (int)$_POST['product_id'];
    $old_size = mysqli_real_escape_string($conn, $_POST['old_size']);
    $new_size = mysqli_real_escape_string($conn, $_POST['new_size']);

    $query = "SELECT quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$new_size'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $new_quantity = $row['quantity'];
        $query = "SELECT quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$old_size'";
        $result = mysqli_query($conn, $query);
        $old_quantity = mysqli_fetch_assoc($result)['quantity'];

        $total_quantity = $new_quantity + $old_quantity;
        $query = "UPDATE cart SET quantity = $total_quantity WHERE user_id = $user_id AND product_id = $product_id AND size = '$new_size'";
        mysqli_query($conn, $query);

        $query = "DELETE FROM cart WHERE user_id = $user_id AND product_id = $product_id AND size = '$old_size'";
        mysqli_query($conn, $query);
    } else {
        $query = "UPDATE cart SET size = '$new_size' WHERE user_id = $user_id AND product_id = $product_id AND size = '$old_size'";
        mysqli_query($conn, $query);
    }
}

mysqli_close($conn);
exit;
?>
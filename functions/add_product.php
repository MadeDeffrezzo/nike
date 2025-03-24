<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
    $brand = mysqli_real_escape_string($conn, $_POST['brand'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $composition = mysqli_real_escape_string($conn, $_POST['composition'] ?? '');
    $model_size = mysqli_real_escape_string($conn, $_POST['model_size'] ?? '');
    $model_parameters = mysqli_real_escape_string($conn, $_POST['model_parameters'] ?? '');
    $model_height = isset($_POST['model_height']) ? (int)$_POST['model_height'] : null;
    $length = isset($_POST['length']) ? (int)$_POST['length'] : null;
    $sleeve_length = isset($_POST['sleeve_length']) ? (int)$_POST['sleeve_length'] : null;
    $season = mysqli_real_escape_string($conn, $_POST['season'] ?? '');
    $article = mysqli_real_escape_string($conn, $_POST['article'] ?? '');
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url'] ?? '');
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? '');
    $price = isset($_POST['price']) ? (float)$_POST['price'] : null;
    $old_price = isset($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $discount = isset($_POST['discount']) ? (float)$_POST['discount'] : null;

    if (empty($name) || empty($brand) || empty($description) || empty($image_url) || empty($category) || $price === null || $price <= 0) {
        header("Location: /page/16.admin/admin.php?error=" . urlencode("Все обязательные поля (название, бренд, описание, URL изображения, категория, цена) должны быть заполнены, а цена должна быть положительной"));
        exit;
    }

    if (!in_array($category, ['Мужское', 'Женское', 'Детское'])) {
        header("Location: /page/16.admin/admin.php?error=" . urlencode("Неверная категория. Допустимые значения: Мужское, Женское, Детское"));
        exit;
    }
    $query = "INSERT INTO products (
        name, brand, description, composition, model_size, model_parameters, 
        model_height, length, sleeve_length, season, article, image_url, 
        category, price, old_price, discount
    ) VALUES (
        '$name', '$brand', '$description', '$composition', '$model_size', '$model_parameters', 
        " . ($model_height !== null ? $model_height : 'NULL') . ", 
        " . ($length !== null ? $length : 'NULL') . ", 
        " . ($sleeve_length !== null ? $sleeve_length : 'NULL') . ", 
        '$season', '$article', '$image_url', '$category', 
        " . ($price !== null ? $price : 'NULL') . ", 
        " . ($old_price !== null ? $old_price : 'NULL') . ", 
        " . ($discount !== null ? $discount : 'NULL') . "
    )";


    if (mysqli_query($conn, $query)) {
        header("Location: /page/16.admin/admin.php?success=" . urlencode("Товар успешно добавлен"));
    } else {
        $error = urlencode("Ошибка при добавлении товара: " . mysqli_error($conn));
        header("Location: /page/16.admin/admin.php?error=$error");
    }
}

$conn->close();
exit;
?>
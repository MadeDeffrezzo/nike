<?php
session_start();
$conn = new mysqli("localhost", "root", "", "nike");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Пожалуйста, войдите в систему, чтобы изменить настройки.");
}

include_once '../../functions/header.php';

$user_id = (int)$_SESSION['user_id'];

// Получаем текущие данные пользователя
$query = "SELECT fullname, login, email, role, avatar FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $query);
if ($user_result === false) {
    die("Ошибка в запросе к базе данных (users): " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($user_result);

// Обработка формы при отправке
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Обновление данных пользователя
    $update_query = "UPDATE users SET fullname = '$fullname', login = '$login', email = '$email'";
    
    // Обработка загрузки аватара
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['avatar']['tmp_name'];
        $fileName = $_FILES['avatar']['name'];
        $fileSize = $_FILES['avatar']['size'];
        $fileType = $_FILES['avatar']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedExts = ['jpg', 'jpeg', 'png'];
        if (in_array($fileExtension, $allowedExts) && $fileSize < 5000000) { // Максимум 5MB
            $newFileName = $user_id . '_' . time() . '.' . $fileExtension;
            $uploadPath = '../../img/avatars/' . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                $update_query .= ", avatar = '$newFileName'";
                // Удаляем старый аватар, если он был
                if ($user['avatar'] && file_exists('../../img/avatars/' . $user['avatar'])) {
                    unlink('../../img/avatars/' . $user['avatar']);
                }
            }
        }
    }
    
    $update_query .= " WHERE id = $user_id";
    if (mysqli_query($conn, $update_query)) {
        // Обновляем данные в сессии или перезагружаем страницу
        header("Location: /page/18.settings/settings.php?success=1");
        exit;
    } else {
        $error = "Ошибка при обновлении данных: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header_footer.css">
    <link rel="stylesheet" href="../../css/style.css">

    <link rel="stylesheet" href="settings.css"> <!-- Новый CSS для настроек -->
    <title>Настройки</title>
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
                    <a href="/page/15.profile/profile.php"><p class="menu-item">Корзина</p></a>
                    <a href="/pags/18.settings/settings.php"><p class="menu-item vib">Настройки</p></a>
                 </div>
                <div class="cont-zak">
                    <div class="settings-container">
                        <h1>Настройки профиля</h1>
                        <?php if (isset($_GET['success'])): ?>
                            <p class="success-msg">Настройки успешно обновлены!</p>
                        <?php endif; ?>
                        <?php if (isset($error)): ?>
                            <p class="error-msg"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data" class="settings-form">
                            <div class="form-group">
                                <label for="fullname">Имя</label>
                                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="login">Логин</label>
                                <input type="text" id="login" name="login" value="<?php echo htmlspecialchars($user['login'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="avatar">Аватар</label>
                                <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png">
                                <small>Поддерживаются JPG, PNG, до 5MB</small>
                            </div>
                            <button type="submit" class="save-btn">Сохранить изменения</button>
                        </form>
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
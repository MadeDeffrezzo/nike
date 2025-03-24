<?php
include ("../../functions/header.php");

// Массив для хранения ошибок
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение и очистка данных
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Валидация полного имени
    if (empty($fullname)) {
        $errors['fullname'] = "Полное имя обязательно для заполнения";
    } elseif (!preg_match("/^[а-яА-ЯёЁ\s-]{2,}$/u", $fullname)) {
        $errors['fullname'] = "Имя должно содержать только русские буквы, пробелы или дефис, минимум 2 символа";
    }

    // Валидация email
    if (empty($email)) {
        $errors['email'] = "Email обязателен для заполнения";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Неверный формат email";
    } else {
        $email_check = mysqli_real_escape_string($conn, $email);
        $query = "SELECT id FROM users WHERE email = '$email_check'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $errors['email'] = "Этот email уже зарегистрирован";
        }
    }

    // Валидация логина
    if (empty($login)) {
        $errors['login'] = "Логин обязателен для заполнения";
    } elseif (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $login)) {
        $errors['login'] = "Логин должен содержать только латинские буквы, цифры и _, от 3 до 20 символов";
    } else {
        $login_check = mysqli_real_escape_string($conn, $login);
        $query = "SELECT id FROM users WHERE login = '$login_check'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $errors['login'] = "Этот логин уже занят";
        }
    }

    // Валидация пароля
    if (empty($password)) {
        $errors['password'] = "Пароль обязателен для заполнения";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Пароль должен содержать минимум 6 символов";
    } elseif (!preg_match("/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/", $password)) {
        $errors['password'] = "Пароль должен содержать буквы и цифры";
    }

    // Если ошибок нет, регистрируем пользователя
    if (empty($errors)) {
        $fullname = mysqli_real_escape_string($conn, $fullname);
        $email = mysqli_real_escape_string($conn, $email);
        $login = mysqli_real_escape_string($conn, $login);
        $password = mysqli_real_escape_string($conn, $password);
        
        $query = "INSERT INTO users (fullname, email, login, password) VALUES ('$fullname', '$email', '$login', '$password')";
        if (mysqli_query($conn, $query)) {
            // Получаем ID только что созданного пользователя
            $user_id = mysqli_insert_id($conn);
            // Устанавливаем сессионные переменные
            $_SESSION['user_id'] = $user_id;
            $success = true;
            header("Location: /page/15.profile/profile.php");
            exit; // Завершаем выполнение после перенаправления
        } else {
            $errors['general'] = "Ошибка при регистрации: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header_footer.css">
    <link rel="stylesheet" href="reg1.css">
    <title>Регистрация</title>
</head>
<body>

    <main>
        <div class="reg">
            <form action="" method="POST">
                <h1>Регистрация</h1>

                <?php if (isset($errors['general'])): ?>
                    <p class="error"><?php echo $errors['general']; ?></p>
                <?php endif; ?>

                <div class="lbl">
                    <label for="fullname">Полное имя</label>
                    <input type="text" name="fullname" id="fullname" placeholder="Введите ФИО" 
                           value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
                    <?php if (isset($errors['fullname'])): ?>
                        <span class="error"><?php echo $errors['fullname']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="lbl">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" placeholder="Введите почту" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="lbl">
                    <label for="login">Логин</label>
                    <input type="text" name="login" id="login" placeholder="Введите логин" 
                           value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>" required>
                    <?php if (isset($errors['login'])): ?>
                        <span class="error"><?php echo $errors['login']; ?></span>
                    <?php endif; ?>
                </div>

                <div class="lbl">
                    <label for="password">Пароль</label>
                    <input type="password" name="password" id="password" placeholder="Введите пароль" required>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit">Регистрация</button>
                <p>Уже есть аккаунт? <a href="../14.auth/auth.php">Войти</a></p>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer_top">
            <div class="footer_logo">
                <a href="../../index.html">
                    <img src="../../img/pngwing 14.png" alt="">
                </a>
            </div>
            <div class="footer_nav">
                <div class="footer_nbox">
                    <a href="../2.help/help.html">
                        <h3>Помощь</h3>
                    </a>
                    <ul>
                        <li><a href="">Мои заказы</a></li>
                        <li><a href="../7.Delivery/Delivery.html">Условия доставки</a></li>
                        <li><a href="../10.gift_certificates/gift_certificates.html">Подарочные сертификаты</a></li>
                    </ul>
                </div>
                <div class="footer_nbox">
                    <a href="../1.Onas/onas.html">
                        <h3>О нас</h3>
                    </a>
                    <ul>
                        <li><a href="">Журнал</a></li>
                        <li><a href="">Публичная оферта</a></li>
                        <li><a href="../9.job/job.html">Вакансии</a></li>
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

<?php mysqli_close($conn); ?>